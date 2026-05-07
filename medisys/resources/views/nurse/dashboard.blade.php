@extends('layouts.app')
@section('page-title', 'Nurse Dashboard')
@section('content')
<style>
:root{--g1:#10b981;--g2:#059669;}
/* Stats */
.ns-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px;margin-bottom:22px;}
.ns-stat{background:white;border-radius:14px;padding:18px 20px;display:flex;align-items:center;gap:14px;box-shadow:0 2px 10px rgba(0,0,0,0.07);border-left:4px solid var(--g1);}
.ns-stat.warn{border-color:#f59e0b;} .ns-stat.danger{border-color:#ef4444;} .ns-stat.blue{border-color:#3b82f6;}
.si{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0;}
.si.g{background:#d1fae5;color:#10b981;} .si.w{background:#fef3c7;color:#f59e0b;}
.si.r{background:#fee2e2;color:#ef4444;} .si.b{background:#dbeafe;color:#3b82f6;}
.sv{font-size:26px;font-weight:800;color:#1e293b;line-height:1;} .sl{font-size:11px;color:#64748b;margin-top:2px;}
/* Layout */
.ns-grid{display:grid;grid-template-columns:1fr 340px;gap:18px;}
@media(max-width:900px){.ns-grid{grid-template-columns:1fr;}}
/* Cards */
.nc{background:white;border-radius:16px;box-shadow:0 2px 10px rgba(0,0,0,0.07);overflow:hidden;}
.nc-head{padding:14px 18px;background:linear-gradient(135deg,#10b981,#059669);color:white;display:flex;align-items:center;justify-content:space-between;}
.nc-head h3{margin:0;font-size:14px;font-weight:700;display:flex;align-items:center;gap:8px;}
.nc-head a{color:white;font-size:11px;background:rgba(255,255,255,0.2);padding:3px 11px;border-radius:20px;text-decoration:none;}
/* Task cards */
.task-card{padding:14px 18px;border-bottom:1px solid #f1f5f9;display:flex;gap:12px;align-items:flex-start;cursor:pointer;transition:background 0.15s;}
.task-card:hover{background:#f8fafc;} .task-card:last-child{border-bottom:none;}
.tc-icon{width:38px;height:38px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0;}
.tc-icon.med{background:#dbeafe;color:#2563eb;} .tc-icon.mon{background:#ede9fe;color:#7c3aed;} .tc-icon.care{background:#d1fae5;color:#059669;}
.tc-body{flex:1;min-width:0;}
.tc-patient{font-weight:700;font-size:13px;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.tc-type{font-size:11px;color:#64748b;margin:2px 0;}
.tc-sched{font-size:11px;color:#94a3b8;display:flex;align-items:center;gap:4px;}
.tc-right{display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0;}
.badge{padding:3px 10px;border-radius:20px;font-size:10px;font-weight:700;}
.badge.Pending{background:#fef3c7;color:#92400e;}
.badge.Ongoing{background:#dbeafe;color:#1e40af;}
.badge.Completed{background:#d1fae5;color:#065f46;}
.badge.overdue{background:#fee2e2;color:#991b1b;animation:pulse 1.5s infinite;}
@keyframes pulse{0%,100%{opacity:1;}50%{opacity:0.7;}}
.empty-msg{padding:32px;text-align:center;color:#94a3b8;}
.empty-msg i{font-size:32px;display:block;margin-bottom:8px;opacity:0.4;}
/* Schedule timeline */
.sched-day{padding:14px 18px;border-bottom:1px solid #f1f5f9;}
.sched-day:last-child{border-bottom:none;}
.sched-date{font-size:12px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;}
.sched-item{display:flex;gap:10px;align-items:flex-start;margin-bottom:8px;}
.sched-time{font-size:11px;font-weight:700;color:#10b981;width:40px;flex-shrink:0;padding-top:2px;}
.sched-dot{width:8px;height:8px;border-radius:50%;margin-top:5px;flex-shrink:0;}
.sched-dot.Pending{background:#f59e0b;} .sched-dot.Ongoing{background:#3b82f6;} .sched-dot.Completed{background:#10b981;}
.sched-text{font-size:12px;color:#374151;}
/* Quick actions */
.qa-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;padding:14px;}
.qa-btn{display:flex;flex-direction:column;align-items:center;gap:5px;padding:14px 8px;border-radius:12px;border:1.5px solid #e2e8f0;background:white;cursor:pointer;text-decoration:none;color:#374151;transition:all 0.2s;font-size:11px;font-weight:600;}
.qa-btn i{font-size:20px;} .qa-btn:hover{transform:translateY(-2px);}
.qa-btn.v:hover{border-color:#ef4444;background:#fee2e2;color:#dc2626;}
.qa-btn.n:hover{border-color:#8b5cf6;background:#ede9fe;color:#7c3aed;}
.qa-btn.o:hover{border-color:#10b981;background:#d1fae5;color:#059669;}
.qa-btn.a:hover{border-color:#f59e0b;background:#fef3c7;color:#d97706;}
/* Execute modal */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:999;align-items:center;justify-content:center;}
.modal-bg.open{display:flex;}
.modal-box{background:white;border-radius:18px;width:100%;max-width:520px;overflow:hidden;margin:16px;box-shadow:0 20px 60px rgba(0,0,0,0.25);}
.mhd{padding:18px 22px;background:linear-gradient(135deg,#10b981,#059669);color:white;display:flex;justify-content:space-between;align-items:center;}
.mhd h3{margin:0;font-size:16px;font-weight:700;display:flex;align-items:center;gap:8px;}
.mc{background:none;border:none;color:white;font-size:22px;cursor:pointer;}
.mbody{padding:20px 22px;}
.fg{margin-bottom:14px;}
.fg label{display:block;font-size:11px;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px;}
.fg input,.fg select,.fg textarea{width:100%;padding:9px 13px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-family:inherit;color:#1e293b;}
.fg input:focus,.fg select:focus,.fg textarea:focus{outline:none;border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,0.1);}
.mfoot{padding:14px 22px;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end;gap:10px;}
.btn-cancel{padding:9px 18px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;color:#64748b;font-size:13px;font-weight:600;cursor:pointer;}
.btn-save{padding:9px 20px;border-radius:10px;background:linear-gradient(135deg,#10b981,#059669);color:white;border:none;font-size:13px;font-weight:700;cursor:pointer;}
.task-detail-box{background:#f8fafc;border-radius:10px;padding:14px;margin-bottom:14px;border-left:3px solid #10b981;}
.task-detail-box .tlabel{font-size:10px;font-weight:700;text-transform:uppercase;color:#64748b;margin-bottom:4px;}
.task-detail-box .tval{font-size:13px;color:#1e293b;}
.toast{position:fixed;bottom:24px;right:24px;background:#1e293b;color:white;padding:12px 20px;border-radius:12px;font-size:13px;z-index:9999;transform:translateY(80px);opacity:0;transition:all 0.3s;}
.toast.show{transform:translateY(0);opacity:1;}
</style>

{{-- Critical Alert Strip --}}
<div id="alert-strip"></div>

<div class="ns-stats">
  <div class="ns-stat"><div class="si g"><i class="fas fa-tasks"></i></div><div><div class="sv" id="s-total">—</div><div class="sl">My Tasks</div></div></div>
  <div class="ns-stat blue"><div class="si b"><i class="fas fa-play-circle"></i></div><div><div class="sv" id="s-ongoing">—</div><div class="sl">In Progress</div></div></div>
  <div class="ns-stat warn"><div class="si w"><i class="fas fa-clock"></i></div><div><div class="sv" id="s-pending">—</div><div class="sl">Pending</div></div></div>
  <div class="ns-stat danger"><div class="si r"><i class="fas fa-exclamation-triangle"></i></div><div><div class="sv" id="s-overdue">—</div><div class="sl">Overdue</div></div></div>
</div>

<div class="ns-grid">
  <div style="display:flex;flex-direction:column;gap:16px;">
    {{-- My Tasks --}}
    <div class="nc">
      <div class="nc-head">
        <h3><i class="fas fa-clipboard-list"></i> My Assigned Tasks</h3>
        <a href="{{ route('nurse.orders') }}">View All →</a>
      </div>
      <div id="tasks-list"><div class="empty-msg"><i class="fas fa-spinner fa-spin"></i> Loading tasks...</div></div>
    </div>

    {{-- Weekly Schedule --}}
    <div class="nc">
      <div class="nc-head"><h3><i class="fas fa-calendar-week"></i> This Week's Schedule</h3></div>
      <div id="schedule-view"><div class="empty-msg"><i class="fas fa-spinner fa-spin"></i> Loading schedule...</div></div>
    </div>
  </div>

  <div style="display:flex;flex-direction:column;gap:16px;">
    {{-- Quick Actions --}}
    <div class="nc">
      <div class="nc-head"><h3><i class="fas fa-bolt"></i> Quick Actions</h3></div>
      <div class="qa-grid">
        <a href="{{ route('nurse.orders') }}" class="qa-btn o"><i class="fas fa-clipboard-list"></i>My Orders</a>
        <a href="{{ route('nurse.patients') }}" class="qa-btn v"><i class="fas fa-heartbeat"></i>Add Vitals</a>
        <a href="{{ route('nurse.patients') }}" class="qa-btn n"><i class="fas fa-notes-medical"></i>Add Notes</a>
        <a href="{{ route('nurse.patients') }}" class="qa-btn a"><i class="fas fa-user-injured"></i>Patients</a>
      </div>
    </div>

    {{-- My Patients (limited view) --}}
    <div class="nc" style="flex:1;">
      <div class="nc-head">
        <h3><i class="fas fa-user-injured"></i> My Patients</h3>
        <a href="{{ route('nurse.patients') }}">View →</a>
      </div>
      <div id="my-patients-list"><div class="empty-msg"><i class="fas fa-spinner fa-spin"></i></div></div>
    </div>
  </div>
</div>

{{-- Execute Task Modal --}}
<div class="modal-bg" id="execModal">
  <div class="modal-box">
    <div class="mhd">
      <h3><i class="fas fa-check-circle"></i> Execute Task</h3>
      <button class="mc" onclick="closeExec()">×</button>
    </div>
    <div class="mbody">
      <div class="task-detail-box">
        <div class="tlabel">Task</div><div class="tval" id="exec-type">—</div>
        <div class="tlabel" style="margin-top:8px;">Patient</div><div class="tval" id="exec-patient">—</div>
        <div class="tlabel" style="margin-top:8px;">Instructions</div><div class="tval" id="exec-instructions">—</div>
      </div>
      <div class="fg">
        <label>Status</label>
        <select id="exec-status">
          <option value="Ongoing">🔄 In Progress</option>
          <option value="Completed">✅ Completed</option>
        </select>
      </div>
      <div class="fg">
        <label>Record Result (e.g. BP: 120/80, Glucose: 95, SpO2: 98%)</label>
        <textarea id="exec-result" rows="3" placeholder="Enter measured values, observations..."></textarea>
      </div>
    </div>
    <div class="mfoot">
      <button class="btn-cancel" onclick="closeExec()">Cancel</button>
      <button class="btn-save" onclick="submitExec()"><i class="fas fa-save"></i> Save</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
const token = localStorage.getItem('auth_token') || '';
const H = { 'Accept':'application/json','Content-Type':'application/json','Authorization':'Bearer '+token };
let currentOrderId = null;

const typeIcon  = { 'Medication':'pills','Monitoring':'eye','Care Procedure':'hand-holding-medical' };
const typeClass = { 'Medication':'med','Monitoring':'mon','Care Procedure':'care' };

async function loadDashboard() {
  try {
    const [ordersR, patientsR] = await Promise.all([
      fetch('/api/nursing-orders', { headers: H }),
      fetch('/api/nurse/my-patients', { headers: H })
    ]);
    const ordersD = await ordersR.json();
    const patientsD = await patientsR.json();

    if (ordersD.success) renderTasks(ordersD.data || []);
    if (patientsD.success) renderPatients(patientsD.data || []);
  } catch(e) {
    document.getElementById('tasks-list').innerHTML = '<div class="empty-msg">Failed to load</div>';
  }
}

function renderTasks(orders) {
  const total   = orders.length;
  const ongoing = orders.filter(o => o.status === 'Ongoing').length;
  const pending = orders.filter(o => o.status === 'Pending').length;
  const overdue = orders.filter(o => o.is_overdue && o.status !== 'Completed').length;

  document.getElementById('s-total').textContent   = total;
  document.getElementById('s-ongoing').textContent = ongoing;
  document.getElementById('s-pending').textContent = pending;
  document.getElementById('s-overdue').textContent = overdue;

  // Alert strip for overdue
  if (overdue > 0) {
    document.getElementById('alert-strip').innerHTML = `
      <div style="background:linear-gradient(135deg,#fef2f2,#fee2e2);border-left:4px solid #ef4444;border-radius:12px;padding:12px 18px;display:flex;align-items:center;gap:12px;margin-bottom:16px;">
        <div style="width:36px;height:36px;border-radius:50%;background:#fecaca;display:flex;align-items:center;justify-content:center;color:#dc2626;"><i class="fas fa-exclamation-triangle"></i></div>
        <div style="flex:1;"><div style="font-weight:700;color:#991b1b;font-size:13px;">⚠ ${overdue} Overdue Task${overdue>1?'s':''} — Immediate Attention Required</div></div>
      </div>`;
  }

  // Active tasks (not completed)
  const active = orders.filter(o => o.status !== 'Completed').slice(0, 8);
  if (!active.length) {
    document.getElementById('tasks-list').innerHTML = '<div class="empty-msg"><i class="fas fa-clipboard-check"></i><br>All tasks completed!</div>';
  } else {
    document.getElementById('tasks-list').innerHTML = active.map(o => `
      <div class="task-card" onclick="openExec(${o.id},'${esc(o.type)}','${esc(o.patient?.name||'')}','${esc(o.instructions||o.notes||'')}')">
        <div class="tc-icon ${typeClass[o.type]||'med'}"><i class="fas fa-${typeIcon[o.type]||'clipboard'}"></i></div>
        <div class="tc-body">
          <div class="tc-patient">${esc(o.patient?.name||'Patient')}</div>
          <div class="tc-type">${esc(o.type)}${o.dosage_method?' · '+esc(o.dosage_method):''}</div>
          ${o.schedule?`<div class="tc-sched"><i class="fas fa-clock" style="font-size:9px;"></i> ${esc(o.schedule)}</div>`:''}
        </div>
        <div class="tc-right">
          <span class="badge ${o.is_overdue?'overdue':o.status}">${o.is_overdue?'OVERDUE':o.status}</span>
        </div>
      </div>`).join('');
  }

  // Weekly schedule
  renderSchedule(orders);
}

function renderSchedule(orders) {
  const days = {};
  const now = new Date();
  for (let i = 0; i < 7; i++) {
    const d = new Date(now); d.setDate(now.getDate() + i);
    const key = d.toISOString().split('T')[0];
    days[key] = [];
  }

  orders.forEach(o => {
    if (o.status === 'Completed') return;
    const times = o.scheduled_time ? o.scheduled_time.split(',') : ['—'];
    const start = o.start_date ? new Date(o.start_date) : now;
    const end   = o.end_date   ? new Date(o.end_date)   : null;

    Object.keys(days).forEach(dayKey => {
      const dayDate = new Date(dayKey);
      if (dayDate >= start && (!end || dayDate <= end)) {
        times.forEach(t => {
          days[dayKey].push({ time: t.trim(), order: o });
        });
      }
    });
  });

  const hasAny = Object.values(days).some(d => d.length > 0);
  if (!hasAny) {
    document.getElementById('schedule-view').innerHTML = '<div class="empty-msg"><i class="fas fa-calendar-times"></i><br>No scheduled tasks this week</div>';
    return;
  }

  const dayNames = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
  document.getElementById('schedule-view').innerHTML = Object.entries(days)
    .filter(([,items]) => items.length > 0)
    .map(([date, items]) => {
      const d = new Date(date);
      const isToday = date === now.toISOString().split('T')[0];
      return `<div class="sched-day">
        <div class="sched-date" style="${isToday?'color:#10b981;':''}">
          ${isToday?'📅 Today':''}${!isToday?dayNames[d.getDay()]+' '+d.getDate():''}
        </div>
        ${items.map(({time,order}) => `
          <div class="sched-item">
            <div class="sched-time">${time}</div>
            <div class="sched-dot ${order.status}"></div>
            <div class="sched-text"><strong>${esc(order.patient?.name||'')}</strong> — ${esc(order.type)}</div>
          </div>`).join('')}
      </div>`;
    }).join('');
}

function renderPatients(patients) {
  const el = document.getElementById('my-patients-list');
  if (!patients.length) { el.innerHTML = '<div class="empty-msg"><i class="fas fa-user-plus"></i><br>No patients assigned</div>'; return; }
  el.innerHTML = patients.slice(0,6).map(p => `
    <div class="task-card">
      <div class="tc-icon care" style="background:linear-gradient(135deg,#10b981,#059669);color:white;font-weight:800;">${esc(p.name[0])}</div>
      <div class="tc-body">
        <div class="tc-patient">${esc(p.name)}</div>
        <div class="tc-type">${p.age||'?'} yrs · ${p.blood_type||'N/A'} · ${p.gender||''}</div>
        ${p.allergies?`<div class="tc-sched" style="color:#ef4444;"><i class="fas fa-allergies" style="font-size:9px;"></i> ${esc(p.allergies)}</div>`:''}
      </div>
    </div>`).join('');
}

function openExec(id, type, patientName, instructions) {
  currentOrderId = id;
  document.getElementById('exec-type').textContent = type;
  document.getElementById('exec-patient').textContent = patientName;
  document.getElementById('exec-instructions').textContent = instructions || 'No specific instructions';
  document.getElementById('exec-result').value = '';
  document.getElementById('exec-status').value = 'Ongoing';
  document.getElementById('execModal').classList.add('open');
}
function closeExec() { document.getElementById('execModal').classList.remove('open'); }

async function submitExec() {
  if (!currentOrderId) return;
  const body = {
    status: document.getElementById('exec-status').value,
    result: document.getElementById('exec-result').value,
  };
  try {
    const r = await fetch(`/api/nursing-orders/${currentOrderId}`, { method:'PUT', headers:H, body:JSON.stringify(body) });
    const d = await r.json();
    if (d.success) { closeExec(); showToast('Task updated successfully!'); loadDashboard(); }
    else showToast(d.message||'Error', true);
  } catch(e) { showToast('Network error', true); }
}

function esc(str) {
  if (!str) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function showToast(msg, err=false) {
  const t = document.getElementById('toast');
  t.textContent = msg; t.style.background = err?'#ef4444':'#059669';
  t.classList.add('show'); setTimeout(()=>t.classList.remove('show'), 3000);
}
window.addEventListener('click', e => { if(e.target.classList.contains('modal-bg')) e.target.classList.remove('open'); });

loadDashboard();
</script>
@endsection
