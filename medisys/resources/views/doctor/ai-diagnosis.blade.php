@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold text-primary">
      <i class="bi bi-robot me-2"></i> AI Diagnosis Assistant
    </h2>
    <span class="badge {{ $aiServiceOnline ? 'bg-success' : 'bg-danger' }} fs-6">
      AI Service: {{ $aiServiceOnline ? 'Online' : 'Offline' }}
    </span>
  </div>

  {{-- Upload Card --}}
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom">
      <h5 class="mb-0 fw-semibold">New Analysis</h5>
    </div>
    <div class="card-body">
      <form id="aiForm" enctype="multipart/form-data">
        @csrf

        <div class="row g-3">
          {{-- Patient Select --}}
          <div class="col-md-4">
            <label class="form-label fw-semibold">Patient</label>
            <select name="patient_id" id="patientSelect" class="form-select" required>
              <option value="" disabled selected>Select patient...</option>
              @foreach($patients as $patient)
                <option value="{{ $patient->id }}">{{ $patient->name }} ({{ $patient->age }}y)</option>
              @endforeach
            </select>
          </div>

          {{-- Analysis Type --}}
          <div class="col-md-3">
            <label class="form-label fw-semibold">Analysis Type</label>
            <select name="image_type" id="analysisType" class="form-select" required>
              <option value="skin">Skin Lesion (ISIC)</option>
              <option value="xray">Chest X-Ray</option>
            </select>
          </div>

          {{-- Image Upload --}}
          <div class="col-md-5">
            <label class="form-label fw-semibold">Medical Image</label>
            <input type="file" name="image" id="imageInput" class="form-control" accept="image/*" required>
            <small class="text-muted">Max 5MB. JPG, PNG accepted.</small>
          </div>
        </div>

        {{-- Preview --}}
        <div class="row mt-3">
          <div class="col-md-6">
            <div id="imagePreview" class="d-none border rounded p-2 text-center bg-light">
              <img id="previewImg" src="" alt="Preview" style="max-height: 250px; max-width: 100%;" class="rounded">
            </div>
          </div>
        </div>

        {{-- Submit --}}
        <div class="mt-3">
          <button type="submit" id="submitBtn" class="btn btn-primary px-4">
            <i class="bi bi-lightning-charge me-1"></i> Run AI Analysis
          </button>
        </div>
      </form>
    </div>
  </div>

  {{-- Result Card (hidden initially) --}}
  <div id="resultCard" class="card shadow border-0 mb-4 d-none">
    <div class="card-header bg-white border-bottom d-flex justify-content-between">
      <h5 class="mb-0 fw-semibold">AI Analysis Result</h5>
      <span id="resultBadge" class="badge"></span>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-4 text-center mb-3">
          <img id="resultImage" src="" alt="Analyzed" class="img-fluid rounded border" style="max-height: 280px;">
        </div>
        <div class="col-md-8">
          <div class="row g-3">
            <div class="col-sm-6">
              <div class="p-3 bg-light rounded">
                <div class="text-muted small">AI Prediction</div>
                <div id="resultPrediction" class="fs-4 fw-bold text-dark"></div>
              </div>
            </div>
            <div class="col-sm-6">
              <div class="p-3 bg-light rounded">
                <div class="text-muted small">Confidence</div>
                <div class="fs-4 fw-bold">
                  <span id="resultConfidence"></span>%
                </div>
              </div>
            </div>
          </div>

          {{-- Concern Meter --}}
          <div class="mt-3">
            <label class="form-label fw-semibold">Concern Level</label>
            <div class="progress" style="height: 28px;">
              <div id="concernBar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
            </div>
            <div class="d-flex justify-content-between mt-1">
              <small class="text-muted">Low Risk</small>
              <small class="text-muted">Critical</small>
            </div>
          </div>

          {{-- All Predictions --}}
          <div class="mt-3">
            <label class="form-label fw-semibold">All Probabilities</label>
            <div id="allPredictions" class="small"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- History Table --}}
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom">
      <h5 class="mb-0 fw-semibold">Recent Analyses</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th>Date</th>
              <th>Patient</th>
              <th>Type</th>
              <th>Prediction</th>
              <th>Confidence</th>
              <th>Concern</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse($analyses as $analysis)
            <tr>
              <td>{{ $analysis->created_at->format('d/m/Y H:i') }}</td>
              <td>{{ $analysis->patient->name ?? '—' }}</td>
              <td>
                <span class="badge bg-{{ $analysis->image_type === 'skin' ? 'info' : 'secondary' }}">
                  {{ strtoupper($analysis->image_type) }}
                </span>
              </td>
              <td class="fw-semibold">{{ $analysis->ai_prediction }}</td>
              <td>{{ $analysis->confidence_score }}%</td>
              <td>
                <span class="badge bg-{{ $analysis->concern_level === 'critical' ? 'danger' : ($analysis->concern_level === 'high' ? 'warning text-dark' : ($analysis->concern_level === 'medium' ? 'info' : 'success')) }}">
                  {{ ucfirst($analysis->concern_level) }}
                </span>
              </td>
              <td>
                <span class="badge bg-{{ $analysis->status === 'confirmed' ? 'success' : ($analysis->status === 'reviewed' ? 'primary' : 'light text-dark') }}">
                  {{ ucfirst($analysis->status) }}
                </span>
              </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-4">No analyses yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection

@section('scripts')
<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(e) {
      document.getElementById('previewImg').src = e.target.result;
      document.getElementById('imagePreview').classList.remove('d-none');
    };
    reader.readAsDataURL(file);
  }
});

document.getElementById('aiForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('submitBtn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Analyzing...';

  const formData = new FormData(this);
  const token = localStorage.getItem('auth_token');

  try {
    const res = await fetch('/api/ai-analyses', {
      method: 'POST',
      headers: { 'Authorization': 'Bearer ' + token },
      body: formData
    });
    const data = await res.json();

    if (data.success) {
      showResult(data.data);
    } else {
      alert(data.message || 'Analysis failed.');
    }
  } catch (err) {
    alert('Error: ' + err.message);
  }

  btn.disabled = false;
  btn.innerHTML = '<i class="bi bi-lightning-charge me-1"></i> Run AI Analysis';
});

function showResult(data) {
  const card = document.getElementById('resultCard');
  card.classList.remove('d-none');

  document.getElementById('resultImage').src = data.image_path ? '/storage/' + data.image_path : '';
  document.getElementById('resultPrediction').textContent = data.ai_prediction;
  document.getElementById('resultConfidence').textContent = data.confidence_score;

  // Concern badge
  const badge = document.getElementById('resultBadge');
  badge.textContent = data.concern_level.toUpperCase();
  badge.className = 'badge fs-6 ' + concernClass(data.concern_level);

  // Progress bar
  const bar = document.getElementById('concernBar');
  bar.style.width = data.confidence_score + '%';
  bar.className = 'progress-bar ' + concernClass(data.concern_level);
  bar.textContent = data.confidence_score + '%';

  // All predictions
  const predsDiv = document.getElementById('allPredictions');
  predsDiv.innerHTML = '';
  if (data.details && data.details.all_predictions) {
    Object.entries(data.details.all_predictions).forEach(([cls, val]) => {
      const row = document.createElement('div');
      row.className = 'd-flex justify-content-between border-bottom py-1';
      row.innerHTML = `<span>${cls}</span><span class="fw-bold">${val.toFixed ? val.toFixed(2) : val}%</span>`;
      predsDiv.appendChild(row);
    });
  }
}

function concernClass(level) {
  switch(level) {
    case 'critical': return 'bg-danger';
    case 'high': return 'bg-warning text-dark';
    case 'medium': return 'bg-info text-dark';
    default: return 'bg-success';
  }
}
</script>
@endsection
