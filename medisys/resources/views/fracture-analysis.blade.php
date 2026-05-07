@extends('layouts.app')
@section('page-title', 'AI X-Ray Fracture Detection')

@section('content')
<style>
  .xray-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f3460 100%);
    border-radius: 20px;
    padding: 32px;
    margin-bottom: 28px;
    color: white;
    display: flex;
    align-items: center;
    gap: 20px;
  }
  .xray-hero-icon {
    font-size: 56px;
    opacity: 0.9;
    flex-shrink: 0;
  }
  .upload-zone {
    border: 2.5px dashed #cbd5e1;
    border-radius: 16px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background: #f8fafc;
  }
  .upload-zone:hover, .upload-zone.drag-over {
    border-color: #3b82f6;
    background: #eff6ff;
  }
  .upload-zone input { display: none; }
  .preview-img {
    width: 100%;
    max-height: 360px;
    object-fit: contain;
    border-radius: 12px;
    background: #000;
    margin-bottom: 12px;
  }
  .risk-bar-wrap { height: 10px; background: #e5e7eb; border-radius: 99px; overflow: hidden; margin: 8px 0; }
  .risk-bar-fill { height: 100%; border-radius: 99px; transition: width 1s ease; }
  .result-card {
    border-radius: 18px;
    padding: 24px;
    margin-top: 20px;
    border: 2px solid;
  }
  .result-card.fractured { background: #fff1f2; border-color: #fca5a5; }
  .result-card.normal    { background: #f0fdf4; border-color: #86efac; }
</style>

<div class="xray-hero">
  <div class="xray-hero-icon">🦴</div>
  <div>
    <h1 style="font-size:26px;font-weight:800;margin:0 0 6px;">AI X-Ray Fracture Detection</h1>
    <p style="margin:0;opacity:0.75;font-size:15px;">Upload a bone X-ray image. The CNN model will detect fractures, provide probability, and generate a clinical note for the doctor.</p>
    <div style="margin-top:12px;display:flex;gap:12px;flex-wrap:wrap;">
      <span style="background:rgba(255,255,255,0.15);padding:4px 12px;border-radius:99px;font-size:12px;">📊 Bone Fracture Dataset</span>
      <span style="background:rgba(255,255,255,0.15);padding:4px 12px;border-radius:99px;font-size:12px;">🧠 MobileNetV2 CNN</span>
      <span style="background:rgba(255,255,255,0.15);padding:4px 12px;border-radius:99px;font-size:12px;">🔒 Sanctum Authenticated</span>
    </div>
  </div>
</div>

<div class="row">
  {{-- Upload Form --}}
  <div class="col-md-5">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body" style="padding:28px;">
        <h4 style="font-weight:700;color:#0f3460;margin-bottom:20px;"><i class="fas fa-upload"></i> Upload X-Ray Image</h4>

        @if(session('error'))
          <div class="alert alert-danger" style="border-radius:10px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
          </div>
        @endif

        <form action="{{ route('fracture.analyze') }}" method="POST" enctype="multipart/form-data" id="fracture-form">
          @csrf

          <div class="mb-3">
            <label class="form-label" style="font-weight:600;">Select Patient</label>
            <select name="patient_id" class="form-control" required>
              <option value="">-- Choose Patient --</option>
              @foreach(\App\Models\Patient::with('user')->get() as $patient)
                <option value="{{ $patient->id }}">{{ $patient->user->name ?? 'Patient #'.$patient->id }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight:600;">X-Ray Image</label>
            <div class="upload-zone" id="upload-zone" onclick="document.getElementById('xray-input').click()">
              <div id="upload-placeholder">
                <i class="fas fa-x-ray" style="font-size:40px;color:#94a3b8;display:block;margin-bottom:10px;"></i>
                <p style="color:#64748b;margin:0;font-weight:600;">Click or drag & drop X-ray image here</p>
                <p style="color:#94a3b8;font-size:12px;margin-top:4px;">JPEG, PNG — Max 10MB</p>
              </div>
              <img id="img-preview" class="preview-img" style="display:none;">
              <input type="file" name="image" id="xray-input" accept="image/png,image/jpeg" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label" style="font-weight:600;">Clinical Notes <span style="color:#94a3b8;font-weight:400;">(optional)</span></label>
            <textarea name="notes" class="form-control" rows="3" placeholder="E.g., Patient reports pain after fall. Suspected right wrist fracture."></textarea>
          </div>

          <button type="submit" class="btn btn-primary w-100" style="padding:12px;font-size:16px;font-weight:700;" id="analyze-btn">
            <i class="fas fa-search"></i> Analyze X-Ray
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Result Panel --}}
  <div class="col-md-7">
    @if(session('result'))
      @php $r = session('result'); $ai = $r['ai_analysis']; @endphp
      <div class="card shadow-sm border-0">
        <div class="card-body" style="padding:28px;">
          <h4 style="font-weight:700;color:#0f3460;margin-bottom:16px;"><i class="fas fa-poll-h"></i> Analysis Result</h4>

          <img src="{{ $r['image_url'] }}" alt="X-Ray" class="preview-img" style="max-height:260px;width:100%;object-fit:contain;background:#111;border-radius:12px;margin-bottom:16px;">

          <div class="result-card {{ $ai['is_fractured'] ? 'fractured' : 'normal' }}">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
              <span style="font-size:32px;">{{ $ai['is_fractured'] ? '⚠️' : '✅' }}</span>
              <div>
                <div style="font-size:20px;font-weight:800;color:{{ $ai['is_fractured'] ? '#dc2626' : '#16a34a' }};">
                  {{ $ai['diagnosis'] }}
                </div>
                <div style="font-size:13px;color:#64748b;">Fracture Probability: <strong>{{ $ai['probability'] }}%</strong></div>
              </div>
            </div>
            <div class="risk-bar-wrap">
              <div class="risk-bar-fill" style="width:{{ $ai['probability'] }}%;background:{{ $ai['is_fractured'] ? '#dc2626' : '#16a34a' }};"></div>
            </div>
            <div style="background:white;border-radius:10px;padding:14px;margin-top:14px;border-left:4px solid {{ $ai['is_fractured'] ? '#dc2626' : '#16a34a' }};">
              <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;color:#94a3b8;margin-bottom:6px;">📋 Clinical Note</div>
              <p style="margin:0;font-size:13.5px;color:#1e293b;line-height:1.6;">{{ $ai['clinical_note'] }}</p>
            </div>
          </div>

          <p style="margin-top:16px;font-size:12px;color:#94a3b8;text-align:center;">
            <i class="fas fa-check-circle" style="color:#10b981;"></i>
            This result has been saved to the patient's lab results for the doctor to review.
          </p>
          <a href="/lab-results" class="btn btn-outline-secondary btn-sm w-100">View in Lab Results →</a>
        </div>
      </div>
    @else
      <div class="card shadow-sm border-0 h-100" style="background:linear-gradient(135deg,#f8fafc,#f1f5f9);">
        <div class="card-body" style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 28px;text-align:center;">
          <i class="fas fa-x-ray" style="font-size:64px;color:#cbd5e1;margin-bottom:20px;"></i>
          <h5 style="color:#64748b;font-weight:600;">No Analysis Yet</h5>
          <p style="color:#94a3b8;font-size:14px;">Upload an X-ray image and select a patient to get the AI fracture analysis result here.</p>
        </div>
      </div>
    @endif
  </div>
</div>

@push('scripts')
<script>
  const input = document.getElementById('xray-input');
  const preview = document.getElementById('img-preview');
  const placeholder = document.getElementById('upload-placeholder');
  const zone = document.getElementById('upload-zone');

  input.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
      preview.src = e.target.result;
      preview.style.display = 'block';
      placeholder.style.display = 'none';
    };
    reader.readAsDataURL(file);
  });

  // Drag and drop
  zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
  zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
  zone.addEventListener('drop', e => {
    e.preventDefault();
    zone.classList.remove('drag-over');
    if (e.dataTransfer.files.length) {
      input.files = e.dataTransfer.files;
      input.dispatchEvent(new Event('change'));
    }
  });

  // Show loading on submit
  document.getElementById('fracture-form').addEventListener('submit', function () {
    const btn = document.getElementById('analyze-btn');
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
    btn.disabled = true;
  });
</script>
@endpush
@endsection
