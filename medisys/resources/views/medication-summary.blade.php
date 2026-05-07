@extends('layouts.app')
@section('page-title', 'AI Medication Summary')

@section('content')
<style>
  .med-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #065f46 100%);
    border-radius: 20px; padding: 30px 36px; color: white; margin-bottom: 28px;
    display: flex; align-items: center; gap: 20px;
  }
  .med-hero-icon { font-size: 54px; flex-shrink: 0; }
  .badge-method { display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.15);
    padding:4px 14px; border-radius:99px; font-size:12px; }
  .drug-card { background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:12px;
    padding:14px 16px; margin-bottom:10px; }
  .drug-card-name { font-weight:700; color:#0f3460; font-size:14px; }
  .drug-tag { display:inline-block; padding:2px 10px; border-radius:99px; font-size:11px; font-weight:600; margin-right:4px; }
  .drug-tag-class  { background:#ede9fe; color:#6d28d9; }
  .drug-tag-rxcui  { background:#f0f9ff; color:#0369a1; }
  .warning-card { background:#fffbeb; border:1.5px solid #fcd34d; border-radius:10px;
    padding:12px 16px; margin-bottom:8px; font-size:13px; color:#92400e; }
  .summary-box { background:white; border:2px solid #10b981; border-radius:14px;
    padding:22px; margin-bottom:20px; }
  .summary-box p { font-size:14.5px; line-height:1.75; color:#1e293b; margin:0; }
  .progress-step { display:flex; align-items:center; gap:10px; font-size:13px; color:#64748b; margin-bottom:6px; }
  .step-dot { width:22px; height:22px; border-radius:50%; display:flex; align-items:center;
    justify-content:center; font-size:11px; font-weight:700; flex-shrink:0; }
  .step-done { background:#10b981; color:white; }
  .step-info { background:#3b82f6; color:white; }
</style>

<div class="med-hero">
  <div class="med-hero-icon">💊</div>
  <div>
    <h1 style="font-size:26px;font-weight:800;margin:0 0 6px;">AI Medication History Summarizer</h1>
    <p style="margin:0 0 12px;opacity:0.75;font-size:14px;">
      Analyzes a patient's full medication record using NLP, enriches each drug via RxNorm API,
      detects interaction warnings, and generates a clinical summary.
    </p>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <span class="badge-method">🧬 RxNorm API (NIH)</span>
      <span class="badge-method">🤖 BART NLP / Gemini 2.0 Flash</span>
      <span class="badge-method">⚠️ Interaction Detection</span>
    </div>
  </div>
</div>

<div class="row">
  {{-- Left: Patient selector --}}
  <div class="col-md-4">
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-body" style="padding:26px;">
        <h5 style="font-weight:700;color:#0f3460;margin-bottom:18px;"><i class="fas fa-user-injured"></i> Select Patient</h5>

        @if(session('error'))
          <div class="alert alert-danger" style="border-radius:10px;font-size:13px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
          </div>
        @endif

        <form action="{{ route('medication-summary.generate') }}" method="POST" id="summary-form">
          @csrf
          <div class="mb-3">
            <label class="form-label" style="font-weight:600;font-size:13px;">Patient</label>
            <select name="patient_id" class="form-control" required>
              <option value="">-- Select Patient --</option>
              @foreach($patients as $patient)
                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                  {{ $patient->name ?? 'Patient #'.$patient->id }}
                  {{ $patient->age ? '(Age '.$patient->age.')' : '' }}
                </option>
              @endforeach
            </select>
          </div>
          <button type="submit" class="btn btn-success w-100" style="padding:12px;font-weight:700;font-size:15px;" id="gen-btn">
            <i class="fas fa-brain"></i> Generate AI Summary
          </button>
        </form>

        <div style="margin-top:24px;border-top:1px solid #e2e8f0;padding-top:18px;">
          <div style="font-size:12px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Pipeline Steps</div>
          <div class="progress-step"><div class="step-dot step-done">1</div>Collect prescriptions from ordonnances</div>
          <div class="progress-step"><div class="step-dot step-done">2</div>Extract diagnoses & vitals from records</div>
          <div class="progress-step"><div class="step-dot step-done">3</div>Enrich each drug via RxNorm API (NIH)</div>
          <div class="progress-step"><div class="step-dot step-info">4</div>NLP summarization (BART / Gemini)</div>
          <div class="progress-step"><div class="step-dot step-info">5</div>Drug interaction detection</div>
        </div>
      </div>
    </div>
  </div>

  {{-- Right: Result --}}
  <div class="col-md-8">
    @if(session('result'))
      @php $r = session('result'); @endphp
      <div class="card shadow-sm border-0">
        <div class="card-body" style="padding:28px;">

          {{-- Patient Header --}}
          <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;">
            <div>
              <h4 style="font-weight:800;color:#0f3460;margin:0;">{{ $r['patient_name'] }}</h4>
              <div style="font-size:13px;color:#64748b;margin-top:4px;">
                Age: <strong>{{ $r['age'] }}</strong> &nbsp;|&nbsp;
                Vitals: <strong>{{ $r['vitals'] }}</strong> &nbsp;|&nbsp;
                {{ count($r['medications']) }} medication(s) &nbsp;|&nbsp;
                {{ count($r['diagnoses']) }} diagnosis(es)
              </div>
            </div>
            <span style="background:#d1fae5;color:#065f46;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:700;">
              <i class="fas fa-robot"></i> {{ $r['nlp_method'] }}
            </span>
          </div>

          {{-- AI Clinical Summary --}}
          <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#94a3b8;letter-spacing:.5px;margin-bottom:8px;">
            📋 AI Clinical Summary
          </div>
          <div class="summary-box">
            <p>{{ $r['summary'] }}</p>
          </div>

          {{-- Interaction Warnings --}}
          @if(!empty($r['warnings']))
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#94a3b8;letter-spacing:.5px;margin-bottom:8px;">
              ⚠️ Drug Interaction Alerts ({{ count($r['warnings']) }})
            </div>
            @foreach($r['warnings'] as $warn)
              <div class="warning-card"><i class="fas fa-exclamation-triangle"></i> {{ $warn }}</div>
            @endforeach
          @else
            <div style="background:#f0fdf4;border:1px solid #86efac;border-radius:10px;padding:10px 14px;font-size:13px;color:#166534;margin-bottom:16px;">
              <i class="fas fa-check-circle"></i> No significant drug interactions detected.
            </div>
          @endif

          {{-- Diagnoses --}}
          @if(!empty($r['diagnoses']))
            <div style="margin-bottom:16px;">
              <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#94a3b8;letter-spacing:.5px;margin-bottom:8px;">🏥 Diagnoses</div>
              <div style="display:flex;flex-wrap:wrap;gap:8px;">
                @foreach($r['diagnoses'] as $dx)
                  <span style="background:#fef3c7;color:#92400e;padding:4px 12px;border-radius:99px;font-size:12px;font-weight:600;">{{ $dx }}</span>
                @endforeach
              </div>
            </div>
          @endif

          {{-- Drug Details --}}
          @if(!empty($r['drug_details']))
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#94a3b8;letter-spacing:.5px;margin-bottom:10px;">
              💊 Enriched Drug Details (via RxNorm API)
            </div>
            @foreach($r['drug_details'] as $drug)
              <div class="drug-card">
                <div class="drug-card-name">{{ $drug['name'] }}</div>
                <div style="margin-top:5px;">
                  <span class="drug-tag drug-tag-class">{{ $drug['drug_class'] }}</span>
                  @if($drug['rxcui'] !== 'N/A')
                    <span class="drug-tag drug-tag-rxcui">RxCUI: {{ $drug['rxcui'] }}</span>
                  @endif
                </div>
                <div style="margin-top:6px;font-size:12.5px;color:#334155;">
                  <strong>Purpose:</strong> {{ $drug['purpose'] }}
                </div>
                <div style="font-size:12px;color:#64748b;margin-top:2px;">
                  <strong>Monitor:</strong> {{ $drug['side_effects'] }}
                </div>
              </div>
            @endforeach
          @else
            {{-- Simple fallback: just list medications --}}
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;color:#94a3b8;letter-spacing:.5px;margin-bottom:10px;">💊 Medications</div>
            <div style="display:flex;flex-wrap:wrap;gap:8px;">
              @foreach($r['medications'] as $med)
                <span style="background:#eff6ff;color:#1e40af;padding:5px 14px;border-radius:99px;font-size:13px;font-weight:600;">{{ $med }}</span>
              @endforeach
            </div>
          @endif

        </div>
      </div>
    @else
      <div class="card shadow-sm border-0 h-100" style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);">
        <div class="card-body" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:70px 28px;text-align:center;">
          <i class="fas fa-file-medical-alt" style="font-size:68px;color:#cbd5e1;margin-bottom:20px;"></i>
          <h5 style="color:#64748b;font-weight:600;margin-bottom:10px;">No Summary Generated Yet</h5>
          <p style="color:#94a3b8;font-size:14px;max-width:340px;">Select a patient and click Generate. The AI will analyze their complete prescription history and produce a clinical summary.</p>
        </div>
      </div>
    @endif
  </div>
</div>

@push('scripts')
<script>
  document.getElementById('summary-form').addEventListener('submit', function() {
    const btn = document.getElementById('gen-btn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
    btn.disabled = true;
  });
</script>
@endpush
@endsection
