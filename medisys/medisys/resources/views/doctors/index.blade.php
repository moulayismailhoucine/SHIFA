@extends('layouts.app')
@section('page-title', 'Doctors')
@section('content')
<div class="card">
  <div class="card-header">
    <span class="card-title"><i class="fas fa-user-md" style="color:#e94560;margin-right:8px;"></i>Doctor Management</span>
    <button class="btn btn-primary btn-sm" onclick="openModal()"><i class="fas fa-plus"></i> Add Doctor</button>
  </div>
  <div class="card-body">
    <table class="data-table">
      <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Specialty</th><th>Phone</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody id="doctors-body">
        <tr><td colspan="7" style="text-align:center;padding:28px;color:#94a3b8;"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>
      </tbody>
    </table>
  </div>
</div>

<div id="doctor-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;align-items:center;justify-content:center;">
  <div style="background:white;border-radius:20px;padding:32px;max-width:520px;width:95%;box-shadow:0 24px 80px rgba(0,0,0,0.3);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
      <h3 id="modal-title" style="font-size:18px;font-weight:700;color:#0f3460;"></h3>
      <button onclick="closeModal()" style="border:none;background:none;font-size:20px;cursor:pointer;color:#94a3b8;">&times;</button>
    </div>
    <form id="doctor-form">
      <input type="hidden" id="doctor-id">
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Full Name *</label><input type="text" id="f-name" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Email *</label><input type="email" id="f-email" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Password</label><input type="password" id="f-password" class="form-control" placeholder="Leave blank to keep"></div>
        <div class="form-group"><label class="form-label">Specialty *</label><input type="text" id="f-specialty" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Phone</label><input type="text" id="f-phone" class="form-control"></div>
        <div class="form-group"><label class="form-label">License No.</label><input type="text" id="f-license" class="form-control"></div>
        <div class="form-group"><label class="form-label">Profile Photo</label><input type="file" id="f-photo" class="form-control" accept="image/*"></div>
      </div>
      <div class="form-group"><label class="form-label">Bio</label><textarea id="f-bio" class="form-control" rows="2"></textarea></div>
      <h4 style="margin-top:20px;margin-bottom:10px;font-size:14px;color:#64748b;">Scheduling Settings</h4>
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Working Days</label>
          <div style="display:flex;gap:10px;flex-wrap:wrap;font-size:13px;" id="f-days-container">
            <label><input type="checkbox" value="Sunday" class="f-day"> Sun</label>
            <label><input type="checkbox" value="Monday" class="f-day"> Mon</label>
            <label><input type="checkbox" value="Tuesday" class="f-day"> Tue</label>
            <label><input type="checkbox" value="Wednesday" class="f-day"> Wed</label>
            <label><input type="checkbox" value="Thursday" class="f-day"> Thu</label>
            <label><input type="checkbox" value="Friday" class="f-day"> Fri</label>
            <label><input type="checkbox" value="Saturday" class="f-day"> Sat</label>
          </div>
        </div>
        <div class="form-group"><label class="form-label">Treatment Time (mins)</label><input type="number" id="f-treatment" class="form-control" value="30"></div>
        <div class="form-group"><label class="form-label">Start Time</label><input type="time" id="f-start" class="form-control"></div>
        <div class="form-group"><label class="form-label">End Time</label><input type="time" id="f-end" class="form-control"></div>
      </div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:10px;">
        <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
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

async function loadDoctors() {
  const r = await fetch('/api/doctors', {headers: h});
  const {data} = await r.json();
  const tb = document.getElementById('doctors-body');
  if (!data || !data.length) { tb.innerHTML='<tr><td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">No doctors found</td></tr>'; return; }
  tb.innerHTML = data.map((d,i) => `<tr>
    <td style="color:#94a3b8;font-size:12px;">#${d.id}</td>
    <td>
      <div style="display:flex;align-items:center;gap:10px;">
        ${d.photo ? `<img src="${d.photo}" style="width:32px;height:32px;border-radius:8px;object-fit:cover;">` : `<div class="avatar" style="background:${colors[i%5]}">${d.name.charAt(0)}</div>`}
        <span style="font-weight:600;">${d.name}</span>
      </div>
    </td>
    <td>${d.email}</td>
    <td><span class="badge badge-purple">${d.specialty}</span></td>
    <td>${d.phone||'—'}</td>
    <td><span class="badge ${d.is_active?'badge-success':'badge-danger'}">${d.is_active?'Active':'Inactive'}</span></td>
    <td>
      <button class="btn btn-outline btn-sm" onclick="editDoctor(${d.id})"><i class="fas fa-edit"></i></button>
      <button class="btn btn-sm" style="background:#fff1f2;color:#e94560;border:none;" onclick="deleteDoctor(${d.id})"><i class="fas fa-trash"></i></button>
    </td>
  </tr>`).join('');
}

function openModal(doc=null) {
  editId = doc?.id||null;
  document.getElementById('modal-title').textContent = editId ? 'Edit Doctor' : 'Add New Doctor';
  ['name','email','specialty','phone','license_number','bio'].forEach(k => {
    const el = document.getElementById('f-'+k.replace('_number','').replace('_',''));
    if (el) el.value = doc?.[k]||'';
  });
  document.getElementById('f-name').value = doc?.name||'';
  document.getElementById('f-email').value = doc?.email||'';
  document.getElementById('f-specialty').value = doc?.specialty||'';
  document.getElementById('f-phone').value = doc?.phone||'';
  document.getElementById('f-license').value = doc?.license_number||'';
  document.getElementById('f-bio').value = doc?.bio||'';
  document.getElementById('f-treatment').value = doc?.treatment_time||'30';
  document.getElementById('f-start').value = doc?.working_hours_start||'08:00';
  document.getElementById('f-end').value = doc?.working_hours_end||'16:00';
  const docDays = doc?.working_days || [];
  document.querySelectorAll('.f-day').forEach(cb => { cb.checked = docDays.includes(cb.value); });
  document.getElementById('f-password').value = '';
  document.getElementById('f-photo').value = '';
  document.getElementById('doctor-modal').style.display = 'flex';
}
function closeModal() { document.getElementById('doctor-modal').style.display = 'none'; }

async function editDoctor(id) {
  const r = await fetch(`/api/doctors/${id}`, {headers: h});
  const {data} = await r.json();
  openModal(data);
}

document.getElementById('doctor-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const formData = new FormData();
  formData.append('name', document.getElementById('f-name').value);
  formData.append('email', document.getElementById('f-email').value);
  formData.append('specialty', document.getElementById('f-specialty').value);
  formData.append('phone', document.getElementById('f-phone').value);
  formData.append('license_number', document.getElementById('f-license').value);
  formData.append('bio', document.getElementById('f-bio').value);
  formData.append('treatment_time', document.getElementById('f-treatment').value);
  formData.append('working_hours_start', document.getElementById('f-start').value);
  formData.append('working_hours_end', document.getElementById('f-end').value);
  
  // working_days array
  document.querySelectorAll('.f-day:checked').forEach(cb => {
    formData.append('working_days[]', cb.value);
  });
  
  const pw = document.getElementById('f-password').value;
  if (pw) formData.append('password', pw);
  if (!editId && !pw) formData.append('password', 'Doctor@1234');
  
  const photo = document.getElementById('f-photo').files[0];
  if (photo) formData.append('photo', photo);
  
  if (editId) formData.append('_method', 'PUT');
  
  const url = editId ? `/api/doctors/${editId}` : '/api/doctors';
  const r = await fetch(url, {
    method: 'POST', // Use POST with _method=PUT for file uploads in Laravel
    headers: h,
    body: formData
  });
  const data = await r.json();
  if (data.success) { closeModal(); loadDoctors(); }
  else alert(JSON.stringify(data.errors || data.message));
});

async function deleteDoctor(id) {
  if (!confirm('Delete this doctor?')) return;
  await fetch(`/api/doctors/${id}`, {method:'DELETE',headers:h});
  loadDoctors();
}

loadDoctors();
</script>
@endpush
@endsection
