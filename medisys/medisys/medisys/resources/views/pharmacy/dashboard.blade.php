<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SHIFA — Pharmacy Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0f3460 0%,#16213e 50%,#10b981 100%);min-height:100vh}
.portal-header{background:rgba(255,255,255,0.08);backdrop-filter:blur(10px);padding:16px 32px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(255,255,255,0.1)}
.brand{display:flex;align-items:center;gap:12px;color:white}
.brand-icon{width:40px;height:40px;background:#10b981;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px}
.btn-logout{background:rgba(255,255,255,0.1);color:white;border:1px solid rgba(255,255,255,0.2);padding:8px 16px;border-radius:10px;cursor:pointer;font-size:13px;transition:all 0.2s}
.btn-logout:hover{background:rgba(255,255,255,0.2)}
.portal-content{max-width:1100px;margin:0 auto;padding:40px 24px}
/* Delivery Modal */
.del-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:600;align-items:center;justify-content:center;}
.del-modal.open{display:flex;}
.tabs{display:flex;gap:12px;margin-bottom:28px}
.tab{padding:10px 20px;border-radius:12px;font-size:13.5px;font-weight:600;cursor:pointer;background:rgba(255,255,255,0.1);color:white;border:none;transition:all 0.2s;display:flex;align-items:center;gap:8px}
.tab.active{background:#10b981;color:white}
.panel{display:none}
.panel.active{display:block}
.glass-card{background:rgba(255,255,255,0.97);border-radius:24px;padding:28px;box-shadow:0 20px 60px rgba(0,0,0,0.3);margin-bottom:24px}
.nfc-scanner{background:rgba(255,255,255,0.08);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,0.15);border-radius:24px;padding:40px;text-align:center;margin-bottom:36px}
.nfc-icon{width:80px;height:80px;background:linear-gradient(135deg,#10b981,#059669);border-radius:22px;display:flex;align-items:center;justify-content:center;font-size:32px;color:white;margin:0 auto 20px;animation:pulse 2s infinite}
@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(16,185,129,0.4)}50%{box-shadow:0 0 0 14px rgba(16,185,129,0)}}
.nfc-title{color:white;font-size:20px;font-weight:700;margin-bottom:8px}
.nfc-sub{color:rgba(255,255,255,0.6);font-size:13.5px;margin-bottom:24px}
.nfc-row{display:flex;gap:12px;max-width:480px;margin:0 auto}
.nfc-input{flex:1;background:rgba(255,255,255,0.12);border:1.5px solid rgba(255,255,255,0.2);border-radius:12px;padding:13px 18px;font-size:14px;color:white;font-family:inherit}
.nfc-input::placeholder{color:rgba(255,255,255,0.4)}
.nfc-input:focus{outline:none;border-color:#10b981;background:rgba(255,255,255,0.18)}
.btn-scan{background:#10b981;color:white;border:none;border-radius:12px;padding:13px 22px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;transition:all 0.2s}
.btn-scan:hover{background:#059669}
.patient-bar{display:flex;align-items:center;gap:20px;background:#f8fafc;border-radius:16px;padding:20px;margin-bottom:24px}
.p-avatar{width:64px;height:64px;background:linear-gradient(135deg,#0f3460,#533483);border-radius:18px;display:flex;align-items:center;justify-content:center;color:white;font-size:26px;font-weight:700;overflow:hidden;flex-shrink:0}
.p-avatar img{width:100%;height:100%;object-fit:cover}
.ord-card{border:1.5px solid #e2e8f0;border-radius:16px;padding:22px;margin-bottom:16px;transition:border-color 0.2s}
.ord-card:hover{border-color:#10b981}
.ord-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px;gap:12px;flex-wrap:wrap}
.ord-title{font-size:15px;font-weight:700;color:#0f3460}
.ord-meta{font-size:12px;color:#94a3b8;margin-top:4px}
.med-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px;margin-top:10px}
.med-chip{background:#f8fafc;border-radius:12px;padding:12px;border:1px solid #e2e8f0}
.med-name{font-weight:700;font-size:13px;color:#0f3460}
.med-det{font-size:12px;color:#64748b;margin-top:3px}
.badge{display:inline-flex;align-items:center;gap:4px;padding:4px 12px;border-radius:20px;font-size:11.5px;font-weight:600}
.badge-success{background:#f0fdf4;color:#166534}
.badge-warning{background:#fffbeb;color:#92400e}
.badge-info{background:#eff6ff;color:#1e40af}
.badge-dispensed{background:#f0fdf4;color:#166534;border:1px solid #bbf7d0}
.btn-pdf{display:inline-flex;align-items:center;gap:7px;background:#e94560;color:white;border:none;border-radius:10px;padding:8px 16px;font-size:12.5px;font-weight:700;cursor:pointer;text-decoration:none;transition:all 0.2s}
.btn-pdf:hover{background:#c73852}
.btn-deliver{display:inline-flex;align-items:center;gap:7px;background:#10b981;color:white;border:none;border-radius:10px;padding:8px 16px;font-size:12.5px;font-weight:700;cursor:pointer;transition:all 0.2s}
.btn-deliver:hover{background:#059669}
.btn-deliver:disabled{background:#94a3b8;cursor:not-allowed}
/* Profile tab styles */
.profile-section{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;margin-bottom:24px}
.info-box{background:#f8fafc;border-radius:16px;padding:20px}
.info-box label{font-size:11px;color:#94a3b8;text-transform:uppercase;letter-spacing:0.5px;display:block;margin-bottom:6px}
.info-box div{font-size:14px;font-weight:600;color:#1e293b}
</style>
</head>
<body>
<header class="portal-header">
  <div class="brand">
    <div class="brand-icon"><i class="fas fa-pills"></i></div>
    <div>
      <div style="color:white;font-size:18px;font-weight:800;">SHIFA</div>
      <div style="color:rgba(255,255,255,0.5);font-size:11px;">Pharmacy Portal</div>
    </div>
  </div>
  <div style="display:flex;align-items:center;gap:16px;">
    <div style="color:white;font-size:13px;opacity:0.8;" id="ph-name-hdr"></div>
    <button class="btn-logout" onclick="logout()"><i class="fas fa-sign-out-alt"></i> Logout</button>
  </div>
</header>

<div class="portal-content">
  <div class="tabs">
    <button class="tab active" onclick="showTab('scan',this)"><i class="fas fa-wifi"></i> NFC Scan</button>
    <button class="tab" onclick="showTab('profile',this)"><i class="fas fa-store"></i> Profile</button>
  </div>

  <!-- NFC Scan Tab -->
  <div id="tab-scan" class="panel active">
    <div class="nfc-scanner">
      <div class="nfc-icon"><i class="fas fa-wifi"></i></div>
      <div class="nfc-title">Scan Patient NFC Card</div>
      <div class="nfc-sub">Tap the patient card or type the UID to view their active prescriptions</div>
      <div class="nfc-row">
        <input type="text" id="nfc-uid" class="nfc-input" placeholder="NFC UID (e.g. NFC001ABC)...">
        <button class="btn-scan" onclick="scanNfc()"><i class="fas fa-search"></i> Scan</button>
      </div>
      <div id="scan-err" style="color:#fca5a5;margin-top:14px;font-size:13px;"></div>
    </div>

    <div id="result-area" style="display:none;">
      <div class="glass-card">
        <div class="patient-bar">
          <div class="p-avatar" id="pat-av">?</div>
          <div>
            <div style="font-size:18px;font-weight:700;color:#0f3460;" id="pat-nm">—</div>
            <div style="color:#64748b;font-size:13px;" id="pat-mt">—</div>
            <span class="badge badge-success" style="margin-top:6px;"><i class="fas fa-check-circle"></i> Verified</span>
          </div>
        </div>

        <h4 style="font-size:15px;font-weight:700;color:#0f3460;margin-bottom:16px;"><i class="fas fa-prescription" style="color:#533483;margin-right:8px;"></i>Active Prescriptions</h4>
        <div id="ord-list"><div style="text-align:center;color:#94a3b8;padding:24px;background:#f8fafc;border-radius:12px;">No active prescriptions</div></div>
      </div>
    </div>
  </div>

  <!-- Profile Tab -->
  <div id="tab-profile" class="panel">
    <div class="glass-card">
      <h3 style="font-size:18px;font-weight:700;color:#0f3460;margin-bottom:24px;"><i class="fas fa-store" style="color:#10b981;margin-right:8px;"></i>Pharmacy Profile</h3>
      <div class="profile-section">
        <div class="info-box"><label>Pharmacy Name</label><div id="pr-name">—</div></div>
        <div class="info-box"><label>Email</label><div id="pr-email">—</div></div>
        <div class="info-box"><label>Username</label><div id="pr-username">—</div></div>
        <div class="info-box"><label>Phone</label><div id="pr-phone">—</div></div>
        <div class="info-box"><label>Address</label><div id="pr-address">—</div></div>
        <div class="info-box"><label>License No.</label><div id="pr-license">—</div></div>
        <div class="info-box"><label>Manager</label><div id="pr-manager">—</div></div>
        <div class="info-box"><label>Status</label><div id="pr-status">—</div></div>
      </div>
    </div>
  </div>
</div>

<script>
const token = localStorage.getItem('auth_token') || '';
const user  = JSON.parse(localStorage.getItem('auth_user') || '{}');
const h     = {'Content-Type':'application/json','Accept':'application/json','Authorization':'Bearer '+token};

if (!token || user.role !== 'pharmacy') { window.location.href = '/login'; }
document.getElementById('ph-name-hdr').textContent = user.name || '';

document.getElementById('nfc-uid').addEventListener('keydown', e => { if(e.key==='Enter') scanNfc(); });

function showTab(name, btn) {
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('tab-'+name).classList.add('active');
  if (name === 'profile') loadProfile();
}

async function loadProfile() {
  const r = await fetch('/api/my-profile', {headers: h});
  const {data} = await r.json();
  const ph = data.pharmacy || {};
  document.getElementById('pr-name').textContent    = ph.name || data.name || '—';
  document.getElementById('pr-email').textContent   = ph.email || data.email || '—';
  document.getElementById('pr-username').textContent= data.username || '—';
  document.getElementById('pr-phone').textContent   = ph.phone || '—';
  document.getElementById('pr-address').textContent = ph.address || '—';
  document.getElementById('pr-license').textContent = ph.license_number || '—';
  document.getElementById('pr-manager').textContent = ph.manager_name || '—';
  document.getElementById('pr-status').innerHTML    = ph.is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge" style="background:#fff1f2;color:#9f1239;">Inactive</span>';
}

async function scanNfc() {
  const uid = document.getElementById('nfc-uid').value.trim();
  if (!uid) return;
  document.getElementById('scan-err').textContent = '';
  document.getElementById('result-area').style.display = 'none';

  try {
    const r = await fetch('/api/ordonnances/by-nfc', {
      method:'POST', headers: h, body: JSON.stringify({nfc_uid: uid})
    });
    const data = await r.json();
    if (!data.success) { document.getElementById('scan-err').textContent = data.message; return; }

    const p = data.patient;
    const av = document.getElementById('pat-av');
    av.innerHTML = p.photo ? `<img src="/storage/${p.photo}" alt="${p.name}">` : p.name.charAt(0).toUpperCase();
    document.getElementById('pat-nm').textContent = p.name;
    document.getElementById('pat-mt').textContent = `${p.gender} · Age: ${p.age} · Blood: ${p.blood_type || 'N/A'}`;

    renderOrdonnances(data.ordonnances);
    document.getElementById('result-area').style.display = 'block';
  } catch(e) {
    document.getElementById('scan-err').textContent = 'Connection error.';
  }
}

function renderOrdonnances(ords) {
  const list = document.getElementById('ord-list');
  if (!ords.length) {
    list.innerHTML = '<div style="text-align:center;color:#94a3b8;padding:24px;background:#f8fafc;border-radius:12px;">No active prescriptions</div>';
    return;
  }
  list.innerHTML = ords.map(o => {
    const isDisp = o.status === 'dispensed';
    return `<div class="ord-card" id="ord-${o.id}">
      <div class="ord-top">
        <div>
          <div class="ord-title">Prescription #${o.id}</div>
          <div class="ord-meta">
            <i class="fas fa-user-md"></i> ${o.doctor.user.name} &nbsp;·&nbsp;
            <i class="fas fa-calendar"></i> ${o.issued_date} &nbsp;·&nbsp;
            Valid until: ${o.valid_until || 'No limit'}
          </div>
          ${isDisp ? `<div style="margin-top:8px;font-size:12px;color:#166534;"><i class="fas fa-check-circle"></i> Delivered — ${o.dispensed_at ? new Date(o.dispensed_at).toLocaleString() : ''}</div>` : ''}
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
          <span class="badge ${isDisp ? 'badge-dispensed' : 'badge-success'}">${isDisp ? 'Delivered ✓' : o.status}</span>
          <a href="/api/ordonnances/${o.id}/pdf?token=${token}" target="_blank" class="btn-pdf"><i class="fas fa-print"></i> Print</a>
          ${!isDisp ? `<button class="btn-deliver" onclick="dispense(${o.id})"><i class="fas fa-check-double"></i> Mark Delivered</button>` : `<button class="btn-deliver" disabled><i class="fas fa-check-circle"></i> Delivered</button>`}
        </div>
      </div>
      ${o.instructions ? `<div style="background:#fffbeb;color:#92400e;padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:12px;"><i class="fas fa-info-circle"></i> ${o.instructions}</div>` : ''}
      <div class="med-grid">
        ${(o.medications||[]).map(m => `<div class="med-chip">
          <div class="med-name"><i class="fas fa-capsules" style="color:#533483;margin-right:4px;"></i>${m.name}</div>
          <div class="med-det">${m.dosage} · ${m.frequency} · ${m.duration}</div>
        </div>`).join('')}
      </div>
    </div>`;
  }).join('');
}

let pendingDispenseId = null;

function dispense(id) {
  pendingDispenseId = id;
  document.getElementById('del-note').value = '';
  document.getElementById('del-status').innerHTML = '';
  document.getElementById('del-modal').classList.add('open');
}

async function confirmDispense() {
  if (!pendingDispenseId) return;
  const note = document.getElementById('del-note').value.trim();
  const btn  = document.getElementById('del-confirm-btn');
  const status = document.getElementById('del-status');
  btn.disabled = true;
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

  try {
    const r = await fetch(`/api/ordonnances/${pendingDispenseId}/dispense`, {
      method:'PATCH', headers: h, body: JSON.stringify({note})
    });
    const data = await r.json();
    if (data.success) {
      const now = new Date().toLocaleString();
      // Update card in DOM
      const card = document.getElementById(`ord-${pendingDispenseId}`);
      if (card) {
        const delivBtn = card.querySelector('.btn-deliver');
        if (delivBtn) { delivBtn.disabled=true; delivBtn.innerHTML='<i class="fas fa-check-circle"></i> Delivered'; delivBtn.style.background='#94a3b8'; }
        const badge = card.querySelector('.badge');
        if (badge) { badge.className='badge badge-dispensed'; badge.textContent='Delivered ✓'; }
        // Show note if provided
        if (note) {
          const noteDiv = document.createElement('div');
          noteDiv.style.cssText='background:#f0fdf4;border-left:4px solid #10b981;padding:10px 14px;border-radius:10px;font-size:13px;color:#166534;margin-top:10px;';
          noteDiv.innerHTML=`<i class="fas fa-sticky-note"></i> <strong>Pharmacist note:</strong> ${note}`;
          card.appendChild(noteDiv);
        }
        // Show delivery time
        const topDiv = card.querySelector('.ord-top > div');
        if (topDiv) topDiv.innerHTML += `<div style="margin-top:8px;font-size:12px;color:#166534;"><i class="fas fa-check-circle"></i> Delivered — ${now}</div>`;
      }
      document.getElementById('del-modal').classList.remove('open');
      pendingDispenseId = null;
    } else {
      status.innerHTML = `<span style="color:#9f1239;">${data.message}</span>`;
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-check-double"></i> Mark as Delivered';
    }
  } catch(e) {
    status.innerHTML = '<span style="color:#9f1239;">Connection error.</span>';
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-check-double"></i> Mark as Delivered';
  }
}

function logout() {
  localStorage.removeItem('auth_token');
  localStorage.removeItem('auth_user');
  window.location.href = '/login';
}
</script>
</body>
</html>
