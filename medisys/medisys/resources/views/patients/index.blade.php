@extends('layouts.app')
@section('page-title', 'Patients')

@section('content')
<div class="card">
  <div class="card-header">
    <span class="card-title"><i class="fas fa-user-injured" style="color:#3b82f6;margin-right:8px;"></i>Patient Registry</span>
    <div style="display:flex;gap:10px;align-items:center;">
      <div style="position:relative;">
        <i class="fas fa-search" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:13px;"></i>
        <input type="text" id="search-input" class="form-control" placeholder="Search patients..." style="padding-left:36px;width:220px;height:38px;">
      </div>
      <button class="btn btn-primary btn-sm" onclick="openModal()"><i class="fas fa-plus"></i> Add Patient</button>
    </div>
  </div>
  <div class="card-body">
    <table class="data-table">
      <thead>
        <tr><th>#</th><th>Name</th><th>Age</th><th>Gender</th><th>Phone</th><th>NFC</th><th>Blood</th><th>Actions</th></tr>
      </thead>
      <tbody id="patients-body">
        <tr><td colspan="8" style="text-align:center;padding:28px;color:#94a3b8;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal -->
<div id="patient-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;align-items:center;justify-content:center;">
  <div style="background:white;border-radius:20px;padding:32px;max-width:600px;width:95%;max-height:90vh;overflow-y:auto;box-shadow:0 24px 80px rgba(0,0,0,0.3);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
      <h3 id="modal-title" style="font-size:18px;font-weight:700;color:#0f3460;"></h3>
      <button onclick="closeModal()" style="border:none;background:none;font-size:20px;cursor:pointer;color:#94a3b8;">&times;</button>
    </div>
    <form id="patient-form">
      <input type="hidden" id="patient-id">
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Full Name *</label><input type="text" id="f-name" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Age *</label><input type="number" id="f-age" class="form-control" min="0" max="150" required></div>
        <div class="form-group"><label class="form-label">Gender *</label>
          <select id="f-gender" class="form-control">
            <option value="male">Male</option><option value="female">Female</option><option value="other">Other</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Phone</label><input type="text" id="f-phone" class="form-control"></div>
        <div class="form-group"><label class="form-label">Email</label><input type="email" id="f-email" class="form-control"></div>
        <div class="form-group"><label class="form-label">NFC UID</label><input type="text" id="f-nfc" class="form-control" placeholder="e.g. NFC001ABC"></div>
        <div class="form-group"><label class="form-label">Blood Type</label>
          <select id="f-blood" class="form-control">
            <option value="">Unknown</option>
            <option>A+</option><option>A-</option><option>B+</option><option>B-</option>
            <option>O+</option><option>O-</option><option>AB+</option><option>AB-</option>
          </select>
        </div>
        <div class="form-group"><label class="form-label">Date of Birth</label><input type="date" id="f-dob" class="form-control"></div>
        <div class="form-group"><label class="form-label">Profile Photo</label><input type="file" id="f-photo" class="form-control" accept="image/*"></div>
      </div>
      <div class="form-group"><label class="form-label">Allergies</label><textarea id="f-allergies" class="form-control" rows="2"></textarea></div>
      <div class="form-group"><label class="form-label">Emergency Contact</label><input type="text" id="f-emergency" class="form-control"></div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn btn-primary" id="save-btn"><i class="fas fa-save"></i> Save</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
const token = localStorage.getItem('auth_token') || '';
const h = {'Accept':'application/json','Authorization':'Bearer '+token};
const colors = ['#0f3460','#e94560','#533483','#10b981','#f59e0b'];

let editId = null;

async function loadPatients(search='') {
  const url = search ? `/api/patients?search=${encodeURIComponent(search)}` : '/api/patients';
  const r = await fetch(url, {headers: h});
  const {data} = await r.json();
  const rows = data.data || data;
  const tb = document.getElementById('patients-body');
  if (!rows.length) { tb.innerHTML = '<tr><td colspan="8" style="text-align:center;padding:24px;color:#94a3b8;">No patients found</td></tr>'; return; }
  tb.innerHTML = rows.map((p,i) => `
    <tr>
      <td><span style="color:#94a3b8;font-size:12px;">#${p.id}</span></td>
      <td>
        <div style="display:flex;align-items:center;gap:10px;">
          ${p.photo_url ? `<img src="${p.photo_url}" style="width:32px;height:32px;border-radius:8px;object-fit:cover;">` : `<div class="avatar" style="background:${colors[i%5]}">${p.name.charAt(0)}</div>`}
          <div>
            <div style="font-weight:600;">${p.name}</div>
            <div style="font-size:11px;color:#94a3b8;">${p.email||''}</div>
          </div>
        </div>
      </td>
      <td>${p.age}</td>
      <td><span class="badge ${p.gender==='male'?'badge-info':'badge-purple'}">${p.gender}</span></td>
      <td>${p.phone||'—'}</td>
      <td>${p.nfc_uid ? '<span class="nfc-badge"><i class="fas fa-wifi"></i> NFC</span>' : '<span style="color:#94a3b8;font-size:12px;">None</span>'}</td>
      <td>${p.blood_type ? `<span class="badge badge-danger">${p.blood_type}</span>` : '—'}</td>
      <td>
        <a href="/doctor-patient-view?patient_id=${p.id}" class="btn btn-sm btn-primary" title="View Full Profile"><i class="fas fa-id-card"></i> Profile</a>
        <button class="btn btn-outline btn-sm" onclick="editPatient(${p.id})" title="Edit"><i class="fas fa-edit"></i></button>
        <button class="btn btn-sm" style="background:#fff1f2;color:#e94560;border:none;" onclick="deletePatient(${p.id})" title="Delete"><i class="fas fa-trash"></i></button>
      </td>
    </tr>`).join('');
}

function openModal(patient=null) {
  editId = patient?.id || null;
  document.getElementById('modal-title').textContent = editId ? 'Edit Patient' : 'Add New Patient';
  document.getElementById('patient-id').value = editId || '';
  ['name','age','gender','phone','email','nfc_uid','blood_type','date_of_birth','allergies','emergency_contact'].forEach(k => {
    const el = document.getElementById('f-'+k.replace('_uid','').replace('_type','').replace('_of_birth','ob').replace('_contact','emergency')) || document.getElementById('f-'+k);
    if (el && patient) el.value = patient[k] || '';
    else if (el) el.value = '';
  });
  if (patient) {
    document.getElementById('f-name').value = patient.name||'';
    document.getElementById('f-age').value = patient.age||'';
    document.getElementById('f-gender').value = patient.gender||'male';
    document.getElementById('f-phone').value = patient.phone||'';
    document.getElementById('f-email').value = patient.email||'';
    document.getElementById('f-nfc').value = patient.nfc_uid||'';
    document.getElementById('f-blood').value = patient.blood_type||'';
    document.getElementById('f-dob').value = patient.date_of_birth||'';
    document.getElementById('f-allergies').value = patient.allergies||'';
    document.getElementById('f-emergency').value = patient.emergency_contact||'';
  }
  document.getElementById('f-photo').value = '';
  document.getElementById('patient-modal').style.display = 'flex';
}
function closeModal() { document.getElementById('patient-modal').style.display = 'none'; }

async function editPatient(id) {
  const r = await fetch(`/api/patients/${id}`, {headers: h});
  const {data} = await r.json();
  openModal(data);
}

document.getElementById('patient-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const formData = new FormData();
  formData.append('name', document.getElementById('f-name').value);
  formData.append('age', parseInt(document.getElementById('f-age').value));
  formData.append('gender', document.getElementById('f-gender').value);
  formData.append('phone', document.getElementById('f-phone').value);
  formData.append('email', document.getElementById('f-email').value);
  formData.append('nfc_uid', document.getElementById('f-nfc').value);
  formData.append('blood_type', document.getElementById('f-blood').value);
  formData.append('date_of_birth', document.getElementById('f-dob').value);
  formData.append('allergies', document.getElementById('f-allergies').value);
  formData.append('emergency_contact', document.getElementById('f-emergency').value);
  
  const photo = document.getElementById('f-photo').files[0];
  if (photo) formData.append('photo', photo);
  
  if (editId) formData.append('_method', 'PUT');
  
  const url = editId ? `/api/patients/${editId}` : '/api/patients';
  const r = await fetch(url, {
    method: 'POST', // Use POST with _method=PUT for file uploads in Laravel
    headers: h,
    body: formData
  });
  const data = await r.json();
  if (data.success) { closeModal(); loadPatients(); }
  else alert(data.message || JSON.stringify(data.errors));
});

async function deletePatient(id) {
  if (!confirm('Delete this patient?')) return;
  await fetch(`/api/patients/${id}`, {method:'DELETE',headers:h});
  loadPatients();
}

document.getElementById('search-input').addEventListener('input', function() {
  clearTimeout(this._t);
  this._t = setTimeout(() => loadPatients(this.value), 350);
});

loadPatients();
</script>
@endpush
@endsection
