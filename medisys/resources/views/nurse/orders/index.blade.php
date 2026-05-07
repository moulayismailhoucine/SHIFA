@extends('layouts.app')
@section('page-title', 'Nursing Orders')
@section('content')
<style>
.order-topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;}
.order-topbar h2{font-size:20px;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:10px;}
.btn-create{background:linear-gradient(135deg,#10b981,#059669);color:white;padding:10px 20px;border-radius:12px;border:none;cursor:pointer;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;transition:all 0.2s;}
.btn-create:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(16,185,129,0.4);}
.filter-bar{display:flex;gap:10px;margin-bottom:20px;flex-wrap:wrap;}
.filter-btn{padding:7px 18px;border-radius:20px;border:1.5px solid #e2e8f0;background:white;color:#64748b;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.2s;}
.filter-btn.active,.filter-btn:hover{border-color:#10b981;background:#ecfdf5;color:#059669;}
.orders-grid{display:flex;flex-direction:column;gap:12px;}
.order-card{background:white;border-radius:16px;box-shadow:0 2px 10px rgba(0,0,0,0.07);padding:0;overflow:hidden;border-left:5px solid #e2e8f0;transition:all 0.2s;}
.order-card:hover{transform:translateY(-2px);box-shadow:0 4px 20px rgba(0,0,0,0.1);}
.order-card.Pending{border-left-color:#f59e0b;}
.order-card.Ongoing{border-left-color:#3b82f6;}
.order-card.Completed{border-left-color:#10b981;}
.order-inner{padding:18px 20px;}
.order-top{display:flex;align-items:flex-start;gap:14px;}
.order-type-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
.order-type-icon.Medication{background:#dbeafe;color:#2563eb;}
.order-type-icon.Monitoring{background:#ede9fe;color:#7c3aed;}
.order-type-icon.Care{background:#d1fae5;color:#059669;}
.order-meta{flex:1;min-width:0;}
.order-patient{font-weight:700;font-size:15px;color:#1e293b;}
.order-type-lbl{font-size:12px;color:#64748b;margin-bottom:6px;}
.order-details{display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;}
.order-chip{background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:4px 10px;font-size:12px;color:#374151;display:flex;align-items:center;gap:5px;}
.status-badge{padding:4px 14px;border-radius:20px;font-size:11px;font-weight:700;}
.status-badge.Pending{background:#fef3c7;color:#92400e;}
.status-badge.Ongoing{background:#dbeafe;color:#1e40af;}
.status-badge.Completed{background:#d1fae5;color:#065f46;}
.order-actions{display:flex;gap:8px;margin-top:14px;padding-top:14px;border-top:1px solid #f1f5f9;}
.btn-sm{padding:6px 14px;border-radius:8px;border:1.5px solid;font-size:12px;font-weight:600;cursor:pointer;transition:all 0.2s;}
.btn-complete{background:#d1fae5;border-color:#6ee7b7;color:#065f46;}
.btn-complete:hover{background:#10b981;color:white;}
.btn-ongoing{background:#dbeafe;border-color:#93c5fd;color:#1e40af;}
.btn-ongoing:hover{background:#3b82f6;color:white;}
.btn-delete{background:#fee2e2;border-color:#fca5a5;color:#991b1b;}
.btn-delete:hover{background:#ef4444;color:white;}
.doctor-chip{display:flex;align-items:center;gap:5px;font-size:12px;color:#64748b;}
.empty-orders{text-align:center;padding:60px;color:#94a3b8;}
.empty-orders i{font-size:48px;display:block;margin-bottom:12px;opacity:0.4;}

/* Modal */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:999;align-items:center;justify-content:center;}
.modal-bg.open{display:flex;}
.modal-box{background:white;border-radius:20px;width:100%;max-width:560px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,0.2);margin:20px;}
.modal-hd{background:linear-gradient(135deg,#10b981,#059669);padding:20px 24px;color:white;display:flex;justify-content:space-between;align-items:center;}
.modal-hd h3{margin:0;font-size:17px;font-weight:700;display:flex;align-items:center;gap:10px;}
.modal-close{background:none;border:none;color:white;font-size:22px;cursor:pointer;opacity:0.8;line-height:1;}
.modal-body{padding:24px;}
.fg{margin-bottom:16px;}
.fg label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
.fg input,.fg select,.fg textarea{width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;color:#1e293b;font-family:inherit;transition:border 0.2s;}
.fg input:focus,.fg select:focus,.fg textarea:focus{outline:none;border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,0.1);}
.fg-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;}
.modal-footer{padding:16px 24px;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end;gap:10px;}
.btn-cancel{padding:10px 20px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;color:#64748b;font-size:14px;font-weight:600;cursor:pointer;}
.btn-submit{padding:10px 24px;border-radius:10px;background:linear-gradient(135deg,#10b981,#059669);color:white;border:none;font-size:14px;font-weight:700;cursor:pointer;}
.toast{position:fixed;bottom:24px;right:24px;background:#1e293b;color:white;padding:12px 20px;border-radius:12px;font-size:14px;z-index:9999;transform:translateY(80px);opacity:0;transition:all 0.3s;}
.toast.show{transform:translateY(0);opacity:1;}
</style>

<div class="order-topbar">
  <h2><i class="fas fa-clipboard-list" style="color:#10b981;"></i> Nursing Orders</h2>
  <button class="btn-create" onclick="openModal()"><i class="fas fa-plus"></i> New Order</button>
</div>

<div class="filter-bar">
  <button class="filter-btn active" onclick="filterOrders('All',this)">All</button>
  <button class="filter-btn" onclick="filterOrders('Pending',this)">⏳ Pending</button>
  <button class="filter-btn" onclick="filterOrders('Ongoing',this)">🔄 Ongoing</button>
  <button class="filter-btn" onclick="filterOrders('Completed',this)">✅ Completed</button>
</div>

<div class="orders-grid" id="orders-grid">
  <div class="empty-orders"><i class="fas fa-spinner fa-spin"></i> Loading orders...</div>
</div>

{{-- Create Order Modal --}}
<div class="modal-bg" id="createModal">
  <div class="modal-box">
    <div class="modal-hd">
      <h3><i class="fas fa-plus-circle"></i> New Nursing Order</h3>
      <button class="modal-close" onclick="closeModal()">×</button>
    </div>
    <form id="orderForm" onsubmit="submitOrder(event)">
      <div class="modal-body">
        <div class="fg">
          <label>Patient *</label>
          <select id="f-patient" required>
            <option value="">Select patient...</option>
          </select>
        </div>
        <div class="fg-row">
          <div class="fg">
            <label>Order Type *</label>
            <select id="f-type" required>
              <option value="">Select type...</option>
              <option value="Medication">💊 Medication</option>
              <option value="Monitoring">👁 Monitoring</option>
              <option value="Care Procedure">🤲 Care Procedure</option>
            </select>
          </div>
          <div class="fg">
            <label>Admin. Method</label>
            <select id="f-method">
              <option value="">N/A</option>
              <option>Oral</option><option>IV</option><option>Injection</option>
              <option>Topical</option><option>Inhalation</option><option>Sublingual</option>
            </select>
          </div>
        </div>
        <div class="fg">
          <label>Schedule / Frequency</label>
          <input id="f-schedule" type="text" placeholder="e.g. Every 8 hours, Once daily at 8AM">
        </div>
        <div class="fg">
          <label>Notes / Instructions</label>
          <textarea id="f-notes" rows="3" placeholder="Additional instructions for this order..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Create Order</button>
      </div>
    </form>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
const token = localStorage.getItem('auth_token') || '';
const apiH  = { 'Accept':'application/json','Content-Type':'application/json','Authorization':'Bearer '+token };
let allOrders = [];
let currentFilter = 'All';

const typeIcon = { 'Medication':'pills', 'Monitoring':'eye', 'Care Procedure':'hand-holding-medical' };
const typeClass = { 'Medication':'Medication', 'Monitoring':'Monitoring', 'Care Procedure':'Care' };

async function loadOrders() {
  try {
    const r = await fetch('/api/nursing-orders', { headers: apiH });
    const d = await r.json();
    if (!d.success) { showOrders([]); return; }
    allOrders = d.data || [];
    showOrders(allOrders);
  } catch(e) { document.getElementById('orders-grid').innerHTML = '<div class="empty-orders"><i class="fas fa-exclamation-triangle"></i> Failed to load orders</div>'; }
}

function showOrders(orders) {
  const filtered = currentFilter === 'All' ? orders : orders.filter(o => o.status === currentFilter);
  const grid = document.getElementById('orders-grid');
  if (!filtered.length) {
    grid.innerHTML = '<div class="empty-orders"><i class="fas fa-clipboard-check"></i><br>No orders found</div>'; return;
  }
  grid.innerHTML = filtered.map(o => `
    <div class="order-card ${o.status}" id="order-${o.id}">
      <div class="order-inner">
        <div class="order-top">
          <div class="order-type-icon ${typeClass[o.type]||'Medication'}">
            <i class="fas fa-${typeIcon[o.type]||'clipboard'}"></i>
          </div>
          <div class="order-meta">
            <div class="order-patient">${o.patient?.name||'Unknown Patient'}</div>
            <div class="order-type-lbl">${o.type}</div>
            <div class="order-details">
              ${o.dosage_method?`<span class="order-chip"><i class="fas fa-syringe"></i>${o.dosage_method}</span>`:''}
              ${o.schedule?`<span class="order-chip"><i class="fas fa-clock"></i>${o.schedule}</span>`:''}
              ${o.notes?`<span class="order-chip"><i class="fas fa-comment-medical"></i>${o.notes.substring(0,40)}${o.notes.length>40?'...':''}</span>`:''}
            </div>
            ${o.doctor?.name?`<div class="doctor-chip" style="margin-top:6px;"><i class="fas fa-user-md"></i> Dr. ${o.doctor.name}</div>`:''}
          </div>
          <span class="status-badge ${o.status}">${o.status}</span>
        </div>
        <div class="order-actions">
          ${o.status!=='Completed'?`<button class="btn-sm btn-complete" onclick="updateStatus(${o.id},'Completed')"><i class="fas fa-check"></i> Complete</button>`:''}
          ${o.status==='Pending'?`<button class="btn-sm btn-ongoing" onclick="updateStatus(${o.id},'Ongoing')"><i class="fas fa-play"></i> Start</button>`:''}
          <button class="btn-sm btn-delete" onclick="deleteOrder(${o.id})"><i class="fas fa-trash"></i> Delete</button>
        </div>
      </div>
    </div>`).join('');
}

function filterOrders(status, btn) {
  currentFilter = status;
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  showOrders(allOrders);
}

async function updateStatus(id, status) {
  try {
    const r = await fetch(`/api/nursing-orders/${id}`, {
      method: 'PUT',
      headers: apiH,
      body: JSON.stringify({ status })
    });
    const d = await r.json();
    if (d.success) { showToast('Order updated to ' + status); loadOrders(); }
  } catch(e) { showToast('Update failed', true); }
}

async function deleteOrder(id) {
  if (!confirm('Delete this nursing order?')) return;
  try {
    const r = await fetch(`/api/nursing-orders/${id}`, { method: 'DELETE', headers: apiH });
    const d = await r.json();
    if (d.success) { showToast('Order deleted'); loadOrders(); }
  } catch(e) { showToast('Delete failed', true); }
}

async function loadPatients() {
  try {
    const r = await fetch('/api/patients', { headers: apiH });
    const d = await r.json();
    const sel = document.getElementById('f-patient');
    (d.data||[]).forEach(p => {
      const o = document.createElement('option');
      o.value = p.id; o.textContent = p.name;
      sel.appendChild(o);
    });
  } catch(e) {}
}

function openModal() {
  document.getElementById('createModal').classList.add('open');
  loadPatients();
}
function closeModal() {
  document.getElementById('createModal').classList.remove('open');
  document.getElementById('orderForm').reset();
}

async function submitOrder(e) {
  e.preventDefault();
  const body = {
    patient_id: document.getElementById('f-patient').value,
    type: document.getElementById('f-type').value,
    dosage_method: document.getElementById('f-method').value,
    schedule: document.getElementById('f-schedule').value,
    notes: document.getElementById('f-notes').value,
  };
  try {
    const r = await fetch('/api/nursing-orders', { method:'POST', headers:apiH, body:JSON.stringify(body) });
    const d = await r.json();
    if (d.success) { closeModal(); showToast('Order created successfully!'); loadOrders(); }
    else showToast(d.message||'Error creating order', true);
  } catch(e) { showToast('Network error', true); }
}

function showToast(msg, err=false) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.style.background = err ? '#ef4444' : '#1e293b';
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

loadOrders();
</script>
@endsection
