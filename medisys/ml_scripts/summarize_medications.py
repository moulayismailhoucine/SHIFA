"""
Medication History Summarizer
Uses:
  - RxNorm API (NIH, free) for drug enrichment
  - HuggingFace BART for abstractive NLP summarization
  - Fallback to rule-based extraction if transformers unavailable

Usage:
  python summarize_medications.py --medications "Amoxicillin 500mg, Ibuprofen 400mg" \
      --diagnoses "Sinusitis, Fever" --vitals "BP:120/80, HR:88" \
      --patient_name "John Doe" --age 45
"""

import argparse
import json
import sys
import os
import urllib.request
import urllib.parse

os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'


# ────────────────────────────────────────────────────────────────────────────
# 1. RxNorm Drug Lookup  (NIH free API — no key required)
# ────────────────────────────────────────────────────────────────────────────
RXNORM_NAMES = {
    # Common medications → [drug class, purpose, common side effects]
    "amoxicillin": ("Antibiotic (Penicillin)", "Bacterial infections", "Nausea, diarrhea, rash"),
    "ibuprofen": ("NSAID / Analgesic", "Pain, fever, inflammation", "GI upset, ulcers with long-term use"),
    "paracetamol": ("Analgesic / Antipyretic", "Pain and fever relief", "Liver damage in overdose"),
    "acetaminophen": ("Analgesic / Antipyretic", "Pain and fever relief", "Liver damage in overdose"),
    "metformin": ("Biguanide / Antidiabetic", "Type 2 diabetes management", "GI discomfort, lactic acidosis (rare)"),
    "atorvastatin": ("Statin / Lipid-lowering", "High cholesterol", "Muscle pain, liver enzyme elevation"),
    "lisinopril": ("ACE Inhibitor", "Hypertension, heart failure", "Dry cough, hyperkalemia"),
    "omeprazole": ("Proton Pump Inhibitor", "GERD, peptic ulcer", "Headache, diarrhea"),
    "azithromycin": ("Macrolide Antibiotic", "Respiratory & skin infections", "GI upset, QT prolongation"),
    "ciprofloxacin": ("Fluoroquinolone Antibiotic", "Urinary & respiratory infections", "Tendon rupture, CNS effects"),
    "salbutamol": ("Beta-2 Agonist / Bronchodilator", "Asthma, COPD", "Tremor, tachycardia"),
    "prednisone": ("Corticosteroid", "Inflammation, autoimmune disorders", "Immune suppression, weight gain"),
    "amlodipine": ("Calcium Channel Blocker", "Hypertension, angina", "Ankle swelling, headache"),
    "furosemide": ("Loop Diuretic", "Edema, hypertension, heart failure", "Electrolyte imbalance, dehydration"),
    "warfarin": ("Anticoagulant", "Thrombosis prevention", "Bleeding risk, drug interactions"),
    "aspirin": ("Antiplatelet / NSAID", "Platelet aggregation prevention, pain", "GI bleeding, tinnitus"),
    "clopidogrel": ("Antiplatelet", "Stroke / MI prevention", "Bleeding, bruising"),
    "sertraline": ("SSRI Antidepressant", "Depression, anxiety", "Insomnia, sexual dysfunction"),
    "escitalopram": ("SSRI Antidepressant", "Depression, generalized anxiety", "Nausea, somnolence"),
    "insulin": ("Hormone / Antidiabetic", "Diabetes mellitus", "Hypoglycemia, weight gain"),
    "levothyroxine": ("Thyroid Hormone", "Hypothyroidism", "Palpitations, anxiety if overdosed"),
    "doxycycline": ("Tetracycline Antibiotic", "Broad-spectrum bacterial infections", "Photosensitivity, GI upset"),
    "cetirizine": ("Antihistamine", "Allergic rhinitis, urticaria", "Sedation, dry mouth"),
    "losartan": ("ARB / Antihypertensive", "Hypertension, nephropathy", "Dizziness, hyperkalemia"),
    "gabapentin": ("Anticonvulsant / Neuropathic pain", "Epilepsy, nerve pain", "Dizziness, fatigue"),
}


def rxnorm_lookup(drug_name: str) -> dict:
    """Query RxNorm API for drug concept and details."""
    try:
        encoded = urllib.parse.quote(drug_name.strip())
        url = f"https://rxnav.nlm.nih.gov/REST/drugs.json?name={encoded}"
        req = urllib.request.Request(url, headers={"User-Agent": "MedisysAI/1.0"})
        with urllib.request.urlopen(req, timeout=5) as resp:
            data = json.loads(resp.read())
        groups = data.get("drugGroup", {}).get("conceptGroup", [])
        for group in groups:
            concepts = group.get("conceptProperties", [])
            if concepts:
                return {
                    "rxcui": concepts[0].get("rxcui", ""),
                    "name": concepts[0].get("name", drug_name),
                    "synonym": concepts[0].get("synonym", ""),
                }
    except Exception:
        pass
    return {}


def get_drug_info(drug_name: str) -> dict:
    """Get enriched drug info from local dictionary then RxNorm API."""
    clean = drug_name.strip().lower()
    # Strip dosage numbers: "Amoxicillin 500mg" → "amoxicillin"
    base = clean.split()[0].rstrip('mgmcgmliu0123456789')

    local = RXNORM_NAMES.get(base, None)
    rxnorm = rxnorm_lookup(base)

    return {
        "name": drug_name.strip(),
        "rxcui": rxnorm.get("rxcui", "N/A"),
        "drug_class": local[0] if local else "Pharmaceutical Agent",
        "purpose": local[1] if local else "See prescribing information",
        "side_effects": local[2] if local else "Consult pharmacist",
        "rxnorm_name": rxnorm.get("name", drug_name.strip()),
    }


# ────────────────────────────────────────────────────────────────────────────
# 2. NLP Summarization (BART or rule-based fallback)
# ────────────────────────────────────────────────────────────────────────────

def nlp_summarize(raw_text: str) -> str:
    """Try HuggingFace BART, fall back to rule-based if unavailable."""
    try:
        from transformers import pipeline
        summarizer = pipeline(
            "summarization",
            model="facebook/bart-large-cnn",
            tokenizer="facebook/bart-large-cnn",
        )
        # BART max input is ~1024 tokens; truncate if needed
        result = summarizer(
            raw_text[:1000],
            max_length=180,
            min_length=60,
            do_sample=False
        )
        return result[0]["summary_text"]
    except Exception:
        pass  # Fall through to rule-based
    return ""


def rule_based_summary(medications: list, diagnoses: list, drug_infos: list) -> str:
    """Generate a structured clinical summary without ML."""
    lines = []

    if diagnoses:
        lines.append(f"Patient is being managed for: {', '.join(diagnoses)}.")

    if drug_infos:
        classes = list({d["drug_class"] for d in drug_infos})
        lines.append(f"Current regimen includes {len(drug_infos)} medication(s) across {len(classes)} therapeutic class(es): {', '.join(classes)}.")

        for d in drug_infos:
            lines.append(f"• {d['name']} ({d['drug_class']}): used for {d['purpose']}. Monitor for: {d['side_effects']}.")

    lines.append("Regular follow-up and medication adherence monitoring are recommended.")
    return " ".join(lines)


# ────────────────────────────────────────────────────────────────────────────
# 3. Main pipeline
# ────────────────────────────────────────────────────────────────────────────

def summarize(medications_raw: str, diagnoses_raw: str, vitals_raw: str, patient_name: str, age: str) -> dict:
    # Parse inputs
    medications = [m.strip() for m in medications_raw.split(",") if m.strip()]
    diagnoses   = [d.strip() for d in diagnoses_raw.split(",") if d.strip()]

    # Enrich each medication via RxNorm + local dictionary
    drug_infos = [get_drug_info(m) for m in medications]

    # Build a raw clinical text for NLP summarization
    drug_descriptions = []
    for d in drug_infos:
        drug_descriptions.append(
            f"{d['name']} is a {d['drug_class']} used for {d['purpose']}. "
            f"Common side effects include {d['side_effects']}."
        )

    clinical_text = (
        f"Patient: {patient_name}, Age: {age}. "
        f"Diagnoses: {', '.join(diagnoses) if diagnoses else 'Not specified'}. "
        f"Medications: {'; '.join(drug_descriptions)} "
        f"Vitals: {vitals_raw}. "
        "Clinical monitoring and medication adherence are important."
    )

    # Try NLP summarization first, fall back to rule-based
    nlp_result = nlp_summarize(clinical_text)
    summary = nlp_result if nlp_result else rule_based_summary(medications, diagnoses, drug_infos)

    # Interaction warnings (basic poly-pharmacy check)
    warnings = []
    drug_names_lower = [m.lower() for m in medications]
    if any("warfarin" in d for d in drug_names_lower) and any("aspirin" in d for d in drug_names_lower):
        warnings.append("⚠️ Warfarin + Aspirin: Increased bleeding risk — monitor INR closely.")
    if any("nsaid" in d['drug_class'].lower() for d in drug_infos) and any("corticosteroid" in d['drug_class'].lower() for d in drug_infos):
        warnings.append("⚠️ NSAID + Corticosteroid: High GI ulcer risk — consider PPI prophylaxis.")
    if sum(1 for d in drug_infos if "antibiotic" in d['drug_class'].lower()) > 1:
        warnings.append("⚠️ Multiple antibiotics detected — confirm dual-therapy is intentional.")

    return {
        "status": "success",
        "patient_name": patient_name,
        "age": age,
        "diagnoses": diagnoses,
        "medications_count": len(medications),
        "drug_details": drug_infos,
        "summary": summary,
        "interaction_warnings": warnings,
        "vitals_summary": vitals_raw,
        "nlp_method": "BART (HuggingFace)" if nlp_result else "Rule-based NLP",
    }


if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Medication History NLP Summarizer")
    parser.add_argument("--medications", required=True, help="Comma-separated medication list with doses")
    parser.add_argument("--diagnoses",   default="",   help="Comma-separated diagnoses")
    parser.add_argument("--vitals",      default="N/A", help="Vitals string e.g. 'BP:120/80, HR:88'")
    parser.add_argument("--patient_name", default="Patient", help="Patient full name")
    parser.add_argument("--age",          default="N/A",     help="Patient age")
    args = parser.parse_args()

    try:
        result = summarize(
            args.medications,
            args.diagnoses,
            args.vitals,
            args.patient_name,
            args.age,
        )
        print(json.dumps(result, ensure_ascii=False))
        sys.exit(0)
    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}))
        sys.exit(1)
