@extends('layouts.app')
@section('page-title', 'Pharmacies')
@section('content')
<div class="card">
  <div class="card-header">
    <span class="card-title"><i class="fas fa-pills" style="color:#10b981;margin-right:8px;"></i>Pharmacy Registry</span>
    <button class="btn btn-primary btn-sm" onclick="openModal()"><i class="fas fa-plus"></i> Add Pharmacy</button>
  </div>
  <div class="card-body">
    <table class="data-table">
      <thead><tr><th>#</th><th>Name</th><th>Address</th><th>Phone</th><th>Manager</th><th>License</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody id="ph-body"><tr><td colspan="8" style="text-align:center;padding:28px;color:#94a3b8;"><i class="fas fa-spinner fa-spin"></i></td></tr></tbody>
    </table>
  </div>
</div>
<div id="ph-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;align-items:center;justify-content:center;">
  <div style="background:white;border-radius:20px;padding:32px;max-width:520px;width:95%;box-shadow:0 24px 80px rgba(0,0,0,0.3);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
      <h3 id="ph-title" style="font-size:18px;font-weight:700;color:#0f3460;"></h3>
      <button onclick="closeModal()" style="border:none;background:none;font-size:20px;cursor:pointer;color:#94a3b8;">&times;</button>
    </div>
    <form id="ph-form">
      <input type="hidden" id="ph-id">
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Name *</label><input type="text" id="ph-name" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Phone</label><input type="text" id="ph-phone" class="form-control"></div>
        <div class="form-group"><label class="form-label">Email *</label><input type="email" id="ph-email" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Password *</label><input type="password" id="ph-password" class="form-control" placeholder="Login password"></div>
        <div class="form-group"><label class="form-label">Manager</label><input type="text" id="ph-manager" class="form-control"></div>
        <div class="form-group"><label class="form-label">License No.</label><input type="text" id="ph-license" class="form-control"></div>
        <div class="form-group"><label class="form-label">Work Start</label><input type="time" id="ph-start" class="form-control"></div>
        <div class="form-group"><label class="form-label">Work End</label><input type="time" id="ph-end" class="form-control"></div>
      </div>
      <div class="form-group"><label class="form-label">Address *</label><input type="text" id="ph-address" class="form-control" required></div>
      <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:8px;">
        <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
      </div>
    </form>
  </div>
</div>
@push('scripts')
<script>
const token=localStorage.getItem('auth_token')||'';
const h={'Content-Type':'application/json','Accept':'application/json','Authorization':'Bearer '+token};
let editId=null;
async function load() {
  const r=await fetch('/api/pharmacies',{headers:h});
  const {data}=await r.json();
  const tb=document.getElementById('ph-body');
  if(!data.length){tb.innerHTML='<tr><td colspan="8" style="text-align:center;padding:24px;color:#94a3b8;">No pharmacies</td></tr>';return;}
  tb.innerHTML=data.map((p,i)=>`<tr>
    <td style="color:#94a3b8;font-size:12px;">#${p.id}</td>
    <td style="font-weight:600;">${p.name}</td>
    <td style="color:#64748b;font-size:13px;">${p.address}<div style="font-size:11px;color:#94a3b8;">${p.user?.email || p.email || ''}</div></td>
    <td>${p.phone||'—'}</td>
    <td>${p.manager_name||'—'}</td>
    <td><span class="badge badge-purple">${p.license_number||'—'}</span></td>
    <td><span class="badge ${p.is_active?'badge-success':'badge-danger'}">${p.is_active?'Active':'Inactive'}</span></td>
    <td>
      <button class="btn btn-outline btn-sm" onclick='editP(${JSON.stringify(p)})'><i class="fas fa-edit"></i></button>
      <button class="btn btn-sm" style="background:#fff1f2;color:#e94560;border:none;" onclick="del(${p.id})"><i class="fas fa-trash"></i></button>
    </td>
  </tr>`).join('');
}
function openModal(p=null){
  editId=p?.id||null;
  document.getElementById('ph-title').textContent=editId?'Edit Pharmacy':'Add Pharmacy';
  document.getElementById('ph-id').value=editId||'';
  document.getElementById('ph-name').value=p?.name||'';
  document.getElementById('ph-address').value=p?.address||'';
  document.getElementById('ph-phone').value=p?.phone||'';
  document.getElementById('ph-email').value=p?.email||'';
  document.getElementById('ph-manager').value=p?.manager_name||'';
  document.getElementById('ph-license').value=p?.license_number||'';
  document.getElementById('ph-start').value=p?.working_hours_start ? p.working_hours_start.substring(0,5) : '';
  document.getElementById('ph-end').value=p?.working_hours_end ? p.working_hours_end.substring(0,5) : '';
  document.getElementById('ph-password').value='';
  document.getElementById('ph-password').required = !editId;
  document.getElementById('ph-modal').style.display='flex';
}
function closeModal(){document.getElementById('ph-modal').style.display='none';}
function editP(p){openModal(p);}
document.getElementById('ph-form').addEventListener('submit',async function(e){
  e.preventDefault();
  const body={
    name:document.getElementById('ph-name').value,
    address:document.getElementById('ph-address').value,
    phone:document.getElementById('ph-phone').value,
    email:document.getElementById('ph-email').value,
    manager_name:document.getElementById('ph-manager').value,
    license_number:document.getElementById('ph-license').value,
    working_hours_start:document.getElementById('ph-start').value,
    working_hours_end:document.getElementById('ph-end').value,
    password:document.getElementById('ph-password').value
  };
  const url=editId?`/api/pharmacies/${editId}`:'/api/pharmacies';
  const r=await fetch(url,{method:editId?'PUT':'POST',headers:h,body:JSON.stringify(body)});
  const data=await r.json();
  if(data.success){closeModal();load();}else alert(JSON.stringify(data.errors||data.message));
});
async function del(id){if(!confirm('Delete?'))return;await fetch(`/api/pharmacies/${id}`,{method:'DELETE',headers:h});load();}
load();
</script>
@endpush
@endsection
