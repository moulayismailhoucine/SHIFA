"""
MediSys AI REST API
===================
Endpoints:
  POST /api/analyze/skin        - Skin cancer lesion classification
  POST /api/analyze/fracture    - Bone fracture X-ray detection
  POST /api/medication/summarize - Clinical medication NLP summarizer
  GET  /api/health              - Service health check
  GET  /docs                    - Auto-generated Swagger UI (OpenAPI)
"""

from fastapi import FastAPI, File, UploadFile, Form, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import Dict, Any, List, Optional
import numpy as np
from PIL import Image
import io, os, json, sys, subprocess

# ── App Setup ─────────────────────────────────────────────────────────────────
app = FastAPI(
    title="MediSys AI API",
    description=(
        "Clinical AI REST API for the MediSys Hospital Management Platform.\n\n"
        "**Models available:**\n"
        "- 🔬 Skin Cancer Lesion Classifier (MobileNetV2 CNN)\n"
        "- 🦴 Bone Fracture X-Ray Detector (MobileNetV2 CNN)\n"
        "- 🧠 Medication History Summarizer (RxNorm API + Rule-Based NLP)\n"
    ),
    version="2.0.0",
    contact={"name": "MediSys Support"},
)

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

# ── Config ────────────────────────────────────────────────────────────────────
USE_MOCK   = os.getenv("AI_USE_MOCK", "true").lower() == "true"
MODEL_PATH = os.getenv("AI_MODEL_PATH", "models")
PYTHON_BIN = os.getenv(
    "PYTHON_BIN",
    "C:/Users/H/AppData/Local/Programs/Python/Python313/python.exe"
)
ML_SCRIPTS_DIR = os.getenv(
    "ML_SCRIPTS_DIR",
    os.path.join(os.path.dirname(__file__), "..", "ml_scripts")
)

# ── Skin Classes ──────────────────────────────────────────────────────────────
SKIN_CLASSES = [
    "Melanoma", "Melanocytic Nevus", "Basal Cell Carcinoma",
    "Actinic Keratosis", "Benign Keratosis", "Dermatofibroma", "Vascular Lesion",
]
SKIN_CONCERN = {
    "Melanoma": "critical", "Melanocytic Nevus": "medium",
    "Basal Cell Carcinoma": "high", "Actinic Keratosis": "medium",
    "Benign Keratosis": "low", "Dermatofibroma": "low", "Vascular Lesion": "low",
}

# ── Fracture Classes ───────────────────────────────────────────────────────────
FRACTURE_CLASSES = ["Not Fractured", "Fractured"]

# ── TensorFlow (optional) ─────────────────────────────────────────────────────
try:
    import tensorflow as tf
    TF_AVAILABLE = True
except ImportError:
    TF_AVAILABLE = False

skin_model     = None
fracture_model = None

def load_models():
    global skin_model, fracture_model
    if USE_MOCK or not TF_AVAILABLE:
        return
    skin_path     = os.path.join(MODEL_PATH, "skin_model.h5")
    fracture_path = os.path.join(MODEL_PATH, "fracture_model.h5")
    if os.path.exists(skin_path):
        skin_model = tf.keras.models.load_model(skin_path)
        print(f"✅ Skin model loaded from {skin_path}")
    if os.path.exists(fracture_path):
        fracture_model = tf.keras.models.load_model(fracture_path)
        print(f"✅ Fracture model loaded from {fracture_path}")

# ── Image Preprocessing ────────────────────────────────────────────────────────
def preprocess(image: Image.Image, size=(224, 224)) -> np.ndarray:
    image = image.convert("RGB").resize(size)
    return np.expand_dims(np.array(image) / 255.0, axis=0)

# ── Mock Prediction ────────────────────────────────────────────────────────────
def mock_predict(img_array, classes, concern_map):
    np.random.seed(int(img_array.sum() * 1000) % 2**31)
    weights = np.random.dirichlet(np.ones(len(classes))) * 100
    idx     = int(np.argmax(weights))
    all_p   = {c: round(float(weights[i]), 2) for i, c in enumerate(classes)}
    return classes[idx], round(float(weights[idx]), 2), concern_map.get(classes[idx], "medium"), all_p

# ═══════════════════════════════════════════════════════════════════════════════
# Response Schemas
# ═══════════════════════════════════════════════════════════════════════════════

class ImageAnalysisResponse(BaseModel):
    prediction:      str
    confidence:      float
    concern_level:   str
    all_predictions: Dict[str, float]
    analysis_type:   str
    model_mode:      str
    clinical_note:   str

class DrugDetail(BaseModel):
    name:         str
    rxcui:        str
    drug_class:   str
    purpose:      str
    side_effects: str

class MedicationSummaryRequest(BaseModel):
    medications:  List[str]
    diagnoses:    Optional[List[str]] = []
    vitals:       Optional[str]       = "N/A"
    patient_name: Optional[str]       = "Patient"
    age:          Optional[str]       = "N/A"

class MedicationSummaryResponse(BaseModel):
    status:               str
    patient_name:         str
    age:                  str
    summary:              str
    medications_count:    int
    drug_details:         List[DrugDetail]
    interaction_warnings: List[str]
    vitals_summary:       str
    nlp_method:           str

# ═══════════════════════════════════════════════════════════════════════════════
# Startup
# ═══════════════════════════════════════════════════════════════════════════════

@app.on_event("startup")
async def on_startup():
    load_models()
    mode = "MOCK" if USE_MOCK else "REAL"
    print(f"🚀 MediSys AI API started | Mode: {mode} | TF: {TF_AVAILABLE}")

# ═══════════════════════════════════════════════════════════════════════════════
# Endpoints
# ═══════════════════════════════════════════════════════════════════════════════

@app.get("/api/health", tags=["System"])
async def health():
    """Check service health and model availability."""
    return {
        "status":           "ok",
        "mode":             "mock" if USE_MOCK else "real",
        "tensorflow":       TF_AVAILABLE,
        "skin_model":       skin_model is not None,
        "fracture_model":   fracture_model is not None,
        "version":          "2.0.0",
    }


@app.post(
    "/api/analyze/skin",
    response_model=ImageAnalysisResponse,
    tags=["Image Analysis"],
    summary="Skin Cancer Lesion Classification",
)
async def analyze_skin(file: UploadFile = File(..., description="Dermoscopy or skin image (JPEG/PNG)")):
    """
    Classify a skin lesion image into one of 7 categories.

    Returns prediction, confidence %, concern level, and a clinical note.
    """
    contents = await file.read()
    try:
        image     = Image.open(io.BytesIO(contents))
        img_array = preprocess(image)
    except Exception:
        raise HTTPException(status_code=400, detail="Invalid image file.")

    if not USE_MOCK and skin_model is not None:
        preds      = skin_model.predict(img_array, verbose=0)
        idx        = int(np.argmax(preds[0]))
        prediction = SKIN_CLASSES[idx]
        confidence = round(float(preds[0][idx]) * 100, 2)
        concern    = SKIN_CONCERN.get(prediction, "medium")
        all_p      = {c: round(float(preds[0][i]) * 100, 2) for i, c in enumerate(SKIN_CLASSES)}
        mode       = "real"
    else:
        prediction, confidence, concern, all_p = mock_predict(img_array, SKIN_CLASSES, SKIN_CONCERN)
        mode = "mock"

    note = {
        "critical": f"⚠️ CRITICAL: {prediction} detected with {confidence}% confidence. Immediate dermatology referral required.",
        "high":     f"🔶 HIGH RISK: {prediction} detected ({confidence}%). Prompt biopsy recommended.",
        "medium":   f"⚡ MODERATE: {prediction} detected ({confidence}%). Monitoring advised.",
        "low":      f"✅ LOW RISK: {prediction} ({confidence}%). Benign appearance. Routine follow-up.",
    }.get(concern, f"{prediction} — {confidence}% confidence.")

    return ImageAnalysisResponse(
        prediction=prediction, confidence=confidence, concern_level=concern,
        all_predictions=all_p, analysis_type="skin", model_mode=mode, clinical_note=note,
    )


@app.post(
    "/api/analyze/fracture",
    response_model=ImageAnalysisResponse,
    tags=["Image Analysis"],
    summary="Bone Fracture X-Ray Detection",
)
async def analyze_fracture(file: UploadFile = File(..., description="X-Ray image (JPEG/PNG)")):
    """
    Detect whether a bone fracture is present in an X-Ray image.

    Returns: Fractured / Not Fractured, confidence %, and a clinical note.
    """
    contents = await file.read()
    try:
        image     = Image.open(io.BytesIO(contents))
        img_array = preprocess(image)
    except Exception:
        raise HTTPException(status_code=400, detail="Invalid image file.")

    fracture_concern = {"Fractured": "high", "Not Fractured": "low"}

    if not USE_MOCK and fracture_model is not None:
        preds      = fracture_model.predict(img_array, verbose=0)
        idx        = int(np.argmax(preds[0]))
        prediction = FRACTURE_CLASSES[idx]
        confidence = round(float(preds[0][idx]) * 100, 2)
        concern    = fracture_concern[prediction]
        all_p      = {c: round(float(preds[0][i]) * 100, 2) for i, c in enumerate(FRACTURE_CLASSES)}
        mode       = "real"
    else:
        prediction, confidence, concern, all_p = mock_predict(img_array, FRACTURE_CLASSES, fracture_concern)
        mode = "mock"

    note = (
        f"🦴 FRACTURE DETECTED — {confidence}% probability. Orthopedic consultation recommended. Immobilization advised."
        if prediction == "Fractured"
        else f"✅ No fracture detected ({confidence}% confidence). Continue clinical assessment if symptomatic."
    )

    return ImageAnalysisResponse(
        prediction=prediction, confidence=confidence, concern_level=concern,
        all_predictions=all_p, analysis_type="fracture", model_mode=mode, clinical_note=note,
    )


@app.post(
    "/api/medication/summarize",
    response_model=MedicationSummaryResponse,
    tags=["Medication AI"],
    summary="Clinical Medication History Summarizer",
)
async def summarize_medications(body: MedicationSummaryRequest):
    """
    Generate a clinical medication summary using:
    1. NIH RxNorm API for drug enrichment (free, no key needed)
    2. Rule-based NLP for structured clinical summary
    3. Interaction detection (Warfarin+Aspirin, NSAID+Corticosteroid, etc.)

    **Request body example:**
    ```json
    {
      "medications": ["Amoxicillin 500mg", "Ibuprofen 400mg", "Omeprazole 20mg"],
      "diagnoses": ["Sinusitis", "Gastritis"],
      "vitals": "BP:125/82, HR:90bpm",
      "patient_name": "Ahmed Benali",
      "age": "45"
    }
    ```
    """
    if not body.medications:
        raise HTTPException(status_code=400, detail="medications list cannot be empty.")

    script = os.path.join(ML_SCRIPTS_DIR, "summarize_medications.py")
    if not os.path.exists(script):
        raise HTTPException(status_code=500, detail=f"ML script not found at: {script}")

    try:
        result = subprocess.run(
            [
                PYTHON_BIN, script,
                "--medications",   ", ".join(body.medications),
                "--diagnoses",     ", ".join(body.diagnoses or []),
                "--vitals",        body.vitals or "N/A",
                "--patient_name",  body.patient_name or "Patient",
                "--age",           str(body.age or "N/A"),
            ],
            capture_output=True, text=True, timeout=60
        )
        data = json.loads(result.stdout)
    except subprocess.TimeoutExpired:
        raise HTTPException(status_code=504, detail="AI script timed out.")
    except (json.JSONDecodeError, Exception) as e:
        raise HTTPException(status_code=500, detail=f"AI script error: {str(e)}")

    if data.get("status") != "success":
        raise HTTPException(status_code=500, detail=data.get("message", "Unknown error"))

    return MedicationSummaryResponse(
        status              = data["status"],
        patient_name        = data["patient_name"],
        age                 = str(data["age"]),
        summary             = data["summary"],
        medications_count   = data["medications_count"],
        drug_details        = [DrugDetail(**d) for d in data["drug_details"]],
        interaction_warnings= data["interaction_warnings"],
        vitals_summary      = data["vitals_summary"],
        nlp_method          = data["nlp_method"],
    )
