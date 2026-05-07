<!DOCTYPE html>
<html lang="en" dir="ltr" id="html-root">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SHIFA — Public Booking</title>
<meta name="description" content="Book an appointment at SHIFA Hospital quickly and easily without an account.">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+Arabic:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* ── CSS Variables ── */
:root {
  --primary: #1565C0;
  --primary-light: #42A5F5;
  --secondary: #533483;
  --accent: #e94560;
  --bg: #f4f7fc;
  --surface: #ffffff;
  --text: #1a1a2e;
  --text-muted: #64748b;
  --border: #e2e8f0;
  --input-bg: #f8fafc;
  --shadow: rgba(21,101,192,0.10);
  --radius-lg: 20px;
  --radius-md: 14px;
  --radius-sm: 10px;
}
[data-theme="dark"] {
  --bg: #0d1117;
  --surface: #161b22;
  --text: #f0f6fc;
  --text-muted: #8b949e;
  --border: #30363d;
  --input-bg: #21262d;
  --shadow: rgba(0,0,0,0.4);
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body {
  font-family: 'Inter', 'Noto Sans Arabic', sans-serif;
  background-color: var(--bg);
  color: var(--text);
  line-height: 1.6;
  transition: background 0.3s, color 0.3s;
}
[dir="rtl"] body { font-family: 'Noto Sans Arabic', 'Inter', sans-serif; }

/* ── Navbar ── */
.navbar {
  background: var(--surface);
  padding: 16px 40px;
  display: flex; justify-content: space-between; align-items: center;
  box-shadow: 0 2px 20px var(--shadow);
  position: sticky; top: 0; z-index: 100;
  transition: background 0.3s;
}
.logo {
  font-size: 22px; font-weight: 800; color: var(--primary);
  display: flex; align-items: center; gap: 10px;
}
.logo-icon {
  width: 38px; height: 38px; border-radius: 10px;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  display: flex; align-items: center; justify-content: center; color: white;
}
.nav-actions { display: flex; gap: 12px; align-items: center; }
.nav-link { text-decoration: none; color: var(--text-muted); font-weight: 600; font-size: 13px; transition: color 0.2s; }
.nav-link:hover { color: var(--primary); }
.btn-toggle {
  padding: 7px 14px; border-radius: 8px; border: 1.5px solid var(--border);
  background: var(--input-bg); color: var(--text); font-size: 13px; font-weight: 700;
  cursor: pointer; transition: all 0.2s; font-family: inherit;
}
.btn-toggle:hover { border-color: var(--primary); color: var(--primary); }

/* ── Container ── */
.container { max-width: 820px; margin: 40px auto; padding: 0 20px; }

/* ── Booking card ── */
.booking-card {
  background: var(--surface);
  border-radius: 28px;
  padding: 48px;
  box-shadow: 0 24px 60px var(--shadow);
  border: 1px solid var(--border);
  transition: background 0.3s;
}
@media (max-width: 600px) { .booking-card { padding: 28px 20px; } }

/* ── Header ── */
.booking-header { text-align: center; margin-bottom: 36px; }
.booking-header h1 {
  font-size: 30px; font-weight: 800; color: var(--primary);
  margin-bottom: 8px; letter-spacing: -0.5px;
}
.booking-header p { color: var(--text-muted); font-size: 15px; }

/* ── Form elements ── */
.form-group { margin-bottom: 20px; }
.form-label {
  display: block; font-size: 13px; font-weight: 700;
  color: var(--text); margin-bottom: 8px; letter-spacing: 0.2px;
}
.form-control {
  width: 100%; padding: 13px 16px;
  border: 1.5px solid var(--border);
  border-radius: var(--radius-md);
  font-size: 14px; font-family: inherit; color: var(--text);
  background: var(--input-bg);
  transition: all 0.2s;
}
.form-control:focus {
  outline: none; border-color: var(--primary); background: var(--surface);
  box-shadow: 0 0 0 4px rgba(21,101,192,0.08);
}
[data-theme="dark"] .form-control:focus { box-shadow: 0 0 0 4px rgba(21,101,192,0.2); }

.grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
@media (max-width: 600px) { .grid { grid-template-columns: 1fr; } }

/* ── Time slots ── */
.slots-grid {
  display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
  gap: 10px; margin-top: 10px;
}
.slot-btn {
  padding: 10px 8px; border: 1.5px solid var(--border); border-radius: 10px;
  background: var(--input-bg); color: var(--text); font-size: 13px; font-weight: 600;
  cursor: pointer; transition: all 0.2s; text-align: center; font-family: inherit;
}
.slot-btn:hover { border-color: var(--primary); color: var(--primary); background: rgba(21,101,192,0.06); }
.slot-btn.selected { background: var(--primary); color: white; border-color: var(--primary); }

/* ── Doctor info card (shown after selection) ── */
#doctor-info-card {
  display: none; margin-bottom: 20px;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  border-radius: var(--radius-md); padding: 18px 20px;
  box-shadow: 0 8px 24px rgba(21,101,192,0.25);
}
#doctor-info-card.visible { display: block; }
#doctor-info-name { color: #fff; font-weight: 800; font-size: 16px; margin-bottom: 4px; }
#doctor-info-specialty { color: rgba(255,255,255,0.85); font-size: 13px; }

/* ── Clinic Address Card ── */
.clinic-address-card {
  display: none; margin-bottom: 20px;
  background: rgba(21,101,192,0.06);
  border: 1.5px solid rgba(21,101,192,0.2);
  border-radius: var(--radius-md); padding: 14px 16px;
  cursor: pointer; transition: all 0.2s;
  text-decoration: none;
}
[data-theme="dark"] .clinic-address-card {
  background: rgba(21,101,192,0.12);
  border-color: rgba(21,101,192,0.35);
}
.clinic-address-card:hover {
  border-color: var(--primary);
  background: rgba(21,101,192,0.1);
  transform: translateY(-1px);
  box-shadow: 0 6px 16px rgba(21,101,192,0.15);
}
.clinic-address-card.visible { display: flex; align-items: center; gap: 14px; }
.clinic-address-icon {
  width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  display: flex; align-items: center; justify-content: center; color: white;
  font-size: 18px; box-shadow: 0 4px 10px rgba(21,101,192,0.3);
}
.clinic-address-text { flex: 1; }
.clinic-address-label {
  font-size: 11px; font-weight: 700; color: var(--text-muted);
  text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 3px;
}
.clinic-address-value { font-size: 14px; font-weight: 600; color: var(--text); }
.open-maps-btn {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 8px 14px; border-radius: 10px; font-size: 12px; font-weight: 700;
  background: linear-gradient(135deg, var(--primary), var(--primary-light));
  color: white; white-space: nowrap; flex-shrink: 0;
}

/* ── Booking button ── */
.btn-book {
  width: 100%; padding: 16px;
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  color: white; border: none; border-radius: var(--radius-md);
  font-size: 16px; font-weight: 700; cursor: pointer; transition: all 0.2s;
  margin-top: 16px; display: flex; align-items: center; justify-content: center; gap: 10px;
  font-family: inherit; letter-spacing: 0.3px;
}
.btn-book:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(21,101,192,0.25); }
.btn-book:disabled { background: #cbd5e1; cursor: not-allowed; transform: none; box-shadow: none; }
[data-theme="dark"] .btn-book:disabled { background: #30363d; }

/* ── Alerts ── */
.alert { padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; display: none; }
.alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.alert-error   { background: #fff1f2; color: #9f1239; border: 1px solid #fecdd3; }
[data-theme="dark"] .alert-success { background: rgba(22,101,52,0.15); color: #4ade80; border-color: rgba(74,222,128,0.3); }
[data-theme="dark"] .alert-error   { background: rgba(159,18,57,0.15); color: #fb7185; border-color: rgba(251,113,133,0.3); }

/* ── Security notice ── */
.security-notice {
  background: rgba(21,101,192,0.05); border: 1px solid rgba(21,101,192,0.15);
  border-radius: 10px; padding: 12px 16px; margin-bottom: 20px;
  font-size: 12px; color: var(--primary); display: flex; align-items: center; gap: 8px;
}

/* ── Section divider ── */
.section-label {
  font-size: 13px; font-weight: 800; color: var(--text-muted);
  text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 14px;
  padding-bottom: 8px; border-bottom: 1.5px solid var(--border);
}
</style>
</head>
<body>

<nav class="navbar">
  <div class="logo">
    <div class="logo-icon"><i class="fas fa-hospital-alt"></i></div>
    SHIFA
  </div>
  <div class="nav-actions">
    <a href="/medical-chat" class="nav-link"><i class="fas fa-robot"></i> <span data-en="AI Chat" data-ar="الذكاء الاصطناعي">AI Chat</span></a>
    <a href="/contact" class="nav-link"><i class="fas fa-envelope"></i> <span data-en="Contact" data-ar="تواصل معنا">Contact</span></a>
    <a href="/login" class="nav-link"><i class="fas fa-arrow-left"></i> <span data-en="Login" data-ar="تسجيل الدخول">Login</span></a>
    <button class="btn-toggle" id="lang-toggle" onclick="toggleLang()">ع</button>
    <button class="btn-toggle" id="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon"></i></button>
  </div>
</nav>

<div class="container">
  <div class="booking-card">
    <div class="booking-header">
      <h1><span data-en="Book an Appointment" data-ar="احجز موعدك">Book an Appointment</span></h1>
      <p><span data-en="Quick and easy booking without an account" data-ar="حجز سريع وسهل بدون حساب">Quick and easy booking without an account</span></p>
    </div>

    <div class="security-notice">
      <i class="fas fa-shield-alt"></i>
      <span data-en="This form is protected against spam and duplicate bookings." data-ar="هذا النموذج محمي ضد البريد العشوائي والحجوزات المكررة.">
        This form is protected against spam and duplicate bookings.
      </span>
    </div>

    <div class="alert alert-success" id="success-msg"></div>
    <div class="alert alert-error" id="error-msg"></div>

    <form id="booking-form">
      {{-- Honeypot --}}
      <div style="display:none; position:absolute; left:-9999px;" aria-hidden="true">
        <input type="text" name="website" id="hp_website" value="" tabindex="-1" autocomplete="off">
        <input type="email" name="confirm_email" id="hp_confirm_email" value="" tabindex="-1" autocomplete="off">
      </div>

      <p class="section-label" data-en="Your Details" data-ar="بياناتك">Your Details</p>

      <div class="grid">
        <div class="form-group">
          <label class="form-label" for="guest_name">
            <span data-en="Full Name" data-ar="الاسم الكامل">Full Name</span>
          </label>
          <input type="text" id="guest_name" class="form-control" placeholder="John Doe" required minlength="2" maxlength="255">
        </div>
        <div class="form-group">
          <label class="form-label" for="guest_phone">
            <span data-en="Phone Number" data-ar="رقم الهاتف">Phone Number</span>
          </label>
          <input type="tel" id="guest_phone" class="form-control" placeholder="0555 XX XX XX" required
                 pattern="^[0-9+\-\s\(\)]{7,20}$" title="Enter a valid phone number">
        </div>
      </div>

      <p class="section-label" data-en="Select Doctor &amp; Time" data-ar="اختر الطبيب والوقت">Select Doctor &amp; Time</p>

      <div class="form-group">
        <label class="form-label" for="doctor_id">
          <span data-en="Select Doctor" data-ar="اختر الطبيب">Select Doctor</span>
        </label>
        <select id="doctor_id" class="form-control" required onchange="onDoctorChange()">
          <option value=""><span data-en="Choose a doctor..." data-ar="اختر طبيباً...">Choose a doctor...</span></option>
        </select>
      </div>

      {{-- Doctor info banner (appears after selection) --}}
      <div id="doctor-info-card">
        <div id="doctor-info-name"></div>
        <div id="doctor-info-specialty"></div>
      </div>

      {{-- Clinic Address card (appears if doctor has address) --}}
      <a id="clinic-address-card" class="clinic-address-card" href="#" target="_blank" onclick="openMaps(event)">
        <div class="clinic-address-icon"><i class="fas fa-map-marker-alt"></i></div>
        <div class="clinic-address-text">
          <div class="clinic-address-label" data-en="Clinic Address" data-ar="عنوان العيادة">Clinic Address</div>
          <div class="clinic-address-value" id="clinic-address-value"></div>
        </div>
        <div class="open-maps-btn">
          <i class="fas fa-location-arrow"></i>
          <span data-en="Open in Maps" data-ar="فتح في الخرائط">Open in Maps</span>
        </div>
      </a>

      <div class="form-group">
        <label class="form-label" for="booking_date">
          <span data-en="Select Date" data-ar="اختر التاريخ">Select Date</span>
        </label>
        <input type="date" id="booking_date" class="form-control" min="<?= date('Y-m-d') ?>" required onchange="loadSlots()">
      </div>

      <div id="slots-section" style="display:none;" class="form-group">
        <label class="form-label"><span data-en="Available Time Slots" data-ar="المواعيد المتاحة">Available Time Slots</span></label>
        <div class="slots-grid" id="slots-container"></div>
        <input type="hidden" id="selected_time" required>
      </div>

      <p class="section-label" data-en="Reason for Visit" data-ar="سبب الزيارة">Reason for Visit</p>

      <div class="form-group">
        <textarea id="reason" class="form-control" rows="3"
          placeholder="Brief description..." maxlength="500"></textarea>
      </div>

      <button type="submit" class="btn-book" id="submit-btn" disabled>
        <i class="fas fa-calendar-check"></i>
        <span data-en="Confirm Booking" data-ar="تأكيد الحجز">Confirm Booking</span>
      </button>
    </form>
  </div>
</div>

<script>
let doctors = [];
let currentLang = 'en';
let currentDoctorAddress = '';

// ── Language ──────────────────────────────────────────
function toggleLang() {
  currentLang = currentLang === 'en' ? 'ar' : 'en';
  const html = document.getElementById('html-root');
  html.lang = currentLang;
  html.dir = currentLang === 'ar' ? 'rtl' : 'ltr';
  document.getElementById('lang-toggle').textContent = currentLang === 'ar' ? 'EN' : 'ع';
  document.querySelectorAll('[data-en]').forEach(el => {
    el.textContent = currentLang === 'ar' ? el.dataset.ar : el.dataset.en;
  });
}

// ── Theme ─────────────────────────────────────────────
function toggleTheme() {
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  document.documentElement.setAttribute('data-theme', isDark ? '' : 'dark');
  document.getElementById('theme-toggle').innerHTML =
    isDark ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
}

// ── Doctors ───────────────────────────────────────────
async function loadDoctors() {
  try {
    const r = await fetch('/api/public/doctors');
    const d = await r.json();
    doctors = d.data || [];
    const select = document.getElementById('doctor_id');
    doctors.forEach(doc => {
      const opt = document.createElement('option');
      opt.value = doc.id;
      opt.textContent = `${doc.name} (${doc.specialty})`;
      select.appendChild(opt);
    });
  } catch(e) {
    showError('Failed to load doctors. Please refresh the page.');
  }
}

function onDoctorChange() {
  const doctorId = document.getElementById('doctor_id').value;
  const doc = doctors.find(d => d.id == doctorId);

  // Doctor info banner
  const infoCard = document.getElementById('doctor-info-card');
  const addrCard = document.getElementById('clinic-address-card');

  if (doc) {
    document.getElementById('doctor-info-name').textContent = doc.name;
    document.getElementById('doctor-info-specialty').textContent = doc.specialty;
    infoCard.classList.add('visible');

    // Clinic address
    if (doc.clinic_address) {
      currentDoctorAddress = doc.clinic_address;
      document.getElementById('clinic-address-value').textContent = doc.clinic_address;
      addrCard.classList.add('visible');
    } else {
      currentDoctorAddress = '';
      addrCard.classList.remove('visible');
    }
  } else {
    infoCard.classList.remove('visible');
    addrCard.classList.remove('visible');
    currentDoctorAddress = '';
  }

  loadSlots();
}

function openMaps(e) {
  e.preventDefault();
  if (!currentDoctorAddress) return;
  const encoded = encodeURIComponent(currentDoctorAddress);
  window.open(`https://www.google.com/maps/search/?api=1&query=${encoded}`, '_blank');
}

// ── Slots ─────────────────────────────────────────────
async function loadSlots() {
  const doctorId = document.getElementById('doctor_id').value;
  const date = document.getElementById('booking_date').value;
  if (!doctorId || !date) return;

  const container = document.getElementById('slots-container');
  container.innerHTML = '<p style="font-size:12px; color:#94a3b8;"><i class="fas fa-spinner fa-spin"></i> Checking availability...</p>';
  document.getElementById('slots-section').style.display = 'block';
  document.getElementById('selected_time').value = '';
  document.getElementById('submit-btn').disabled = true;

  try {
    const r = await fetch(`/api/public/available-slots?doctor_id=${doctorId}&date=${date}`);
    const d = await r.json();
    const slots = d.slots || [];
    const suggestion = d.suggestion;

    if (slots.length === 0) {
      let html = '<p style="grid-column:1/-1; color:#e94560; font-size:13px; font-weight:600;">No slots available for this day.</p>';
      if (suggestion) {
        html += `<div style="grid-column:1/-1; margin-top:12px; padding:16px; background:rgba(21,101,192,0.06); border-radius:12px; border:1px solid rgba(21,101,192,0.2);">
          <p style="font-size:13px; color:var(--primary); margin-bottom:10px;"><i class="fas fa-lightbulb"></i> Next available: <strong>${suggestion.date} at ${suggestion.time}</strong></p>
          <button type="button" class="slot-btn selected" style="width:auto; padding:8px 16px;" onclick="applySuggestion('${suggestion.date}', '${suggestion.time}')">Switch to this slot</button>
        </div>`;
      }
      container.innerHTML = html;
    } else {
      container.innerHTML = slots.map(s => `<button type="button" class="slot-btn" onclick="selectSlot(this, '${s}')">${s}</button>`).join('');
    }
  } catch (e) {
    container.innerHTML = '<p style="color:#e94560;">Error loading slots. Please try again.</p>';
  }
}

function selectSlot(btn, time) {
  document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
  btn.classList.add('selected');
  document.getElementById('selected_time').value = time;
  document.getElementById('submit-btn').disabled = false;
}

function applySuggestion(date, time) {
  document.getElementById('booking_date').value = date;
  loadSlots().then(() => {
    const buttons = document.querySelectorAll('.slot-btn');
    for (let b of buttons) {
      if (b.textContent === time) { selectSlot(b, time); break; }
    }
  });
}

// ── Submit ────────────────────────────────────────────
document.getElementById('booking-form').addEventListener('submit', async function(e) {
  e.preventDefault();

  if (document.getElementById('hp_website').value || document.getElementById('hp_confirm_email').value) return;

  const phone = document.getElementById('guest_phone').value.trim();
  if (!/^[0-9+\-\s\(\)]{7,20}$/.test(phone)) {
    showError('Please enter a valid phone number.');
    return;
  }

  const btn = document.getElementById('submit-btn');
  btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
  btn.disabled = true;
  hideAlerts();

  const payload = {
    guest_name:   document.getElementById('guest_name').value.trim(),
    guest_phone:  phone,
    doctor_id:    document.getElementById('doctor_id').value,
    scheduled_at: `${document.getElementById('booking_date').value} ${document.getElementById('selected_time').value}:00`,
    reason:       document.getElementById('reason').value.trim(),
    website:      '',
    confirm_email: '',
  };

  try {
    const r = await fetch('/api/public/book-appointment', {
      method: 'POST',
      headers: {'Content-Type': 'application/json', 'Accept': 'application/json'},
      body: JSON.stringify(payload)
    });
    const d = await r.json();

    if (r.status === 429) {
      showError('Too many booking attempts. Please wait a moment and try again.');
    } else if (r.status === 403) {
      showError('Your request was blocked due to suspicious activity. Please contact us directly.');
    } else if (d.success) {
      showSuccess(currentLang === 'ar'
        ? '✅ تم حجز موعدك بنجاح! سنتصل بك قريباً.'
        : '✅ Appointment booked successfully! We will contact you shortly.');
      document.getElementById('booking-form').reset();
      document.getElementById('slots-section').style.display = 'none';
      document.getElementById('doctor-info-card').classList.remove('visible');
      document.getElementById('clinic-address-card').classList.remove('visible');
    } else {
      showError(d.message || 'Error booking appointment. Please try again.');
    }
  } catch (e) {
    showError('Connection error. Please check your internet and try again.');
  } finally {
    btn.innerHTML = `<i class="fas fa-calendar-check"></i> <span data-en="Confirm Booking" data-ar="تأكيد الحجز">${currentLang === 'ar' ? 'تأكيد الحجز' : 'Confirm Booking'}</span>`;
    btn.disabled = false;
  }
});

function showError(msg)   { const el = document.getElementById('error-msg'); el.textContent = msg; el.style.display = 'block'; window.scrollTo({top:0,behavior:'smooth'}); }
function showSuccess(msg) { const el = document.getElementById('success-msg'); el.textContent = msg; el.style.display = 'block'; window.scrollTo({top:0,behavior:'smooth'}); }
function hideAlerts()     { document.getElementById('error-msg').style.display = 'none'; document.getElementById('success-msg').style.display = 'none'; }

// Detect system theme
if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
  document.documentElement.setAttribute('data-theme', 'dark');
  document.getElementById('theme-toggle').innerHTML = '<i class="fas fa-sun"></i>';
}

loadDoctors();
</script>

</body>
</html>
