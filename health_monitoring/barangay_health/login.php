<?php
session_start();

if (isset($_SESSION['staff_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

$conn = new mysqli('localhost', 'root', '', 'barangay_health_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // SECURE QUERY (NO SQL INJECTION)
    $stmt = $conn->prepare("SELECT * FROM staff WHERE username = ? AND isactive = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        $error = 'not_registered';
    } else {

        // PASSWORD CHECK (hashed)
        if (password_verify($password, $user['password'])) {

            $_SESSION['staff_id'] = $user['staffid'];
            $_SESSION['staff_name'] = $user['fullname'];
            $_SESSION['staff_position'] = $user['position'];

            header('Location: index.php');
            exit;

        } else {
            $error = 'wrong_password';
        }
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In — Barangay Health Center</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,600;1,9..144,300&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --forest:    #1a3d2b;
  --forest-m:  #22503a;
  --forest-l:  #2d6b4e;
  --sage:      #4a7c5e;
  --sage-l:    #85b89a;
  --mint:      #c8e6d4;
  --mint-l:    #e8f5ed;
  --cream:     #faf7f2;
  --cream-d:   #f0ebe0;
  --warm:      #7c6a52;
  --warm-l:    #a08d72;
  --gold:      #c9943a;
  --gold-l:    #e8c17a;
  --white:     #ffffff;
  --text:      #1a1a1a;
  --muted:     #6b6b6b;
  --error-bg:  #fff4f4;
  --error:     #c0392b;
}

body {
  font-family: 'Plus Jakarta Sans', sans-serif;
  background: var(--cream);
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1fr 1fr;
  position: relative;
  overflow: hidden;
}

/* ── Decorative blobs ── */
body::before {
  content: '';
  position: fixed;
  top: -120px; left: -120px;
  width: 480px; height: 480px;
  background: radial-gradient(circle, rgba(26,61,43,.07) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}
body::after {
  content: '';
  position: fixed;
  bottom: -80px; right: 45%;
  width: 300px; height: 300px;
  background: radial-gradient(circle, rgba(201,148,58,.07) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}

/* ═══════════════════════════════
   LEFT PANEL — hero side
   ═══════════════════════════════ */
.hero {
  background: var(--forest);
  padding: 3.5rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  position: relative;
  overflow: hidden;
}

/* Abstract decorative ring */
.hero::before {
  content: '';
  position: absolute;
  top: -160px; right: -160px;
  width: 460px; height: 460px;
  border-radius: 50%;
  border: 64px solid rgba(255,255,255,.04);
  pointer-events: none;
}
.hero::after {
  content: '';
  position: absolute;
  bottom: -80px; left: -80px;
  width: 260px; height: 260px;
  border-radius: 50%;
  border: 40px solid rgba(200,230,212,.06);
  pointer-events: none;
}

/* Small leaf accent pattern */
.leaf-grid {
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(circle, rgba(255,255,255,.025) 1px, transparent 1px);
  background-size: 36px 36px;
  pointer-events: none;
}

.hero-top { position: relative; z-index: 1; }

.brand {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 3.5rem;
}

.brand-icon {
  width: 48px; height: 48px;
  background: rgba(200,230,212,.12);
  border: 1px solid rgba(200,230,212,.2);
  border-radius: 14px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem;
}

.brand-name {
  font-family: 'Fraunces', serif;
  font-size: .8rem;
  font-weight: 500;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--sage-l);
}

.hero-headline {
  font-family: 'Fraunces', serif;
  font-weight: 300;
  font-size: clamp(2rem, 3.2vw, 2.8rem);
  line-height: 1.2;
  letter-spacing: -.02em;
  color: var(--white);
  max-width: 360px;
  margin-bottom: 1.25rem;
}

.hero-headline em {
  font-style: italic;
  color: var(--gold-l);
}

.hero-sub {
  color: rgba(255,255,255,.5);
  font-size: .9rem;
  line-height: 1.7;
  max-width: 320px;
}

/* Stats strip */
.stats {
  position: relative; z-index: 1;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1px;
  background: rgba(255,255,255,.07);
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid rgba(255,255,255,.07);
}

.stat {
  padding: 1.25rem 1.5rem;
  background: rgba(255,255,255,.03);
}
.stat:nth-child(3), .stat:nth-child(4) {
  border-top: 1px solid rgba(255,255,255,.06);
}

.stat-num {
  font-family: 'Fraunces', serif;
  font-size: 1.9rem;
  font-weight: 500;
  color: var(--white);
  letter-spacing: -.03em;
  line-height: 1;
  margin-bottom: .35rem;
}

.stat-label {
  font-size: .75rem;
  color: rgba(255,255,255,.4);
  letter-spacing: .04em;
  text-transform: uppercase;
}

.hero-bottom { position: relative; z-index: 1; }

.pill-tags {
  display: flex; flex-wrap: wrap; gap: .5rem;
}
.pill {
  display: flex; align-items: center; gap: .4rem;
  padding: .4rem .9rem;
  border-radius: 100px;
  background: rgba(255,255,255,.07);
  border: 1px solid rgba(255,255,255,.1);
  font-size: .78rem;
  color: rgba(255,255,255,.65);
  letter-spacing: .02em;
}
.pill-dot {
  width: 6px; height: 6px;
  border-radius: 50%;
  background: var(--gold-l);
  flex-shrink: 0;
}

/* ═══════════════════════════════
   RIGHT PANEL — form side
   ═══════════════════════════════ */
.form-side {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 3rem 4rem;
  background: var(--cream);
  position: relative;
}

.form-wrap {
  width: 100%;
  max-width: 380px;
}

.form-eyebrow {
  display: flex;
  align-items: center;
  gap: .6rem;
  margin-bottom: 2rem;
}

.eyebrow-line {
  width: 24px; height: 2px;
  background: var(--gold);
  border-radius: 2px;
}

.eyebrow-text {
  font-size: .75rem;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--warm-l);
  font-weight: 600;
}

.form-title {
  font-family: 'Fraunces', serif;
  font-size: 2.1rem;
  font-weight: 500;
  letter-spacing: -.03em;
  color: var(--forest);
  line-height: 1.15;
  margin-bottom: .6rem;
}

.form-subtitle {
  font-size: .88rem;
  color: var(--muted);
  margin-bottom: 2.5rem;
  line-height: 1.6;
}

/* Error alert */
.alert {
  display: flex;
  align-items: flex-start;
  gap: .75rem;
  padding: .9rem 1.1rem;
  background: var(--error-bg);
  border-left: 3px solid var(--error);
  border-radius: 10px;
  margin-bottom: 1.75rem;
  animation: slideIn .25s ease;
}
.alert-icon { font-size: 1rem; flex-shrink: 0; margin-top: .05rem; }
.alert-text { font-size: .84rem; color: var(--error); font-weight: 500; line-height: 1.5; }

@keyframes slideIn { from { opacity:0; transform: translateY(-6px); } to { opacity:1; transform:none; } }

/* Form fields */
.field { margin-bottom: 1.25rem; }

.field-label {
  display: block;
  font-size: .78rem;
  font-weight: 600;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: var(--warm);
  margin-bottom: .55rem;
}

.input-group {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 1rem; top: 50%;
  transform: translateY(-50%);
  font-size: .95rem;
  opacity: .45;
  pointer-events: none;
}

.input-group input {
  width: 100%;
  padding: .85rem 1rem .85rem 2.8rem;
  border: 1.5px solid var(--cream-d);
  border-radius: 12px;
  background: var(--white);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: .92rem;
  color: var(--text);
  transition: border-color .2s, box-shadow .2s;
  -webkit-appearance: none;
}

.input-group input:focus {
  outline: none;
  border-color: var(--sage);
  box-shadow: 0 0 0 4px rgba(74,124,94,.1);
  background: var(--white);
}

.input-group input::placeholder { color: #bbb; }

/* Options row */
.options-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.remember {
  display: flex; align-items: center; gap: .5rem;
  cursor: pointer;
  font-size: .84rem;
  color: var(--muted);
  user-select: none;
}

.remember input[type=checkbox] {
  width: 16px; height: 16px;
  border-radius: 4px;
  accent-color: var(--sage);
  cursor: pointer;
}

/* Submit button */
.btn-submit {
  width: 100%;
  padding: .9rem 1.5rem;
  border: none;
  border-radius: 12px;
  background: var(--forest);
  color: var(--white);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: .95rem;
  font-weight: 600;
  letter-spacing: .02em;
  cursor: pointer;
  transition: background .2s, transform .15s, box-shadow .2s;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: .5rem;
  position: relative;
  overflow: hidden;
}

.btn-submit::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, transparent 40%, rgba(255,255,255,.06) 100%);
  pointer-events: none;
}

.btn-submit:hover {
  background: var(--forest-m);
  box-shadow: 0 8px 24px rgba(26,61,43,.22);
  transform: translateY(-1px);
}

.btn-submit:active { transform: translateY(0); }

.btn-arrow {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 24px; height: 24px;
  background: rgba(255,255,255,.12);
  border-radius: 6px;
  font-size: .85rem;
  transition: transform .2s;
}

.btn-submit:hover .btn-arrow { transform: translateX(3px); }

/* Divider */
.divider {
  display: flex; align-items: center; gap: 1rem;
  margin: 1.75rem 0;
  color: var(--muted);
  font-size: .78rem;
  letter-spacing: .06em;
  text-transform: uppercase;
}
.divider::before, .divider::after {
  content: '';
  flex: 1;
  height: 1px;
  background: var(--cream-d);
}

/* Register link */
.register-cta {
  background: var(--white);
  border: 1.5px solid var(--cream-d);
  border-radius: 12px;
  padding: .85rem 1.25rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.register-cta-text { font-size: .85rem; color: var(--muted); }
.register-cta-text strong { color: var(--text); font-weight: 600; }

.register-link {
  display: flex; align-items: center; gap: .4rem;
  font-size: .84rem;
  font-weight: 600;
  color: var(--forest-l);
  text-decoration: none;
  padding: .4rem .85rem;
  border-radius: 8px;
  background: var(--mint-l);
  transition: background .18s;
}
.register-link:hover { background: var(--mint); }

/* Footer */
.form-footer {
  margin-top: 2.25rem;
  text-align: center;
  font-size: .75rem;
  color: var(--warm-l);
}

/* Entrance animation */
.form-wrap { animation: fadeUp .4s ease both; }
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(18px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* ── Responsive ── */
@media (max-width: 900px) {
  body { grid-template-columns: 1fr; }
  .hero { display: none; }
  .form-side { padding: 2.5rem 1.5rem; }
}
</style>
</head>
<body>

<!-- ── Left: Hero Panel ── -->
<div class="hero">
  <div class="leaf-grid"></div>

  <div class="hero-top">
    <div class="brand">
      <div class="brand-icon">🏥</div>
      <span class="brand-name">Barangay Health Center</span>
    </div>

    <h1 class="hero-headline">
      <em>Caring</em> for the community,<br>one record at a time.
    </h1>
    <p class="hero-sub">
      A unified system for managing patient health records, appointments, and daily vitals. Built for barangay health workers.
    </p>
  </div>

  <div class="stats">
    <div class="stat">
      <div class="stat-label">Patients served</div>
    </div>
    <div class="stat">
      <div class="stat-label">Record accuracy</div>
    </div>
    <div class="stat">
      <div class="stat-label">Daily consultations</div>
    </div>
    <div class="stat">
      <div class="stat-label">System uptime</div>
    </div>
  </div>

  <div class="hero-bottom">
    <div class="pill-tags">
      <div class="pill"><span class="pill-dot"></span>Patient Records</div>
      <div class="pill"><span class="pill-dot"></span>Appointments</div>
      <div class="pill"><span class="pill-dot"></span>Vitals Monitoring</div>
      <div class="pill"><span class="pill-dot"></span>Health Analytics</div>
    </div>
  </div>
</div>

<!-- ── Right: Form Panel ── -->
<div class="form-side">
  <div class="form-wrap">

    <div class="form-eyebrow">
      <div class="eyebrow-line"></div>
      <span class="eyebrow-text">Staff Portal</span>
    </div>

    <h2 class="form-title">Welcome back</h2>
    <p class="form-subtitle">Sign in to access the health center system</p>

    <?php if ($error === 'wrong_password'): ?>
    <div class="alert">
      <span class="alert-icon">⚠️</span>
      <span class="alert-text">Incorrect password. Please try again.</span>
    </div>
    <?php endif; ?>

    <form method="POST" autocomplete="on">

      <div class="field">
        <label class="field-label">Username</label>
        <div class="input-group">
          <span class="input-icon">👤</span>
          <input
            type="text"
            name="username"
            placeholder="Enter your username"
            autocomplete="username"
            required
            autofocus
          >
        </div>
      </div>

      <div class="field">
        <label class="field-label">Password</label>
        <div class="input-group">
          <span class="input-icon">🔒</span>
          <input
            type="password"
            name="password"
            placeholder="Enter your password"
            autocomplete="current-password"
            required
          >
        </div>
      </div>

      <div class="options-row">
        <label class="remember">
          <input type="checkbox"> Remember me
        </label>
      </div>

      <button type="submit" class="btn-submit">
        Sign in
        <span class="btn-arrow">→</span>
      </button>

    </form>

    <div class="divider">or</div>

    <div class="register-cta">
      <span class="register-cta-text">New to the system? <strong>Join the team</strong></span>
      <a href="register.php" class="register-link">Register →</a>
    </div>

    <p class="form-footer">&copy; <?= date('Y') ?> Barangay Health Center Management System</p>
  </div>
</div>

<!-- ── Not-Registered Modal ── -->
<?php if ($error === 'not_registered'): ?>
<div class="modal-backdrop" id="notRegModal">
  <div class="modal-box">
    <div class="modal-icon-wrap">
      <div class="modal-icon">🔍</div>
    </div>
    <h3 class="modal-title">Account not found</h3>
    <p class="modal-body">
      We couldn't find an account for
      <strong>"<?= htmlspecialchars($_POST['username'] ?? '') ?>"</strong>.
      You may not be registered yet.
    </p>
    <a href="register.php" class="modal-btn-primary">Create an account →</a>
    <button class="modal-btn-ghost" onclick="document.getElementById('notRegModal').classList.add('hidden')">
      Try a different username
    </button>
  </div>
</div>
<?php endif; ?>

<style>
/* ── Modal ── */
.modal-backdrop {
  position: fixed; inset: 0; z-index: 999;
  background: rgba(15,30,20,.55);
  backdrop-filter: blur(4px);
  display: flex; align-items: center; justify-content: center;
  padding: 1.5rem;
  animation: backdropIn .22s ease both;
}
.modal-backdrop.hidden { display: none; }

@keyframes backdropIn {
  from { opacity: 0; }
  to   { opacity: 1; }
}

.modal-box {
  background: var(--white);
  border-radius: 20px;
  padding: 2.5rem 2.25rem 2rem;
  max-width: 380px; width: 100%;
  text-align: center;
  position: relative;
  animation: modalUp .28s cubic-bezier(.34,1.56,.64,1) both;
  border: 1px solid var(--cream-d);
}

@keyframes modalUp {
  from { opacity: 0; transform: translateY(24px) scale(.96); }
  to   { opacity: 1; transform: none; }
}

.modal-icon-wrap {
  width: 72px; height: 72px;
  background: var(--mint-l);
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  margin: 0 auto 1.5rem;
  border: 4px solid var(--mint);
}

.modal-icon { font-size: 2rem; line-height: 1; }

.modal-title {
  font-family: 'Fraunces', serif;
  font-size: 1.45rem;
  font-weight: 500;
  letter-spacing: -.02em;
  color: var(--forest);
  margin-bottom: .6rem;
}

.modal-body {
  font-size: .88rem;
  color: var(--muted);
  line-height: 1.65;
  margin-bottom: 1.75rem;
}

.modal-body strong {
  color: var(--text);
  font-weight: 600;
}

.modal-btn-primary {
  display: flex; align-items: center; justify-content: center;
  width: 100%;
  padding: .85rem 1.25rem;
  background: var(--forest);
  color: var(--white);
  border: none;
  border-radius: 11px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: .92rem;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: background .2s, transform .15s, box-shadow .2s;
  margin-bottom: .75rem;
}

.modal-btn-primary:hover {
  background: var(--forest-m);
  box-shadow: 0 6px 20px rgba(26,61,43,.2);
  transform: translateY(-1px);
}

.modal-btn-ghost {
  display: block; width: 100%;
  padding: .75rem 1.25rem;
  background: transparent;
  color: var(--muted);
  border: 1.5px solid var(--cream-d);
  border-radius: 11px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: .88rem;
  font-weight: 500;
  cursor: pointer;
  transition: background .18s, border-color .18s, color .18s;
}

.modal-btn-ghost:hover {
  background: var(--cream-d);
  border-color: var(--cream-d);
  color: var(--text);
}

/* close on backdrop click */
.modal-backdrop { cursor: default; }
.modal-box { cursor: auto; }
</style>

<script>
// Close modal when clicking the backdrop (outside the box)
const modal = document.getElementById('notRegModal');
if (modal) {
  modal.addEventListener('click', function(e) {
    if (e.target === modal) modal.classList.add('hidden');
  });
  // ESC key also closes
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') modal.classList.add('hidden');
  });
}
</script>

</body>
</html>