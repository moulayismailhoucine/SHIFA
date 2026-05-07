<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SHIFA — Login</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: 'Inter', sans-serif;
  background: linear-gradient(135deg, #0f3460 0%, #16213e 50%, #533483 100%);
  min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px;
}
.login-container {
  display: flex; max-width: 900px; width: 100%;
  background: white; border-radius: 24px; overflow: hidden;
  box-shadow: 0 32px 80px rgba(0,0,0,0.4);
}
.login-left {
  background: linear-gradient(160deg, #0f3460, #533483);
  flex: 1; padding: 50px 40px; display: flex; flex-direction: column;
  justify-content: space-between; min-width: 260px;
}
.login-brand { color: white; }
.brand-logo {
  width: 56px; height: 56px; background: rgba(255,255,255,0.15);
  border-radius: 16px; display: flex; align-items: center; justify-content: center;
  font-size: 24px; margin-bottom: 20px;
}
.login-brand h1 { font-size: 28px; font-weight: 800; margin-bottom: 6px; }
.login-brand p { color: rgba(255,255,255,0.6); font-size: 13px; }
.login-features { margin-top: 32px; }
.feature-item {
  display: flex; align-items: center; gap: 14px;
  color: rgba(255,255,255,0.8); font-size: 13px; margin-bottom: 20px;
}
.feature-icon {
  width: 40px; height: 40px; background: rgba(255,255,255,0.12);
  border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0;
}
.login-footer { color: rgba(255,255,255,0.3); font-size: 11px; }

.login-right { flex: 1.1; padding: 50px 40px; display: flex; flex-direction: column; justify-content: center; }
.login-right h2 { font-size: 24px; font-weight: 700; margin-bottom: 6px; color: #0f3460; }
.login-right p.sub { color: #64748b; font-size: 13.5px; margin-bottom: 32px; }

.form-group { margin-bottom: 18px; }
.form-label { display: block; font-size: 12.5px; font-weight: 600; color: #334155; margin-bottom: 6px; }
.input-wrap { position: relative; }
.input-wrap .icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px; }
.form-control {
  width: 100%; padding: 12px 14px 12px 42px; border: 1.5px solid #e2e8f0;
  border-radius: 12px; font-size: 14px; font-family: inherit; color: #1e293b;
  transition: border-color 0.2s; background: #f8fafc;
}
.form-control:focus { outline: none; border-color: #0f3460; background: white; box-shadow: 0 0 0 3px rgba(15,52,96,0.08); }
.toggle-pw { position: absolute; right: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; cursor: pointer; font-size: 14px; }
.btn-login {
  width: 100%; padding: 13px; background: linear-gradient(135deg, #0f3460, #533483);
  color: white; border: none; border-radius: 12px; font-size: 15px; font-weight: 700;
  cursor: pointer; transition: opacity 0.2s, transform 0.1s;
  display: flex; align-items: center; justify-content: center; gap: 8px;
}
.btn-login:hover { opacity: 0.93; }
.btn-login:active { transform: scale(0.99); }
.divider { display: flex; align-items: center; gap: 12px; margin: 22px 0; color: #94a3b8; font-size: 12px; }
.divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: #e2e8f0; }
.nfc-btn {
  width: 100%; padding: 12px; background: white; color: #0f3460;
  border: 1.5px solid #e2e8f0; border-radius: 12px; font-size: 14px; font-weight: 600;
  cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;
  transition: all 0.2s;
}
.nfc-btn:hover { border-color: #0f3460; background: #f0f4ff; }
.alert { border-radius: 10px; padding: 12px 16px; font-size: 13px; margin-bottom: 16px; display: none; }
.alert-error { background: #fff1f2; color: #9f1239; border: 1px solid #fecdd3; }
.alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }

/* ========== MOBILE RESPONSIVE ========== */
@media (max-width: 768px) {
  body { padding: 0; }
  .login-container {
    flex-direction: column;
    max-width: 100%;
    border-radius: 0;
    min-height: 100vh;
  }
  .login-left {
    padding: 30px 24px;
    min-height: auto;
  }
  .login-right {
    padding: 30px 24px;
    flex: none;
  }
  .login-brand h1 { font-size: 24px; }
  .login-features { margin-top: 20px; }
  .feature-item { margin-bottom: 14px; }
  .login-footer { margin-top: 20px; }
}

@media (max-width: 480px) {
  .login-left {
    padding: 24px 20px;
    text-align: center;
  }
  .login-brand {
    display: flex;
    flex-direction: column;
    align-items: center;
  }
  .brand-logo {
    width: 48px;
    height: 48px;
    font-size: 20px;
  }
  .login-brand h1 { font-size: 22px; }
  .login-brand p { font-size: 12px; }
  .login-features {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
  }
  .feature-item {
    flex-direction: column;
    text-align: center;
    gap: 8px;
    font-size: 11px;
    margin-bottom: 0;
  }
  .feature-icon {
    width: 36px;
    height: 36px;
    font-size: 14px;
  }
  .login-right {
    padding: 24px 20px;
  }
  .login-right h2 { font-size: 20px; }
  .login-right p.sub { font-size: 13px; margin-bottom: 24px; }
  .form-label { font-size: 12px; }
  .form-control {
    padding: 14px 14px 14px 42px;
    font-size: 16px;
    min-height: 48px;
  }
  .btn-login, .nfc-btn {
    padding: 14px;
    font-size: 15px;
    min-height: 48px;
  }
  .login-footer { font-size: 10px; }
}

@media (max-width: 360px) {
  .login-left { padding: 20px 16px; }
  .login-right { padding: 20px 16px; }
  .login-features {
    grid-template-columns: 1fr;
    gap: 10px;
  }
  .feature-item {
    flex-direction: row;
    text-align: left;
    gap: 12px;
  }
}
</style>
</head>
<body>
<div class="login-container">
  <div class="login-left">
    <div class="login-brand">
      <div class="brand-logo"><i class="fas fa-hospital-alt" style="color:white;"></i></div>
      <h1>SHIFA</h1>
      <p>Hospital & Clinic Management</p>
    </div>
    <div class="login-features">
      <div class="feature-item"><div class="feature-icon"><i class="fas fa-wifi"></i></div><span>NFC patient authentication</span></div>
      <div class="feature-item"><div class="feature-icon"><i class="fas fa-file-medical"></i></div><span>Complete medical records</span></div>
      <div class="feature-item"><div class="feature-icon"><i class="fas fa-prescription"></i></div><span>PDF prescription generation</span></div>
      <div class="feature-item"><div class="feature-icon"><i class="fas fa-shield-alt"></i></div><span>Secure role-based access</span></div>
    </div>
    <div class="login-footer">© 2025 SHIFA Hospital Management. All rights reserved.</div>
  </div>

  <div class="login-right">
    <h2>Welcome back</h2>
    <p class="sub">Sign in with your credentials to access the system</p>

    <div class="alert alert-error" id="error-msg"></div>
    <div class="alert alert-success" id="success-msg"></div>

    <form id="login-form">
      <div class="form-group">
        <label class="form-label">Email, Username or Code</label>
        <div class="input-wrap">
          <i class="fas fa-user icon"></i>
          <input type="text" id="login" class="form-control" placeholder="admin@medisys.local or CODE123" required>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-wrap">
          <i class="fas fa-lock icon"></i>
          <input type="password" id="password" class="form-control" placeholder="••••••••" required>
          <i class="fas fa-eye toggle-pw" id="toggle-pw"></i>
        </div>
      </div>
      <button type="submit" class="btn-login" id="login-btn">
        <i class="fas fa-sign-in-alt"></i> Sign In
      </button>
    </form>

    <div class="divider">or</div>

    <button class="nfc-btn" id="nfc-btn">
      <i class="fas fa-wifi"></i> Authenticate Patient via NFC
    </button>

    <div id="nfc-section" style="display:none;margin-top:16px;">
      <div class="input-wrap">
        <i class="fas fa-wifi icon"></i>
        <input type="text" id="nfc-uid" class="form-control" placeholder="Scan or enter NFC UID...">
      </div>
      <button onclick="doNfcLogin()" class="btn-login" style="margin-top:10px;background:linear-gradient(135deg,#533483,#0f3460);">
        <i class="fas fa-check"></i> Authenticate Patient
      </button>
    </div>

    <div style="margin-top:24px; text-align:center;">
      <p style="color:#64748b; font-size:13px; margin-bottom:12px;">Don't have an account or NFC card?</p>
      <a href="/public-booking" class="nfc-btn" style="text-decoration:none; border-color:#533483; color:#533483;">
        <i class="fas fa-calendar-plus"></i> Book Appointment (Public)
      </a>
      <a href="/contact" style="display:inline-block; margin-top:16px; color:#64748b; font-size:13px; text-decoration:none; font-weight:600; transition:color 0.2s;" onmouseover="this.style.color='#0f3460'" onmouseout="this.style.color='#64748b'">
        <i class="fas fa-envelope"></i> Contact Support
      </a>
      <a href="/medical-chat" style="display:inline-block; margin-top:16px; color:#64748b; font-size:13px; text-decoration:none; font-weight:600; transition:color 0.2s;" onmouseover="this.style.color='#0f3460'" onmouseout="this.style.color='#64748b'">
        <i class="fas fa-robot"></i> AI Medical Chat
      </a>
    </div>
  </div>
</div>

<script>
// Toggle password visibility
document.getElementById('toggle-pw').addEventListener('click', function() {
  const pw = document.getElementById('password');
  pw.type = pw.type === 'password' ? 'text' : 'password';
  this.className = pw.type === 'password' ? 'fas fa-eye toggle-pw' : 'fas fa-eye-slash toggle-pw';
});

// Toggle NFC section
document.getElementById('nfc-btn').addEventListener('click', function() {
  const s = document.getElementById('nfc-section');
  s.style.display = s.style.display === 'none' ? 'block' : 'none';
  if (s.style.display === 'block') document.getElementById('nfc-uid').focus();
});

// Staff login
document.getElementById('login-form').addEventListener('submit', async function(e) {
  e.preventDefault();
  const btn = document.getElementById('login-btn');
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...';
  btn.disabled = true;
  hideAlerts();

  try {
    const resp = await fetch('/api/login', {
      method: 'POST',
      credentials: 'include',
      headers: {'Content-Type':'application/json','Accept':'application/json'},
      body: JSON.stringify({
        login: document.getElementById('login').value,
        password: document.getElementById('password').value,
      })
    });
    const data = await resp.json();
    if (data.success) {
      localStorage.setItem('auth_token', data.token);
      localStorage.setItem('auth_user', JSON.stringify(data.user));
      showSuccess('Login successful! Redirecting...');
      const role = data.user.role;
      let target = '/dashboard';
      if (role === 'pharmacy') target = '/pharmacy-dashboard';
      else if (role === 'lab')  target = '/lab-dashboard';
      setTimeout(() => window.location.href = target, 800);
    } else {
      showError(data.message || 'Invalid credentials.');
    }
  } catch(e) {
    showError('Connection error. Ensure the server is running.');
  } finally {
    btn.innerHTML = '<i class="fas fa-sign-in-alt"></i> Sign In';
    btn.disabled = false;
  }
});

async function doNfcLogin() {
  const uid = document.getElementById('nfc-uid').value.trim();
  if (!uid) return;
  hideAlerts();
  try {
    const resp = await fetch('/api/nfc-login', {
      method: 'POST',
      headers: {'Content-Type':'application/json','Accept':'application/json'},
      body: JSON.stringify({nfc_uid: uid})
    });
    const data = await resp.json();
    if (data.success) {
      sessionStorage.setItem('patient_token', data.token);
      sessionStorage.setItem('patient_data', JSON.stringify(data.patient));
      showSuccess(`Welcome, ${data.patient.name}! Loading your profile...`);
      setTimeout(() => window.location.href = '/patient-profile', 900);
    } else {
      showError(data.message);
    }
  } catch(e) {
    showError('Connection error.');
  }
}

document.getElementById('nfc-uid').addEventListener('keydown', e => { if (e.key === 'Enter') doNfcLogin(); });

function showError(msg) { const el = document.getElementById('error-msg'); el.textContent = msg; el.style.display = 'block'; }
function showSuccess(msg) { const el = document.getElementById('success-msg'); el.textContent = msg; el.style.display = 'block'; }
function hideAlerts() {
  document.getElementById('error-msg').style.display = 'none';
  document.getElementById('success-msg').style.display = 'none';
}
</script>
</body>
</html>
