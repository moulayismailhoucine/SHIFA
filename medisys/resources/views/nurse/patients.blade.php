@extends('layouts.app')
@section('page-title', 'Patients — Nurse Portal')
@section('content')
<style>
.np-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px;}
.np-header h2{font-size:20px;font-weight:700;color:#1e293b;margin:0;display:flex;align-items:center;gap:10px;}
.search-wrap{position:relative;flex:1;max-width:320px;}
.search-wrap i{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:14px;}
.search-input{width:100%;padding:10px 14px 10px 36px;border:1.5px solid #e2e8f0;border-radius:12px;font-size:14px;font-family:inherit;}
.search-input:focus{outline:none;border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,0.1);}
.p-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:16px;}
.p-card{background:white;border-radius:18px;box-shadow:0 2px 12px rgba(0,0,0,0.07);overflow:hidden;transition:all 0.2s;}
.p-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,0.12);}
.p-card-head{background:linear-gradient(135deg,#0f3460,#533483);padding:18px;display:flex;align-items:center;gap:14px;}
.p-ava{width:50px;height:50px;border-radius:14px;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:800;color:white;flex-shrink:0;}
.p-card-name{color:white;font-size:16px;font-weight:700;margin:0;}
.p-card-sub{color:rgba(255,255,255,0.7);font-size:12px;}
.p-card-body{padding:16px;}
.p-info-row{display:flex;justify-content:space-between;padding:5px 0;border-bottom:1px solid #f8fafc;font-size:13px;}
.p-info-row:last-child{border-bottom:none;}
.p-info-row .k{color:#64748b;font-weight:600;}
.p-info-row .v{color:#1e293b;}
.p-card-actions{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;padding:0 16px 16px;}
.pac-btn{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;padding:12px 4px;border-radius:12px;border:1.5px solid #e2e8f0;background:white;cursor:pointer;transition:all 0.2s;font-size:11px;font-weight:600;color:#374151;}
.pac-btn i{font-size:18px;}
.pac-btn:hover{transform:translateY(-2px);}
.pac-btn.vitals:hover{border-color:#ef4444;background:#fee2e2;color:#dc2626;}
.pac-btn.notes:hover{border-color:#8b5cf6;background:#ede9fe;color:#7c3aed;}
.pac-btn.orders:hover{border-color:#10b981;background:#d1fae5;color:#059669;}
.empty{text-align:center;padding:60px;color:#94a3b8;grid-column:1/-1;}
.empty i{font-size:48px;display:block;margin-bottom:12px;opacity:0.4;}

/* Modal shared */
.modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:998;align-items:center;justify-content:center;}
.modal-bg.open{display:flex;}
.modal-box{background:white;border-radius:20px;width:100%;max-width:580px;overflow:hidden;margin:16px;box-shadow:0 20px 60px rgba(0,0,0,0.25);}
.modal-hd{padding:20px 24px;display:flex;justify-content:space-between;align-items:center;color:white;}
.modal-hd.vitals-hd{background:linear-gradient(135deg,#ef4444,#dc2626);}
.modal-hd.notes-hd{background:linear-gradient(135deg,#8b5cf6,#7c3aed);}
.modal-hd.orders-hd{background:linear-gradient(135deg,#10b981,#059669);}
.modal-hd h3{margin:0;font-size:17px;font-weight:700;display:flex;align-items:center;gap:10px;}
.modal-hd .patient-label{font-size:13px;opacity:0.85;margin-top:2px;}
.modal-close{background:none;border:none;color:white;font-size:22px;cursor:pointer;line-height:1;}
.modal-body{padding:20px 24px;max-height:70vh;overflow-y:auto;}
.fg{margin-bottom:14px;}
.fg label{display:block;font-size:12.5px;font-weight:700;color:#374151;margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px;}
.fg input,.fg select,.fg textarea{width:100%;padding:10px 14px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-family:inherit;color:#1e293b;transition:border 0.2s;}
.fg input:focus,.fg select:focus,.fg textarea:focus{outline:none;}
.vitals-hd~.modal-body .fg input:focus,.vitals-hd~.modal-body .fg select:focus{border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,0.1);}
.notes-hd~.modal-body .fg input:focus,.notes-hd~.modal-body .fg select:focus,.notes-hd~.modal-body .fg textarea:focus{border-color:#8b5cf6;box-shadow:0 0 0 3px rgba(139,92,246,0.1);}
.orders-hd~.modal-body .fg input:focus,.orders-hd~.modal-body .fg select:focus,.orders-hd~.modal-body .fg textarea:focus{border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,0.1);}
.fg-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.pain-slider{display:flex;align-items:center;gap:12px;}
.pain-slider input[type=range]{flex:1;accent-color:#8b5cf6;}
.pain-val{width:36px;height:36px;border-radius:10px;background:#8b5cf6;color:white;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;}
.modal-footer{padding:16px 24px;border-top:1px solid #f1f5f9;display:flex;justify-content:flex-end;gap:10px;}
.btn-cancel{padding:10px 20px;border-radius:10px;border:1.5px solid #e2e8f0;background:white;color:#64748b;font-size:14px;font-weight:600;cursor:pointer;}
.btn-vitals{padding:10px 22px;border-radius:10px;background:linear-gradient(135deg,#ef4444,#dc2626);color:white;border:none;font-size:14px;font-weight:700;cursor:pointer;}
.btn-notes{padding:10px 22px;border-radius:10px;background:linear-gradient(135deg,#8b5cf6,#7c3aed);color:white;border:none;font-size:14px;font-weight:700;cursor:pointer;}
.btn-orders{padding:10px 22px;border-radius:10px;background:linear-gradient(135deg,#10b981,#059669);color:white;border:none;font-size:14px;font-weight:700;cursor:pointer;}
.toast{position:fixed;bottom:24px;right:24px;background:#1e293b;color:white;padding:12px 20px;border-radius:12px;font-size:14px;z-index:9999;transform:translateY(80px);opacity:0;transition:all 0.3s;}
.toast.show{transform:translateY(0);opacity:1;}
</style>

<div class="np-header">
  <h2><i class="fas fa-user-injured" style="color:#10b981;"></i> Patients</h2>
  <div class="search-wrap">
    <i class="fas fa-search"></i>
    <input class="search-input" id="search" placeholder="Search patient..." oninput="filterPatients(this.value)">
  </div>
</div>

<div class="p-grid" id="p-grid">
  @forelse($patients as $p)
  <div class="p-card" data-name="{{ strtolower($p->name) }}">
    <div class="p-card-head">
      <div class="p-ava">{{ strtoupper(substr($p->name,0,1)) }}</div>
      <div>
        <div class="p-card-name">{{ $p->name }}</div>
        <div class="p-card-sub">ID #{{ $p->id }} &nbsp;·&nbsp; {{ $p->blood_type ?? 'N/A' }}</div>
      </div>
    </div>
    <div class="p-card-body">
      <div class="p-info-row"><span class="k"><i class="fas fa-birthday-cake"></i> Age</span><span class="v">{{ $p->age ?? 'N/A' }} years</span></div>
      <div class="p-info-row"><span class="k"><i class="fas fa-phone"></i> Phone</span><span class="v">{{ $p->phone ?? 'N/A' }}</span></div>
      <div class="p-info-row"><span class="k"><i class="fas fa-venus-mars"></i> Gender</span><span class="v">{{ ucfirst($p->gender ?? 'N/A') }}</span></div>
      @if($p->allergies)
      <div class="p-info-row"><span class="k"><i class="fas fa-allergies"></i> Allergies</span><span class="v" style="color:#ef4444;">{{ $p->allergies }}</span></div>
      @endif
    </div>
    <div class="p-card-actions">
      <button class="pac-btn vitals" onclick="openVitals({{ $p->id }},'{{ addslashes($p->name) }}')">
        <i class="fas fa-heartbeat" style="color:#ef4444;"></i> Vitals
      </button>
      <button class="pac-btn notes" onclick="openNotes({{ $p->id }},'{{ addslashes($p->name) }}')">
        <i class="fas fa-notes-medical" style="color:#8b5cf6;"></i> Notes
      </button>
      <button class="pac-btn orders" onclick="openOrders({{ $p->id }},'{{ addslashes($p->name) }}')">
        <i class="fas fa-clipboard-list" style="color:#10b981;"></i> Orders
      </button>
    </div>
  </div>
  @empty
  <div class="empty"><i class="fas fa-user-plus"></i><br>No patients found</div>
  @endforelse
</div>

@if($patients->hasPages())
<div style="display:flex;justify-content:center;margin-top:24px;">{{ $patients->links() }}</div>
@endif

{{-- Vitals Modal --}}
<div class="modal-bg" id="vitalsModal">
  <div class="modal-box">
    <div class="modal-hd vitals-hd">
      <div><h3><i class="fas fa-heartbeat"></i> Record Vitals</h3><div class="patient-label" id="v-patient-label"></div></div>
      <button class="modal-close" onclick="close('vitalsModal')">×</button>
    </div>
    <form method="POST" id="vitalsForm" action="" enctype="multipart/form-data">
      @csrf
      <div class="modal-body">
        <div class="fg-row">
          <div class="fg">
            <label>Systolic BP (mmHg)</label>
            <input type="number" name="blood_pressure_systolic" required min="50" max="250" placeholder="120">
          </div>
          <div class="fg">
            <label>Diastolic BP (mmHg)</label>
            <input type="number" name="blood_pressure_diastolic" required min="30" max="150" placeholder="80">
          </div>
        </div>
        <div class="fg-row">
          <div class="fg">
            <label>Heart Rate (bpm)</label>
            <input type="number" name="heart_rate" required min="40" max="200" placeholder="72">
          </div>
          <div class="fg">
            <label>Temperature (°C)</label>
            <input type="number" step="0.1" name="temperature" required min="30" max="45" placeholder="36.5">
          </div>
        </div>
        <div class="fg-row">
          <div class="fg">
            <label>SpO₂ (%)</label>
            <input type="number" name="oxygen_saturation" required min="70" max="100" placeholder="98">
          </div>
          <div class="fg">
            <label>Respiratory Rate</label>
            <input type="number" name="respiratory_rate" required min="8" max="40" placeholder="16">
          </div>
        </div>
        <div class="fg">
          <label>Notes</label>
          <textarea name="notes" rows="2" placeholder="Additional observations..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="close('vitalsModal')">Cancel</button>
        <button type="submit" class="btn-vitals"><i class="fas fa-save"></i> Save Vitals</button>
      </div>
    </form>
  </div>
</div>

{{-- Notes Modal --}}
<div class="modal-bg" id="notesModal">
  <div class="modal-box">
    <div class="modal-hd notes-hd">
      <div><h3><i class="fas fa-notes-medical"></i> Add Nurse Note</h3><div class="patient-label" id="n-patient-label"></div></div>
      <button class="modal-close" onclick="close('notesModal')">×</button>
    </div>
    <form method="POST" id="notesForm" action="" enctype="multipart/form-data">
      @csrf
      <div class="modal-body">
        <div class="fg-row">
          <div class="fg">
            <label>Note Type</label>
            <select name="type" required>
              <option value="">Select type...</option>
              <option value="observation">🔍 Observation</option>
              <option value="care">💊 Care Provided</option>
              <option value="medication">🩺 Medication</option>
              <option value="other">📋 Other</option>
            </select>
          </div>
          <div class="fg">
            <label>Pain Level (0–10)</label>
            <div class="pain-slider">
              <input type="range" name="pain_level" min="0" max="10" value="0" oninput="document.getElementById('painVal').textContent=this.value">
              <div class="pain-val" id="painVal">0</div>
            </div>
          </div>
        </div>
        <div class="fg">
          <label>Clinical Notes</label>
          <textarea name="note" rows="4" required placeholder="Describe patient condition, observations, or care provided..."></textarea>
        </div>
        <div class="fg">
          <label>Attachment (image or PDF)</label>
          <input type="file" name="attachment" accept="image/*,.pdf">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="close('notesModal')">Cancel</button>
        <button type="submit" class="btn-notes"><i class="fas fa-save"></i> Save Note</button>
      </div>
    </form>
  </div>
</div>

{{-- Nursing Order Modal (quick) --}}
<div class="modal-bg" id="ordersModal">
  <div class="modal-box">
    <div class="modal-hd orders-hd">
      <div><h3><i class="fas fa-clipboard-list"></i> Quick Nursing Order</h3><div class="patient-label" id="o-patient-label"></div></div>
      <button class="modal-close" onclick="close('ordersModal')">×</button>
    </div>
    <form id="quickOrderForm" onsubmit="submitQuickOrder(event)">
      <div class="modal-body">
        <div class="fg-row">
          <div class="fg">
            <label>Order Type</label>
            <select id="qo-type" required>
              <option value="">Select...</option>
              <option value="Medication">💊 Medication</option>
              <option value="Monitoring">👁 Monitoring</option>
              <option value="Care Procedure">🤲 Care Procedure</option>
            </select>
          </div>
          <div class="fg">
            <label>Admin. Method</label>
            <select id="qo-method">
              <option value="">N/A</option>
              <option>Oral</option><option>IV</option><option>Injection</option>
              <option>Topical</option><option>Inhalation</option>
            </select>
          </div>
        </div>
        <div class="fg">
          <label>Schedule / Frequency</label>
          <input id="qo-schedule" type="text" placeholder="e.g. Every 8 hours">
        </div>
        <div class="fg">
          <label>Notes / Instructions</label>
          <textarea id="qo-notes" rows="3" placeholder="Additional instructions..."></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-cancel" onclick="close('ordersModal')">Cancel</button>
        <button type="submit" class="btn-orders"><i class="fas fa-save"></i> Create Order</button>
      </div>
    </form>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
let currentPatientId = null;
const token = localStorage.getItem('auth_token') || '';
const apiH  = { 'Accept':'application/json','Content-Type':'application/json','Authorization':'Bearer '+token };

function openVitals(id, name) {
  currentPatientId = id;
  document.getElementById('v-patient-label').textContent = name;
  document.getElementById('vitalsForm').action = `/nurse/vitals/${id}`;
  open('vitalsModal');
}
function openNotes(id, name) {
  currentPatientId = id;
  document.getElementById('n-patient-label').textContent = name;
  document.getElementById('notesForm').action = `/nurse/notes/${id}`;
  open('notesModal');
}
function openOrders(id, name) {
  currentPatientId = id;
  document.getElementById('o-patient-label').textContent = name;
  open('ordersModal');
}

function open(id)  { document.getElementById(id).classList.add('open'); }
function close(id) { document.getElementById(id).classList.remove('open'); }
window.addEventListener('click', e => { if(e.target.classList.contains('modal-bg')) e.target.classList.remove('open'); });

// Success flash
@if(session('success'))
showToast('{{ session("success") }}');
@endif

async function submitQuickOrder(e) {
  e.preventDefault();
  const body = {
    patient_id: currentPatientId,
    type: document.getElementById('qo-type').value,
    dosage_method: document.getElementById('qo-method').value,
    schedule: document.getElementById('qo-schedule').value,
    notes: document.getElementById('qo-notes').value,
  };
  try {
    const r = await fetch('/api/nursing-orders', { method:'POST', headers:apiH, body:JSON.stringify(body) });
    const d = await r.json();
    if (d.success) { close('ordersModal'); document.getElementById('quickOrderForm').reset(); showToast('Order created!'); }
    else showToast(d.message||'Error', true);
  } catch(e) { showToast('Network error', true); }
}

function filterPatients(q) {
  document.querySelectorAll('.p-card').forEach(c => {
    c.style.display = c.dataset.name.includes(q.toLowerCase()) ? '' : 'none';
  });
}

function showToast(msg, err=false) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.style.background = err ? '#ef4444' : '#059669';
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3500);
}
</script>
@endsection
