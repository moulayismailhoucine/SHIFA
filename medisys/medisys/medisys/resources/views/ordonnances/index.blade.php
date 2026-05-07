@extends('layouts.app')
@section('page-title', 'Prescriptions')
@section('content')
<div class="card">
  <div class="card-header">
    <span class="card-title"><i class="fas fa-prescription" style="color:#533483;margin-right:8px;"></i>Prescriptions (Ordonnances)</span>
    <button class="btn btn-primary btn-sm" onclick="document.getElementById('ord-modal').style.display='flex'"><i class="fas fa-plus"></i> New Prescription</button>
  </div>
  <div class="card-body">
    <table class="data-table">
      <thead><tr><th>#</th><th>Patient</th><th>Doctor</th><th>Medications</th><th>Issued</th><th>Valid Until</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody id="ord-body">
        <tr><td colspan="8" style="text-align:center;padding:28px;color:#94a3b8;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<div id="ord-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;align-items:center;justify-content:center;">
  <div style="background:white;border-radius:20px;padding:32px;max-width:640px;width:95%;max-height:90vh;overflow-y:auto;box-shadow:0 24px 80px rgba(0,0,0,0.3);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
      <h3 style="font-size:18px;font-weight:700;color:#0f3460;">New Prescription</h3>
      <button onclick="document.getElementById('ord-modal').style.display='none'" style="border:none;background:none;font-size:20px;cursor:pointer;color:#94a3b8;">&times;</button>
    </div>
    <form id="ord-form">
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Patient *</label><select id="of-patient" class="form-control" required><option value="">Select...</option></select></div>
        <div class="form-group"><label class="form-label">Issue Date *</label><input type="date" id="of-date" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Valid Until <span style="color:#94a3b8;font-weight:400;">(optional)</span></label><input type="date" id="of-valid" class="form-control"></div>
      </div>
      <div style="margin-bottom:12px;"><label class="form-label">Medications *</label>
        <div id="meds-list"></div>
        <button type="button" class="btn btn-outline btn-sm" style="margin-top:8px;" onclick="addMed()"><i class="fas fa-plus"></i> Add Medication</button>
      </div>
      <div class="form-group"><label class="form-label">Instructions</label><textarea id="of-instructions" class="form-control" rows="2"></textarea></div>
      <div style="display:flex;gap:10px;justify-content:flex-end;">
        <button type="button" class="btn btn-outline" onclick="document.getElementById('ord-modal').style.display='none'">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
const token = localStorage.getItem('auth_token')||'';
const h = {'Content-Type':'application/json','Accept':'application/json','Authorization':'Bearer '+token};

function medRow() {
  return `<div class="form-grid" style="margin-bottom:8px;border:1px solid #e2e8f0;border-radius:10px;padding:10px;">
    <div class="form-group" style="margin:0;"><input type="text" class="form-control med-name" placeholder="Drug name *" required></div>
    <div class="form-group" style="margin:0;"><input type="text" class="form-control med-dosage" placeholder="Dosage (optional)"></div>
    <div class="form-group" style="margin:0;"><input type="text" class="form-control med-freq" placeholder="Frequency (optional)"></div>
    <div class="form-group" style="margin:0;"><input type="text" class="form-control med-dur" placeholder="Duration (optional)"></div>
  </div>`;
}
function addMed() { document.getElementById('meds-list').insertAdjacentHTML('beforeend', medRow()); }

async function loadOrdonnances() {
  const r = await fetch('/api/ordonnances', {headers: h});
  const {data} = await r.json();
  const tb = document.getElementById('ord-body');
  if (!data.length) { tb.innerHTML='<tr><td colspan="8" style="text-align:center;padding:24px;color:#94a3b8;">No prescriptions yet</td></tr>'; return; }
  tb.innerHTML = data.map(o => `<tr>
    <td style="color:#94a3b8;font-size:12px;">#${o.id}</td>
    <td>${o.patient.name}</td>
    <td>${o.doctor.user.name}</td>
    <td><span class="badge badge-info">${o.medications.length} meds</span></td>
    <td>${o.issued_date}</td>
    <td>${o.valid_until||'—'}</td>
    <td>
      <span class="badge ${o.status==='active'?'badge-success':'badge-warning'}">${o.status}</span>
    </td>
    <td>
      <div style="display:flex;gap:4px;">
        <a href="/api/ordonnances/${o.id}/pdf?token=${token}" target="_blank" class="btn btn-sm btn-outline" style="color:#e94560;border-color:#e94560;"><i class="fas fa-print"></i> Print</a>
        <button class="btn btn-sm btn-outline" style="color:#ef4444;border-color:#ef4444;" onclick="deleteOrdonnance(${o.id})"><i class="fas fa-trash"></i></button>
      </div>
    </td>
  </tr>`).join('');
}

async function deleteOrdonnance(id) {
  if (!confirm('Are you sure you want to delete this prescription?')) return;
  const r = await fetch('/api/ordonnances/' + id, {method: 'DELETE', headers: h});
  if (r.ok) loadOrdonnances();
}

async function initSelects() {
  const pr = await fetch('/api/patients', {headers: h});
  const {data} = await pr.json();
  const sel = document.getElementById('of-patient');
  (data.data||data).forEach(p => sel.innerHTML += `<option value="${p.id}">${p.name}</option>`);

  document.getElementById('of-patient').addEventListener('change', async function() {
    const rr = await fetch(`/api/medical-records?patient_id=${this.value}`, {headers: h});
    const d = await rr.json();
    const rs = document.getElementById('of-record');
    rs.innerHTML = '<option value="">Select record...</option>';
    (d.data?.data||d.data||[]).forEach(r => rs.innerHTML += `<option value="${r.id}">${r.diagnosis} — ${r.visit_date}</option>`);
  });
}

document.getElementById('ord-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const meds = [...document.querySelectorAll('#meds-list > div')].map(d => ({
    name: d.querySelector('.med-name').value,
    dosage: d.querySelector('.med-dosage').value,
    frequency: d.querySelector('.med-freq').value,
    duration: d.querySelector('.med-dur').value,
  }));
  const body = {
    patient_id:   document.getElementById('of-patient').value,
    issued_date:  document.getElementById('of-date').value,
    valid_until:  document.getElementById('of-valid').value || null,
    instructions: document.getElementById('of-instructions').value,
    medications:  meds,
  };
  const r = await fetch('/api/ordonnances', {method:'POST',headers:h,body:JSON.stringify(body)});
  const data = await r.json();
  if (data.success) { document.getElementById('ord-modal').style.display='none'; loadOrdonnances(); }
  else alert(JSON.stringify(data.errors||data.message));
});

document.getElementById('of-date').value = new Date().toISOString().split('T')[0];
addMed();
loadOrdonnances();
initSelects();
</script>
@endpush
@endsection
