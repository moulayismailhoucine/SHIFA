@extends('layouts.app')
@section('page-title', 'Medical Records')
@section('content')
<div class="card">
  <div class="card-header">
    <span class="card-title"><i class="fas fa-file-medical" style="color:#3b82f6;margin-right:8px;"></i>Medical Records</span>
    <button class="btn btn-primary btn-sm" onclick="openModal()"><i class="fas fa-plus"></i> New Record</button>
  </div>
  <div class="card-body">
    <table class="data-table">
      <thead><tr><th>#</th><th>Patient</th><th>Doctor</th><th>Diagnosis</th><th>Visit Type</th><th>Date</th><th>Actions</th></tr></thead>
      <tbody id="records-body">
        <tr><td colspan="7" style="text-align:center;padding:28px;color:#94a3b8;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<div id="record-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;align-items:center;justify-content:center;">
  <div style="background:white;border-radius:20px;padding:32px;max-width:600px;width:95%;max-height:90vh;overflow-y:auto;box-shadow:0 24px 80px rgba(0,0,0,0.3);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
      <h3 style="font-size:18px;font-weight:700;color:#0f3460;">New Medical Record</h3>
      <button onclick="document.getElementById('record-modal').style.display='none'" style="border:none;background:none;font-size:20px;cursor:pointer;color:#94a3b8;">&times;</button>
    </div>
    <form id="record-form">
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Patient *</label>
          <select id="f-patient" class="form-control" required><option value="">Select patient...</option></select>
        </div>
        <div class="form-group"><label class="form-label">Visit Type</label>
          <select id="f-type" class="form-control">
            <option value="consultation">Consultation</option>
            <option value="follow_up">Follow-up</option>
            <option value="emergency">Emergency</option>
            <option value="routine">Routine</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Visit Date *</label><input type="date" id="f-date" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Temperature (°C)</label><input type="number" id="f-temp" class="form-control" step="0.1" min="30" max="45"></div>
        <div class="form-group"><label class="form-label">Blood Pressure</label><input type="text" id="f-bp" class="form-control" placeholder="120/80"></div>
        <div class="form-group"><label class="form-label">Heart Rate (bpm)</label><input type="number" id="f-hr" class="form-control" min="20" max="300"></div>
        <div class="form-group"><label class="form-label">Weight (kg)</label><input type="number" id="f-weight" class="form-control" step="0.1"></div>
        <div class="form-group"><label class="form-label">Height (cm)</label><input type="number" id="f-height" class="form-control" step="0.1"></div>
      </div>
      <div class="form-group"><label class="form-label">Diagnosis *</label><input type="text" id="f-diagnosis" class="form-control" required></div>
      <div class="form-group"><label class="form-label">Notes</label><textarea id="f-notes" class="form-control" rows="3"></textarea></div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn btn-outline" onclick="document.getElementById('record-modal').style.display='none'">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Record</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
const token = localStorage.getItem('auth_token')||'';
const h = {'Content-Type':'application/json','Accept':'application/json','Authorization':'Bearer '+token};
const colors = ['#0f3460','#e94560','#533483','#10b981','#f59e0b'];
const typeColors = {consultation:'badge-info',follow_up:'badge-purple',emergency:'badge-danger',routine:'badge-success'};

async function loadRecords() {
  const r = await fetch('/api/medical-records', {headers: h});
  const {data} = await r.json();
  const rows = data.data||data;
  const tb = document.getElementById('records-body');
  if (!rows.length) { tb.innerHTML='<tr><td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">No records yet</td></tr>'; return; }
  tb.innerHTML = rows.map((r,i) => `<tr>
    <td style="color:#94a3b8;font-size:12px;">#${r.id}</td>
    <td><div style="display:flex;align-items:center;gap:8px;"><div class="avatar" style="background:${colors[i%5]};width:28px;height:28px;font-size:11px;">${r.patient.name.charAt(0)}</div>${r.patient.name}</div></td>
    <td>${r.doctor.user.name}</td>
    <td style="max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${r.diagnosis}</td>
    <td><span class="badge ${typeColors[r.visit_type]||'badge-info'}">${r.visit_type}</span></td>
    <td>${r.visit_date}</td>
    <td>
      <a href="/ordonnances" class="btn btn-sm btn-outline" style="color:#533483;border-color:#533483;margin-right:4px;" title="Write Prescription"><i class="fas fa-prescription"></i> Ordonnance</a>
      <button class="btn btn-sm" style="background:#fff1f2;color:#e94560;border:none;" onclick="deleteRecord(${r.id})"><i class="fas fa-trash"></i></button>
    </td>
  </tr>`).join('');
}

async function loadPatients() {
  const r = await fetch('/api/patients', {headers: h});
  const {data} = await r.json();
  const sel = document.getElementById('f-patient');
  (data.data||data).forEach(p => sel.innerHTML += `<option value="${p.id}">${p.name} (${p.age}y)</option>`);
}

function openModal() {
  document.getElementById('f-date').value = new Date().toISOString().split('T')[0];
  document.getElementById('record-modal').style.display = 'flex';
}

document.getElementById('record-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const body = {
    patient_id: document.getElementById('f-patient').value,
    diagnosis: document.getElementById('f-diagnosis').value,
    notes: document.getElementById('f-notes').value,
    visit_type: document.getElementById('f-type').value,
    visit_date: document.getElementById('f-date').value,
    temperature: document.getElementById('f-temp').value||null,
    blood_pressure: document.getElementById('f-bp').value||null,
    heart_rate: document.getElementById('f-hr').value||null,
    weight: document.getElementById('f-weight').value||null,
    height: document.getElementById('f-height').value||null,
  };
  const r = await fetch('/api/medical-records', {method:'POST',headers:h,body:JSON.stringify(body)});
  const data = await r.json();
  if (data.success) { document.getElementById('record-modal').style.display='none'; loadRecords(); }
  else alert(JSON.stringify(data.errors||data.message));
});

async function deleteRecord(id) {
  if (!confirm('Delete this record?')) return;
  await fetch(`/api/medical-records/${id}`, {method:'DELETE',headers:h});
  loadRecords();
}

loadRecords(); loadPatients();
</script>
@endpush
@endsection
