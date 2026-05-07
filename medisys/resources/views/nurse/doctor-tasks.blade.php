@extends('layouts.app')
@section('page-title', 'Nursing Task Management')
@section('content')
<style>
.dt-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;}
.dt-header h2{font-size:20px;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:10px;}
.btn-new{background:linear-gradient(135deg,#0f3460,#533483);color:white;padding:10px 20px;border-radius:12px;border:none;cursor:pointer;font-size:14px;font-weight:600;display:flex;align-items:center;gap:8px;transition:all 0.2s;}
.btn-new:hover{transform:translateY(-2px);box-shadow:0 4px 14px rgba(15,52,96,0.4);}
/* Tabs */
.tabs{display:flex;gap:4px;background:#f1f5f9;border-radius:12px;padding:4px;margin-bottom:20px;width:fit-content;}
.tab-btn{padding:8px 20px;border-radius:10px;border:none;background:transparent;color:#64748b;font-size:13px;font-weight:600;cursor:pointer;transition:all 0.2s;}
.tab-btn.active{background:white;color:#0f3460;box-shadow:0 2px 6px rgba(0,0,0,0.08);}
.tab-pane{display:none;} .tab-pane.active{display:block;}
/* Task list */
.task-row{background:white;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,0.06);padding:0;overflow:hidden;border-left:5px solid #e2e8f0;margin-bottom:10px;transition:all 0.2s;}
.task-row:hover{box-shadow:0 4px 16px rgba(0,0,0,0.1);transform:translateY(-1px);}
.task-row.Pending{border-color:#f59e0b;} .task-row.Ongoing{border-color:#3b82f6;} .task-row.Completed{border-color:#10b981;}
.task-inner{padding:16px 18px;display:flex;gap:14px;align-items:flex-start;}
.t-icon{width:42px;height:42px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
.t-icon.med{background:#dbeafe;color:#2563eb;} .t-icon.mon{background:#ede9fe;color:#7c3aed;} .t-icon.care{background:#d1fae5;color:#059669;}
.t-info{flex:1;min-width:0;}
.t-top{display:flex;align-items:center;gap:10px;margin-bottom:4px;flex-wrap:wrap;}
.t-patient{font-weight:700;font-size:14px;color:#1e293b;}
.t-type{font-size:12px;color:#64748b;}
.t-nurse{font-size:11px;color:#8b5cf6;display:flex;align-items:center;gap:4px;}
.t-details{display:flex;flex-wrap:wrap;gap:8px;margin-top:6px;}
.chip{background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;padding:3px 9px;font-size:11px;color:#374151;display:flex;align-items:center;gap:4px;}
.t-result{background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:8px 12px;margin-top:8px;font-size:12px;color:#14532d;}
.t-right{display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0;}
.badge{padding:3px 11px;border-radius:20px;font-size:10px;font-weight:700;}
.badge.Pending{background:#fef3c7;color:#92400e;} .badge.Ongoing{background:#dbeafe;color:#1e40af;} .badge.Completed{background:#d1fae5;color:#065f46;}
.badge.overdue{background:#fee2e2;color:#991b1b;}
.btn-sm{padding:5px 12px;border-radius:8px;border:1.5px solid #e2e8f0;background:white;font-size:11px;font-weight:600;cursor:pointer;transition:all 0.2s;}
.btn-sm.del:hover{background:#fee2e2;border-color:#fca5a5;color:#dc2626;}
/* Nurse activity */
.nurse-card{background:white;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,0.06);overflow:hidden;margin-bottom:12px;}
.nurse-card-head{background:linear-gradient(135deg,#533483,#7c3aed);padding:14px 18px;color:white;display:flex;align-items:center;gap:12px;}
.nurse-ava{width:40px;height:40px;border-radius:12px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;}
.nurse-stats{display:flex;gap:16px;padding:14px 18px;border-bottom:1px solid #f1f5f9;}
.nurse-stat{text-align:center;}
.nurse-stat-val{font-size:22px;font-weight:800;color:#1e293b;}
.nurse-stat-lbl{font-size:10px;color:#64748b;text-transform:uppercase;}
/* Audit log */
.log-row{display:flex;gap:12px;padding:11px 18px;border-bottom:1px solid #f8fafc;align-items:flex-start;}
.log-row:last-child{border-bottom:none;}
.log-dot{width:10px;height:10px;border-radius:50%;margin-top:4px;flex-shrink:0;}
.log-dot.task_Completed{background:#10b981;} .log-dot.task_Ongoing{background:#3b82f6;} .log-dot.task_Pending{background:#f59e0b;}
.log-dot.task_assigned{background:#8b5cf6;} .log-dot.vitals_recorded{background:#ef4444;}
.log-action{font-size:12px;font-weight:600;color:#374151;}
.log-detail{font-size:11px;color:#64748b;}
.log-time{font-size:10px;color:#94a3b8;margin-left:auto;flex-shrink:0;}
/* Modal */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:999;align-items:center;justify-content:center;}
.modal-bg.open{display:flex;}
.modal-box{background:white;border-radius:18px;width:100%;max-width:600px;overflow:hidden;margin:16px;box-shadow:0 20px 60px rgba(0,0,0,0.25);max-height:92vh;overflow-y:auto;}
.mhd{padding:18px 22px;background:linear-gradient(135deg,#0f3460,#533483);color:white;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:1;}
.mhd h3{margin:0;font-size:16px;font-weight:700;display:flex;align-items:center;gap:8px;}
.mc{background:none;border:none;color:white;font-size:22px;cursor:pointer;}
.mbody{padding:20px 22px;}
.fg{margin-bottom:14px;}
.fg label{display:block;font-size:11px;font-weight:700;color:#374151;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;}
.fg input,.fg select,.fg textarea{width:100%;padding:9px 13px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-family:inherit;color:#1e293b;transition:border 0.2s;}
.fg input:focus,.fg select:focus,.fg textarea:focus{outline:none;border-color:#0f3460;box-shadow:0 0 0 3px rgba(15,52,96,0.1);}
.fg-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.mfoot{padding:14px 22px;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end;gap:10px;position:sticky;bottom:0;background:white;}
.btn-cancel{padding:9px 18px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;color:#64748b;font-size:13px;font-weight:600;cursor:pointer;}
.btn-submit{padding:9px 20px;border-radius:10px;background:linear-gradient(135deg,#0f3460,#533483);color:white;border:none;font-size:13px;font-weight:700;cursor:pointer;}
.empty-msg{padding:32px;text-align:center;color:#94a3b8;}
.empty-msg i{font-size:32px;display:block;margin-bottom:8px;opacity:0.4;}
.toast{position:fixed;bottom:24px;right:24px;background:#1e293b;color:white;padding:12px 20px;border-radius:12px;font-size:13px;z-index:9999;transform:translateY(80px);opacity:0;transition:all 0.3s;}
.toast.show{transform:translateY(0);opacity:1;}
.section-title{font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;display:flex;align-items:center;gap:6px;}
</style>

<div class="dt-header">
  <h2><i class="fas fa-clipboard-list" style="color:#0f3460;"></i> Nursing Task Management</h2>
  <button class="btn-new" onclick="openModal()"><i class="fas fa-plus"></i> Assign New Task</button>
</div>

<div class="tabs">
  <button class="tab-btn active" onclick="switchTab('tasks',this)"><i class="fas fa-tasks"></i> Tasks</button>
  <button class="tab-btn" onclick="switchTab('nurses',this)"><i class="fas fa-user-nurse"></i> My Nurses</button>
  <button class="tab-btn" onclick="switchTab('audit',this)"><i class="fas fa-shield-alt"></i> Audit Log</button>
</div>

{{-- Tasks Tab --}}
<div class="tab-pane active" id="tab-tasks">
  <div style="display:flex;gap:8px;margin-bottom:14px;flex-wrap:wrap;">
    <button class="btn-sm active" onclick="filterTasks('All',this)" style="border-color:#0f3460;color:#0f3460;">All</button>
    <button class="btn-sm" onclick="filterTasks('Pending',this)">⏳ Pending</button>
    <button class="btn-sm" onclick="filterTasks('Ongoing',this)">🔄 Ongoing</button>
    <button class="btn-sm" onclick="filterTasks('Completed',this)">✅ Completed</button>
  </div>
  <div id="tasks-list"><div class="empty-msg"><i class="fas fa-spinner fa-spin"></i> Loading tasks...</div></div>
</div>

{{-- Nurses Tab --}}
<div class="tab-pane" id="tab-nurses">
  <div id="nurses-list"><div class="empty-msg"><i class="fas fa-spinner fa-spin"></i></div></div>
</div>

{{-- Audit Log Tab --}}
<div class="tab-pane" id="tab-audit">
  <div id="audit-list"><div class="empty-msg"><i class="fas fa-spinner fa-spin"></i></div></div>
</div>

{{-- Assign Task Modal --}}
<div class="modal-bg" id="assignModal">
  <div class="modal-box">
    <div class="mhd">
      <h3><i class="fas fa-plus-circle"></i> Assign Nursing Task</h3>
      <button class="mc" onclick="closeModal()">×</button>
    </div>
    <form id="assignForm" onsubmit="submitTask(event)">
      <div class="mbody">
        <div class="fg-row">
          <div class="fg">
            <label>Patient *</label>
            <select id="f-patient" required><option value="">Select patient...</option></select>
          </div>
          <div class="fg">
            <label>Assign to Nurse *</label>
            <select id="f-nurse" required><option value="">Select nurse...</option></select>
          </div>
        </div>
        <div class="fg-row">
          <div class="fg">
            <label>Task Type *</label>
            <select id="f-type" required>
              <option value="">Select...</option>
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
          <label>Detailed Instructions</label>
          <textarea id="f-instructions" rows="3" placeholder="Specific instructions for the nurse..."></textarea>
        </div>
        <div class="section-title"><i class="fas fa-clock"></i> Schedule</div>
        <div class="fg-row">
          <div class="fg">
            <label>Specific Times (comma-separated)</label>
            <input id="f-times" type="text" placeholder="08:00,14:00,20:00">
          </div>
          <div class="fg">
            <label>Or: Every X Hours</label>
            <input id="f-interval" type="number" min="1" max="24" placeholder="e.g. 8">
          </div>
        </div>
        <div class="fg-row">
          <div class="fg">
            <label>Start Date</label>
            <input id="f-start" type="date">
          </div>
          <div class="fg">
            <label>End Date</label>
            <input id="f-end" type="date">
          </div>
        </div>
        <div class="fg">
          <label>General Notes</label>
          <textarea id="f-notes" rows="2" placeholder="Additional notes..."></textarea>
        </div>
      </div>
      <div class="mfoot">
        <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Assign Task</button>
      </div>
    </form>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
const token = localStorage.getItem('auth_token') || '';
const H = { 'Accept':'application/json','Content-Type':'application/json','Authorization':'Bearer '+token };
let allTasks = [];
let filterStatus = 'All';
const typeIcon  = { 'Medication':'pills','Monitoring':'eye','Care Procedure':'hand-holding-medical' };
const typeClass = { 'Medication':'med','Monitoring':'mon','Care Procedure':'care' };

// ── Tabs ──
function switchTab(name, btn) {
  document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('tab-'+name).classList.add('active');
  btn.classList.add('active');
  if (name === 'nurses') loadNurses();
  if (name === 'audit')  loadAuditLog();
}

// ── Tasks ──
async function loadTasks() {
  try {
    const r = await fetch('/api/nursing-orders', { headers: H });
    const d = await r.json();
    allTasks = d.data || [];
    renderTasks();
  } catch(e) { document.getElementById('tasks-list').innerHTML = '<div class="empty-msg"><i class="fas fa-times"></i> Failed to load</div>'; }
}

function renderTasks() {
  const list = filterStatus === 'All' ? allTasks : allTasks.filter(t => t.status === filterStatus);
  const el = document.getElementById('tasks-list');
  if (!list.length) { el.innerHTML = '<div class="empty-msg"><i class="fas fa-clipboard-check"></i><br>No tasks found</div>'; return; }
  el.innerHTML = list.map(t => `
    <div class="task-row ${t.status}">
      <div class="task-inner">
        <div class="t-icon ${typeClass[t.type]||'med'}"><i class="fas fa-${typeIcon[t.type]||'clipboard'}"></i></div>
        <div class="t-info">
          <div class="t-top">
            <span class="t-patient">${esc(t.patient?.name||'?')}</span>
            <span class="t-type">${esc(t.type)}</span>
          </div>
          <div class="t-nurse"><i class="fas fa-user-nurse"></i> ${esc(t.nurse?.name||'Unassigned')}</div>
          <div class="t-details">
            ${t.dosage_method?`<span class="chip"><i class="fas fa-syringe"></i>${esc(t.dosage_method)}</span>`:''}
            ${t.scheduled_time?`<span class="chip"><i class="fas fa-clock"></i>${esc(t.scheduled_time)}</span>`:''}
            ${t.start_date?`<span class="chip"><i class="fas fa-calendar"></i>${esc(t.start_date)} → ${esc(t.end_date||'?')}</span>`:''}
            ${t.interval_hours?`<span class="chip"><i class="fas fa-redo"></i>Every ${t.interval_hours}h</span>`:''}
          </div>
          ${t.instructions?`<div style="font-size:12px;color:#64748b;margin-top:5px;">${esc(t.instructions.substring(0,100))}${t.instructions.length>100?'...':''}</div>`:''}
          ${t.result?`<div class="t-result"><i class="fas fa-clipboard-check"></i> <strong>Result:</strong> ${esc(t.result)}</div>`:''}
        </div>
        <div class="t-right">
          <span class="badge ${t.is_overdue?'overdue':t.status}">${t.is_overdue?'OVERDUE':t.status}</span>
          <button class="btn-sm del" onclick="deleteTask(${t.id})"><i class="fas fa-trash"></i></button>
        </div>
      </div>
    </div>`).join('');
}

function filterTasks(status, btn) {
  filterStatus = status;
  document.querySelectorAll('.tab-pane.active .btn-sm').forEach(b => b.style.borderColor='');
  if (btn) { btn.style.borderColor = '#0f3460'; btn.style.color = '#0f3460'; }
  renderTasks();
}

async function deleteTask(id) {
  if (!confirm('Delete this nursing task?')) return;
  const r = await fetch(`/api/nursing-orders/${id}`, { method:'DELETE', headers:H });
  const d = await r.json();
  if (d.success) { showToast('Task deleted'); loadTasks(); }
  else showToast(d.message||'Error', true);
}

// ── Nurses ──
async function loadNurses() {
  const el = document.getElementById('nurses-list');
  el.innerHTML = '<div class="empty-msg"><i class="fas fa-spinner fa-spin"></i></div>';
  try {
    const [nursesR, tasksR] = await Promise.all([
      fetch('/api/nurse/my-nurses', { headers: H }),
      fetch('/api/nursing-orders', { headers: H })
    ]);
    const nursesD = await nursesR.json();
    const tasksD  = await tasksR.json();
    const tasks   = tasksD.data || [];
    const nurses  = nursesD.data || [];
    if (!nurses.length) { el.innerHTML = '<div class="empty-msg"><i class="fas fa-user-nurse"></i><br>No nurses assigned to you</div>'; return; }
    el.innerHTML = nurses.map(n => {
      const nTasks   = tasks.filter(t => t.nurse?.id === n.user?.id || t.nurse_id === n.user_id);
      const completed = nTasks.filter(t => t.status === 'Completed').length;
      const ongoing   = nTasks.filter(t => t.status === 'Ongoing').length;
      const pending   = nTasks.filter(t => t.status === 'Pending').length;
      return `<div class="nurse-card">
        <div class="nurse-card-head">
          <div class="nurse-ava">${esc((n.user?.name||'N')[0])}</div>
          <div>
            <div style="font-weight:700;font-size:14px;">${esc(n.user?.name||'Nurse')}</div>
            <div style="font-size:11px;opacity:0.8;">${esc(n.user?.email||'')} ${n.department?'· '+esc(n.department):''}</div>
          </div>
        </div>
        <div class="nurse-stats">
          <div class="nurse-stat"><div class="nurse-stat-val">${nTasks.length}</div><div class="nurse-stat-lbl">Total Tasks</div></div>
          <div class="nurse-stat"><div class="nurse-stat-val" style="color:#10b981;">${completed}</div><div class="nurse-stat-lbl">Completed</div></div>
          <div class="nurse-stat"><div class="nurse-stat-val" style="color:#3b82f6;">${ongoing}</div><div class="nurse-stat-lbl">Ongoing</div></div>
          <div class="nurse-stat"><div class="nurse-stat-val" style="color:#f59e0b;">${pending}</div><div class="nurse-stat-lbl">Pending</div></div>
        </div>
      </div>`;
    }).join('');
  } catch(e) { el.innerHTML = '<div class="empty-msg">Failed to load</div>'; }
}

// ── Audit Log ──
async function loadAuditLog() {
  const el = document.getElementById('audit-list');
  el.innerHTML = '<div class="empty-msg"><i class="fas fa-spinner fa-spin"></i></div>';
  try {
    const r = await fetch('/api/nurse/audit-log', { headers: H });
    const d = await r.json();
    if (!d.success || !d.data.length) { el.innerHTML = '<div class="empty-msg"><i class="fas fa-list"></i><br>No audit logs yet</div>'; return; }
    el.innerHTML = `<div style="background:white;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,0.06);">` +
      d.data.map(l => {
        const action = l.action.replace(/_/g,' ');
        const details = l.details ? JSON.parse(l.details) : {};
        return `<div class="log-row">
          <div class="log-dot log-dot-${l.action}"></div>
          <div style="flex:1;">
            <div class="log-action">${esc(l.nurse?.name||'Nurse')} — <span style="color:#0f3460;">${action}</span></div>
            <div class="log-detail">${l.patient?.name?'Patient: '+esc(l.patient.name)+' · ':''} ${esc(JSON.stringify(details))}</div>
          </div>
          <div class="log-time">${new Date(l.created_at).toLocaleString()}</div>
        </div>`;
      }).join('') + `</div>`;
  } catch(e) { el.innerHTML = '<div class="empty-msg">Failed to load</div>'; }
}

// ── Modal ──
async function openModal() {
  document.getElementById('assignModal').classList.add('open');
  // Load patients and nurses
  const [pR, nR] = await Promise.all([
    fetch('/api/patients', { headers: H }),
    fetch('/api/nurse/my-nurses', { headers: H })
  ]);
  const pD = await pR.json(); const nD = await nR.json();
  const pSel = document.getElementById('f-patient');
  const nSel = document.getElementById('f-nurse');
  pSel.innerHTML = '<option value="">Select patient...</option>';
  nSel.innerHTML = '<option value="">Select nurse...</option>';
  
  // Extract patient array from paginator if present
  const patientsArray = Array.isArray(pD.data) ? pD.data : (pD.data?.data || []);
  
  patientsArray.forEach(p => { const o=document.createElement('option'); o.value=p.id; o.textContent=p.name; pSel.appendChild(o); });
  (nD.data||[]).forEach(n => { const o=document.createElement('option'); o.value=n.user_id; o.textContent=n.user?.name||'Nurse'; nSel.appendChild(o); });
}
function closeModal() {
  document.getElementById('assignModal').classList.remove('open');
  document.getElementById('assignForm').reset();
}

async function submitTask(e) {
  e.preventDefault();
  const body = {
    patient_id: document.getElementById('f-patient').value,
    nurse_id: document.getElementById('f-nurse').value,
    type: document.getElementById('f-type').value,
    dosage_method: document.getElementById('f-method').value,
    instructions: document.getElementById('f-instructions').value,
    scheduled_time: document.getElementById('f-times').value,
    interval_hours: document.getElementById('f-interval').value || null,
    start_date: document.getElementById('f-start').value || null,
    end_date: document.getElementById('f-end').value || null,
    notes: document.getElementById('f-notes').value,
  };
  try {
    const r = await fetch('/api/nursing-orders', { method:'POST', headers:H, body:JSON.stringify(body) });
    const d = await r.json();
    if (d.success) { closeModal(); showToast('Task assigned to nurse!'); loadTasks(); }
    else showToast(d.message||'Error', true);
  } catch(e) { showToast('Network error', true); }
}

function esc(str) {
  if (!str) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}
function showToast(msg, err=false) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.style.background = err?'#ef4444':'#0f3460';
  t.classList.add('show'); setTimeout(()=>t.classList.remove('show'), 3000);
}
window.addEventListener('click', e => { if(e.target.classList.contains('modal-bg')) e.target.classList.remove('open'); });

loadTasks();
</script>
@endsection
