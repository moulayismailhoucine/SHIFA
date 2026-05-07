@extends('layouts.app')
@section('page-title', 'Laboratories')
@section('content')
<div class="card">
  <div class="card-header">
    <span class="card-title"><i class="fas fa-flask" style="color:#f59e0b;margin-right:8px;"></i>Laboratory Registry</span>
    <button class="btn btn-primary btn-sm" onclick="openModal()"><i class="fas fa-plus"></i> Add Laboratory</button>
  </div>
  <div class="card-body">
    <table class="data-table">
      <thead><tr><th>#</th><th>Name</th><th>Specialization</th><th>Phone</th><th>Address</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody id="lab-body"><tr><td colspan="7" style="text-align:center;padding:28px;color:#94a3b8;"><i class="fas fa-spinner fa-spin"></i></td></tr></tbody>
    </table>
  </div>
</div>
<div id="lab-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:500;align-items:center;justify-content:center;">
  <div style="background:white;border-radius:20px;padding:32px;max-width:520px;width:95%;box-shadow:0 24px 80px rgba(0,0,0,0.3);">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
      <h3 id="lab-title" style="font-size:18px;font-weight:700;color:#0f3460;"></h3>
      <button onclick="closeModal()" style="border:none;background:none;font-size:20px;cursor:pointer;">&times;</button>
    </div>
    <form id="lab-form">
      <input type="hidden" id="lab-id">
      <div class="form-grid">
        <div class="form-group"><label class="form-label">Name *</label><input type="text" id="l-name" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Specialization</label><input type="text" id="l-spec" class="form-control"></div>
        <div class="form-group"><label class="form-label">Phone</label><input type="text" id="l-phone" class="form-control"></div>
        <div class="form-group"><label class="form-label">Email *</label><input type="email" id="l-email" class="form-control" required></div>
        <div class="form-group"><label class="form-label">Password *</label><input type="password" id="l-password" class="form-control" placeholder="Login password"></div>
        <div class="form-group"><label class="form-label">License No.</label><input type="text" id="l-license" class="form-control"></div>
        <div class="form-group"><label class="form-label">Work Start</label><input type="time" id="l-start" class="form-control"></div>
        <div class="form-group"><label class="form-label">Work End</label><input type="time" id="l-end" class="form-control"></div>
      </div>
      <div class="form-group"><label class="form-label">Address *</label><input type="text" id="l-address" class="form-control" required></div>
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
async function load(){
  const r=await fetch('/api/laboratories',{headers:h});
  const {data}=await r.json();
  const tb=document.getElementById('lab-body');
  if(!data.length){tb.innerHTML='<tr><td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">No laboratories</td></tr>';return;}
  tb.innerHTML=data.map(l=>`<tr>
    <td style="color:#94a3b8;font-size:12px;">#${l.id}</td>
    <td style="font-weight:600;">${l.name}</td>
    <td><span class="badge badge-warning">${l.specialization||'General'}</span><div style="font-size:11px;color:#94a3b8;">${l.user?.email || l.email || ''}</div></td>
    <td>${l.phone||'—'}</td>
    <td style="color:#64748b;font-size:13px;">${l.address}</td>
    <td><span class="badge ${l.is_active?'badge-success':'badge-danger'}">${l.is_active?'Active':'Inactive'}</span></td>
    <td>
      <button class="btn btn-outline btn-sm" onclick='editL(${JSON.stringify(l)})'><i class="fas fa-edit"></i></button>
      <button class="btn btn-sm" style="background:#fff1f2;color:#e94560;border:none;" onclick="del(${l.id})"><i class="fas fa-trash"></i></button>
    </td>
  </tr>`).join('');
}
function openModal(l=null){
  editId=l?.id||null;
  document.getElementById('lab-title').textContent=editId?'Edit Laboratory':'Add Laboratory';
  document.getElementById('lab-id').value=editId||'';
  document.getElementById('l-name').value=l?.name||'';
  document.getElementById('l-spec').value=l?.specialization||'';
  document.getElementById('l-phone').value=l?.phone||'';
  document.getElementById('l-email').value=l?.email||'';
  document.getElementById('l-license').value=l?.license_number||'';
  document.getElementById('l-address').value=l?.address||'';
  document.getElementById('l-start').value=l?.working_hours_start ? l.working_hours_start.substring(0,5) : '';
  document.getElementById('l-end').value=l?.working_hours_end ? l.working_hours_end.substring(0,5) : '';
  document.getElementById('l-password').value='';
  document.getElementById('l-password').required = !editId;
  document.getElementById('lab-modal').style.display='flex';
}
function closeModal(){document.getElementById('lab-modal').style.display='none';}
function editL(l){openModal(l);}
document.getElementById('lab-form').addEventListener('submit',async function(e){
  e.preventDefault();
  const body={
    name:document.getElementById('l-name').value,
    address:document.getElementById('l-address').value,
    specialization:document.getElementById('l-spec').value,
    phone:document.getElementById('l-phone').value,
    email:document.getElementById('l-email').value,
    license_number:document.getElementById('l-license').value,
    working_hours_start:document.getElementById('l-start').value,
    working_hours_end:document.getElementById('l-end').value,
    password:document.getElementById('l-password').value
  };
  const url=editId?`/api/laboratories/${editId}`:'/api/laboratories';
  const r=await fetch(url,{method:editId?'PUT':'POST',headers:h,body:JSON.stringify(body)});
  const data=await r.json();
  if(data.success){closeModal();load();}else alert(JSON.stringify(data.errors||data.message));
});
async function del(id){if(!confirm('Delete?'))return;await fetch(`/api/laboratories/${id}`,{method:'DELETE',headers:h});load();}
load();
</script>
@endpush
@endsection
