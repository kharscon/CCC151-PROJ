<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Barangay Health Center — Client Management System</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,200;0,9..144,400;0,9..144,600;1,9..144,200;1,9..144,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --forest:   #1a3d2b;
  --forest-m: #22503a;
  --forest-l: #2d6b4e;
  --sage:     #4a7c5e;
  --sage-l:   #85b89a;
  --mint:     #c8e6d4;
  --mint-l:   #e8f5ed;
  --cream:    #faf7f2;
  --cream-d:  #ede8df;
  --warm:     #7c6a52;
  --gold:     #c9943a;
  --gold-l:   #e8c17a;
  --white:    #ffffff;
  --text:     #1a1a1a;
  --muted:    #6b6b6b;
}

html { scroll-behavior: smooth; }

body {
  font-family: 'Plus Jakarta Sans', sans-serif;
  background: var(--cream);
  color: var(--text);
  overflow-x: hidden;
}

/* ── NAV ── */
nav {
  position: fixed; top: 0; left: 0; right: 0; z-index: 100;
  display: flex; align-items: center; justify-content: space-between;
  padding: 1.25rem 4rem;
  background: rgba(250,247,242,.92);
  backdrop-filter: blur(12px);
  border-bottom: 1px solid rgba(200,230,212,.3);
  animation: fadeDown .5s ease both;
}

.nav-brand {
  display: flex; align-items: center; gap: .75rem;
}

.nav-logo {
  width: 38px; height: 38px;
  background: var(--forest);
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.1rem;
}

.nav-name {
  font-family: 'Fraunces', serif;
  font-size: .95rem;
  font-weight: 600;
  color: var(--forest);
  letter-spacing: -.01em;
  line-height: 1.2;
}

.nav-name span {
  display: block;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: .7rem;
  font-weight: 500;
  color: var(--sage);
  letter-spacing: .08em;
  text-transform: uppercase;
}

.nav-links {
  display: flex; align-items: center; gap: 2rem;
  list-style: none;
}

.nav-links a {
  font-size: .87rem;
  font-weight: 500;
  color: var(--warm);
  text-decoration: none;
  transition: color .2s;
}
.nav-links a:hover { color: var(--forest); }

.nav-cta {
  display: flex; gap: .75rem;
}

.btn-outline {
  padding: .55rem 1.25rem;
  border: 1.5px solid var(--forest);
  border-radius: 9px;
  background: transparent;
  color: var(--forest);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: .87rem;
  font-weight: 600;
  text-decoration: none;
  transition: all .2s;
}
.btn-outline:hover { background: var(--forest); color: var(--white); }

.btn-solid {
  padding: .55rem 1.25rem;
  border: none;
  border-radius: 9px;
  background: var(--forest);
  color: var(--white);
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: .87rem;
  font-weight: 600;
  text-decoration: none;
  transition: all .2s;
}
.btn-solid:hover { background: var(--forest-m); box-shadow: 0 4px 14px rgba(26,61,43,.25); transform: translateY(-1px); }

/* ── HERO ── */
.hero {
  min-height: 100vh;
  display: grid;
  grid-template-columns: 1fr 1fr;
  padding-top: 5rem;
  position: relative;
  overflow: hidden;
}

.hero-bg {
  position: absolute; inset: 0; z-index: 0;
  background:
    radial-gradient(ellipse 60% 60% at 80% 50%, rgba(200,230,212,.25) 0%, transparent 60%),
    radial-gradient(ellipse 40% 40% at 10% 80%, rgba(201,148,58,.07) 0%, transparent 60%);
}

/* Dot grid texture */
.hero-bg::after {
  content: '';
  position: absolute; inset: 0;
  background-image: radial-gradient(circle, rgba(26,61,43,.06) 1px, transparent 1px);
  background-size: 28px 28px;
}

.hero-left {
  position: relative; z-index: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 5rem 3.5rem 5rem 6rem;
}

.hero-badge {
  display: inline-flex; align-items: center; gap: .5rem;
  padding: .4rem 1rem;
  background: rgba(26,61,43,.07);
  border: 1px solid rgba(26,61,43,.12);
  border-radius: 100px;
  font-size: .75rem;
  font-weight: 600;
  color: var(--forest-l);
  letter-spacing: .06em;
  text-transform: uppercase;
  margin-bottom: 2rem;
  width: fit-content;
  animation: fadeUp .6s .1s ease both;
}

.badge-dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: var(--gold);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: .5; transform: scale(.7); }
}

.hero-title {
  font-family: 'Fraunces', serif;
  font-size: clamp(2.8rem, 5vw, 4.2rem);
  font-weight: 200;
  letter-spacing: -.04em;
  line-height: 1.08;
  color: var(--forest);
  margin-bottom: 1.5rem;
  animation: fadeUp .6s .2s ease both;
}

.hero-title em {
  font-style: italic;
  color: var(--gold);
}

.hero-title strong {
  font-weight: 600;
  color: var(--forest);
}

.hero-desc {
  font-size: 1.05rem;
  color: var(--muted);
  line-height: 1.75;
  max-width: 420px;
  margin-bottom: 2.75rem;
  font-weight: 300;
  animation: fadeUp .6s .3s ease both;
}

.hero-actions {
  display: flex; align-items: center; gap: 1rem;
  animation: fadeUp .6s .4s ease both;
}

.btn-hero-primary {
  display: inline-flex; align-items: center; gap: .6rem;
  padding: .85rem 2rem;
  background: var(--forest);
  color: var(--white);
  border-radius: 12px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: .97rem;
  font-weight: 600;
  text-decoration: none;
  transition: all .2s;
  box-shadow: 0 4px 20px rgba(26,61,43,.2);
}
.btn-hero-primary:hover { background: var(--forest-m); transform: translateY(-2px); box-shadow: 0 8px 28px rgba(26,61,43,.28); }

.btn-hero-secondary {
  display: inline-flex; align-items: center; gap: .5rem;
  padding: .85rem 1.75rem;
  background: transparent;
  color: var(--forest);
  border: 1.5px solid var(--cream-d);
  border-radius: 12px;
  font-family: 'Plus Jakarta Sans', sans-serif;
  font-size: .97rem;
  font-weight: 600;
  text-decoration: none;
  transition: all .2s;
}
.btn-hero-secondary:hover { background: var(--white); border-color: var(--mint); }

.arrow-icon {
  width: 22px; height: 22px;
  background: rgba(255,255,255,.15);
  border-radius: 6px;
  display: flex; align-items: center; justify-content: center;
  font-size: .75rem;
  transition: transform .2s;
}
.btn-hero-primary:hover .arrow-icon { transform: translateX(3px); }

.hero-trust {
  margin-top: 3rem;
  display: flex; align-items: center; gap: 1rem;
  animation: fadeUp .6s .5s ease both;
}

.trust-avatars {
  display: flex;
}

.trust-av {
  width: 34px; height: 34px;
  border-radius: 50%;
  border: 2px solid var(--cream);
  background: var(--mint);
  display: flex; align-items: center; justify-content: center;
  font-size: .8rem;
  margin-left: -8px;
  font-weight: 600;
  color: var(--forest);
}
.trust-av:first-child { margin-left: 0; }

.trust-text {
  font-size: .82rem;
  color: var(--muted);
  line-height: 1.5;
}
.trust-text strong { color: var(--forest); font-weight: 600; }

/* Hero right — visual panel */
.hero-right {
  position: relative; z-index: 1;
  display: flex; align-items: center; justify-content: center;
  padding: 5rem 4rem 5rem 2rem;
  animation: fadeUp .7s .3s ease both;
}

.dashboard-preview {
  width: 100%;
  max-width: 480px;
  background: var(--white);
  border-radius: 20px;
  box-shadow: 0 20px 60px rgba(26,61,43,.12), 0 4px 16px rgba(26,61,43,.06);
  overflow: hidden;
  border: 1px solid rgba(200,230,212,.4);
  transform: perspective(1000px) rotateY(-4deg) rotateX(2deg);
  transition: transform .4s ease;
}
.dashboard-preview:hover { transform: perspective(1000px) rotateY(-1deg) rotateX(1deg); }

.preview-topbar {
  background: var(--forest);
  padding: .85rem 1.25rem;
  display: flex; align-items: center; justify-content: space-between;
}

.preview-dots { display: flex; gap: .4rem; }
.preview-dot { width: 9px; height: 9px; border-radius: 50%; }
.preview-dot.r { background: #ff5f56; }
.preview-dot.y { background: #ffbd2e; }
.preview-dot.g { background: #27c93f; }

.preview-title {
  font-size: .75rem; font-weight: 600;
  color: rgba(255,255,255,.5); letter-spacing: .05em;
}

.preview-body { padding: 1.25rem; background: #f0faf4; }

.preview-greeting {
  font-family: 'Fraunces', serif;
  font-size: 1rem; font-weight: 400;
  color: var(--forest); margin-bottom: 1rem;
}

.preview-cards {
  display: grid; grid-template-columns: 1fr 1fr;
  gap: .6rem; margin-bottom: 1rem;
}

.preview-card {
  background: var(--white); border-radius: 10px;
  padding: .75rem; border: 1px solid var(--cream-d);
}

.preview-card-num {
  font-family: 'Fraunces', serif;
  font-size: 1.5rem; font-weight: 400;
  color: var(--forest); line-height: 1;
  margin-bottom: .2rem;
}

.preview-card-label {
  font-size: .68rem; color: var(--muted);
  letter-spacing: .03em;
}

.preview-table-head {
  display: flex; justify-content: space-between;
  margin-bottom: .6rem;
}
.preview-table-title {
  font-size: .78rem; font-weight: 700; color: var(--forest);
}
.preview-table-link {
  font-size: .7rem; color: var(--sage); text-decoration: none;
}

.preview-row {
  display: flex; align-items: center; justify-content: space-between;
  padding: .5rem .6rem;
  background: var(--white); border-radius: 8px;
  margin-bottom: .4rem; border: 1px solid var(--cream-d);
}

.preview-patient { font-size: .75rem; font-weight: 600; color: var(--forest); }
.preview-purpose { font-size: .68rem; color: var(--muted); }

.badge-done { background: #dcfce7; color: #15803d; font-size: .63rem; font-weight: 600; padding: .22rem .6rem; border-radius: 100px; }
.badge-pending { background: #fef9c3; color: #854d0e; font-size: .63rem; font-weight: 600; padding: .22rem .6rem; border-radius: 100px; }

/* ── STATS BAND ── */
.stats-band {
  background: var(--forest);
  padding: 3rem 6rem;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1px;
  position: relative;
  overflow: hidden;
}

.stats-band::before {
  content: '';
  position: absolute; inset: 0;
  background-image: radial-gradient(circle, rgba(255,255,255,.03) 1px, transparent 1px);
  background-size: 32px 32px;
}

.stat-item {
  text-align: center; padding: 1.5rem;
  position: relative; z-index: 1;
}

.stat-item + .stat-item::before {
  content: '';
  position: absolute; left: 0; top: 20%; bottom: 20%;
  width: 1px; background: rgba(255,255,255,.08);
}

.stat-num {
  font-family: 'Fraunces', serif;
  font-size: 2.4rem; font-weight: 200;
  color: var(--white); line-height: 1;
  margin-bottom: .4rem;
  letter-spacing: -.04em;
}
.stat-num em { font-style: normal; color: var(--gold-l); }

.stat-label {
  font-size: .78rem; color: rgba(255,255,255,.45);
  letter-spacing: .08em; text-transform: uppercase;
}

/* ── FEATURES ── */
.features {
  padding: 7rem 6rem;
  position: relative;
}

.section-label {
  display: inline-flex; align-items: center; gap: .5rem;
  font-size: .75rem; font-weight: 600;
  letter-spacing: .1em; text-transform: uppercase;
  color: var(--sage); margin-bottom: 1.25rem;
}
.section-label::before {
  content: '';
  width: 20px; height: 2px; background: var(--gold); border-radius: 2px;
}

.section-title {
  font-family: 'Fraunces', serif;
  font-size: clamp(2rem, 3.5vw, 3rem);
  font-weight: 200; letter-spacing: -.04em;
  color: var(--forest); margin-bottom: 1rem;
  max-width: 540px;
}
.section-title em { font-style: italic; color: var(--gold); }

.section-sub {
  font-size: .95rem; color: var(--muted);
  line-height: 1.75; max-width: 480px;
  margin-bottom: 3.5rem;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

.feat-card {
  background: var(--white);
  border: 1px solid var(--cream-d);
  border-radius: 18px;
  padding: 2rem;
  transition: all .25s;
  position: relative;
  overflow: hidden;
}

.feat-card::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 3px;
  background: linear-gradient(90deg, var(--forest), var(--sage));
  opacity: 0;
  transition: opacity .25s;
}

.feat-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(26,61,43,.08);
  border-color: var(--mint);
}
.feat-card:hover::before { opacity: 1; }

.feat-icon {
  width: 48px; height: 48px;
  background: var(--mint-l);
  border-radius: 13px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.4rem;
  margin-bottom: 1.25rem;
  border: 1px solid var(--mint);
}

.feat-title {
  font-family: 'Fraunces', serif;
  font-size: 1.1rem; font-weight: 400;
  color: var(--forest); margin-bottom: .6rem;
  letter-spacing: -.02em;
}

.feat-desc {
  font-size: .87rem; color: var(--muted);
  line-height: 1.7;
}

/* ── HOW IT WORKS ── */
.how {
  background: var(--forest);
  padding: 7rem 6rem;
  position: relative;
  overflow: hidden;
}

.how::before {
  content: '';
  position: absolute; inset: 0;
  background-image: radial-gradient(circle, rgba(255,255,255,.02) 1px, transparent 1px);
  background-size: 36px 36px;
}

.how .section-label { color: var(--sage-l); }
.how .section-title { color: var(--white); max-width: 500px; }
.how .section-sub { color: rgba(255,255,255,.5); }

.steps {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
  position: relative; z-index: 1;
}

.step {
  position: relative;
}

.step-num {
  font-family: 'Fraunces', serif;
  font-size: 3.5rem; font-weight: 200;
  color: rgba(255,255,255,.07);
  line-height: 1; margin-bottom: .5rem;
  letter-spacing: -.04em;
}

.step-icon {
  width: 44px; height: 44px;
  background: rgba(200,230,212,.1);
  border: 1px solid rgba(200,230,212,.15);
  border-radius: 12px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.2rem;
  margin-bottom: 1.1rem;
}

.step-title {
  font-family: 'Fraunces', serif;
  font-size: 1.15rem; font-weight: 400;
  color: var(--white); margin-bottom: .5rem;
  letter-spacing: -.02em;
}

.step-desc {
  font-size: .87rem;
  color: rgba(255,255,255,.45);
  line-height: 1.7;
}

.step-connector {
  position: absolute; top: 3.5rem; right: -1rem;
  width: calc(100% - 44px);
  height: 1px;
  background: rgba(255,255,255,.07);
  z-index: 0;
}
.step:last-child .step-connector { display: none; }

/* ── CTA ── */
.cta-section {
  padding: 8rem 6rem;
  text-align: center;
  position: relative;
  overflow: hidden;
}

.cta-bg {
  position: absolute; inset: 0;
  background:
    radial-gradient(ellipse 60% 70% at 50% 100%, rgba(200,230,212,.3) 0%, transparent 60%);
}

.cta-section .section-label { justify-content: center; }
.cta-section .section-title { text-align: center; max-width: 600px; margin: 0 auto 1rem; }
.cta-section .section-sub { text-align: center; margin: 0 auto 3rem; }

.cta-buttons {
  display: flex; align-items: center; justify-content: center; gap: 1rem;
  position: relative; z-index: 1;
}

/* ── FOOTER ── */
footer {
  background: var(--forest);
  padding: 2rem 6rem;
  display: flex; align-items: center; justify-content: space-between;
}

.footer-brand {
  display: flex; align-items: center; gap: .65rem;
}

.footer-logo {
  width: 32px; height: 32px;
  background: rgba(255,255,255,.1);
  border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  font-size: .9rem;
}

.footer-name {
  font-family: 'Fraunces', serif;
  font-size: .85rem; font-weight: 400;
  color: rgba(255,255,255,.7);
}

.footer-copy {
  font-size: .78rem;
  color: rgba(255,255,255,.3);
}

.footer-links {
  display: flex; gap: 1.5rem;
}
.footer-links a {
  font-size: .82rem; color: rgba(255,255,255,.4);
  text-decoration: none; transition: color .2s;
}
.footer-links a:hover { color: rgba(255,255,255,.8); }

/* ── ANIMATIONS ── */
@keyframes fadeDown {
  from { opacity: 0; transform: translateY(-12px); }
  to   { opacity: 1; transform: none; }
}

@keyframes fadeUp {
  from { opacity: 0; transform: translateY(20px); }
  to   { opacity: 1; transform: none; }
}

.reveal {
  opacity: 0; transform: translateY(24px);
  transition: opacity .6s ease, transform .6s ease;
}
.reveal.visible {
  opacity: 1; transform: none;
}

/* ── RESPONSIVE ── */
@media (max-width: 1024px) {
  nav { padding: 1.25rem 2rem; }
  .nav-links { display: none; }
  .hero { grid-template-columns: 1fr; }
  .hero-left { padding: 4rem 2rem 2rem; }
  .hero-right { display: none; }
  .stats-band { padding: 2.5rem 2rem; grid-template-columns: 1fr 1fr; }
  .features { padding: 4rem 2rem; }
  .features-grid { grid-template-columns: 1fr 1fr; }
  .how { padding: 4rem 2rem; }
  .steps { grid-template-columns: 1fr; }
  .cta-section { padding: 5rem 2rem; }
  footer { padding: 2rem; flex-direction: column; gap: 1rem; text-align: center; }
  .footer-links { flex-wrap: wrap; justify-content: center; }
}
</style>
</head>
<body>

<!-- NAV -->
<nav>
  <div class="nav-brand">
    <div class="nav-logo">🏥</div>
    <div class="nav-name">
      Barangay Health Center
      <span>Client Management System</span>
    </div>
  </div>
  <ul class="nav-links">
    <li><a href="#features">Features</a></li>
    <li><a href="#how">How It Works</a></li>
    <li><a href="#about">About</a></li>
  </ul>
  <div class="nav-cta">
    <a href="/health_monitoring/barangay_health/login.php" class="btn-outline">Sign In</a>
    <a href="/health_monitoring/barangay_health/register.php" class="btn-solid">Register →</a>
  </div>
</nav>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg"></div>

  <div class="hero-left">
    <div class="hero-badge">
      <span class="badge-dot"></span>
      Now live in your barangay
    </div>

    <h1 class="hero-title">
      <em>Healthier</em> communities<br>start with <strong>better</strong><br>records.
    </h1>

    <p class="hero-desc">
      A unified digital system for managing client health records, scheduling appointments, and monitoring daily vitals. Built specifically for barangay health workers.
    </p>

    <div class="hero-actions">
      <a href="/health_monitoring/barangay_health/register.php" class="btn-hero-primary">
        Get Started Free
        <span class="arrow-icon">→</span>
      </a>
      <a href="/health_monitoring/barangay_health/login.php" class="btn-hero-secondary">
        Sign In
      </a>
    </div>

    <div class="hero-trust">
      <div class="trust-avatars">
        <div class="trust-av">M</div>
        <div class="trust-av">J</div>
        <div class="trust-av">R</div>
        <div class="trust-av">A</div>
      </div>
      <div class="trust-text">
        Trusted by <strong>health workers</strong> across the barangay<br>
        <span>Nurses · Midwives · BHWs · Doctors</span>
      </div>
    </div>
  </div>

  <div class="hero-right">
    <div class="dashboard-preview">
      <div class="preview-topbar">
        <div class="preview-dots">
          <div class="preview-dot r"></div>
          <div class="preview-dot y"></div>
          <div class="preview-dot g"></div>
        </div>
        <div class="preview-title">Dashboard — Barangay Health</div>
        <div></div>
      </div>
      <div class="preview-body">
        <div class="preview-greeting">Good Morning, Khara 👋</div>
        <div class="preview-cards">
          <div class="preview-card">
            <div class="preview-card-num">128</div>
            <div class="preview-card-label">Total Patients</div>
          </div>
          <div class="preview-card">
            <div class="preview-card-num">6</div>
            <div class="preview-card-label">Today's Appointments</div>
          </div>
          <div class="preview-card">
            <div class="preview-card-num">34</div>
            <div class="preview-card-label">Senior Citizens</div>
          </div>
          <div class="preview-card">
            <div class="preview-card-num">12</div>
            <div class="preview-card-label">Chronic Illness</div>
          </div>
        </div>
        <div class="preview-table-head">
          <span class="preview-table-title">📋 Today's Appointments</span>
          <a href="#" class="preview-table-link">View all →</a>
        </div>
        <div class="preview-row">
          <div>
            <div class="preview-patient">Maria Santos</div>
            <div class="preview-purpose">General Check-up</div>
          </div>
          <span class="badge-done">Completed</span>
        </div>
        <div class="preview-row">
          <div>
            <div class="preview-patient">Rosa Garcia</div>
            <div class="preview-purpose">Prenatal Check-up</div>
          </div>
          <span class="badge-pending">Pending</span>
        </div>
        <div class="preview-row">
          <div>
            <div class="preview-patient">Pedro Reyes</div>
            <div class="preview-purpose">Vaccination</div>
          </div>
          <span class="badge-pending">Pending</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- STATS BAND -->
<div class="stats-band reveal">
  <div class="stat-item">
    <div class="stat-num">500<em>+</em></div>
    <div class="stat-label">Patients Served</div>
  </div>
  <div class="stat-item">
    <div class="stat-num">99<em>%</em></div>
    <div class="stat-label">Record Accuracy</div>
  </div>
  <div class="stat-item">
    <div class="stat-num">50<em>+</em></div>
    <div class="stat-label">Daily Consultations</div>
  </div>
  <div class="stat-item">
    <div class="stat-num">24<em>/7</em></div>
    <div class="stat-label">System Uptime</div>
  </div>
</div>

<!-- FEATURES -->
<section class="features" id="features">
  <div class="section-label">Core Features</div>
  <h2 class="section-title reveal">Everything your health center <em>needs</em></h2>
  <p class="section-sub reveal">A complete suite of tools designed for the daily realities of barangay health work.</p>

  <div class="features-grid">
    <div class="feat-card reveal">
      <div class="feat-icon">👤</div>
      <div class="feat-title">Patient Records</div>
      <div class="feat-desc">Store complete patient profiles with medical history, vitals, diagnoses, and treatment notes — all in one place.</div>
    </div>
    <div class="feat-card reveal">
      <div class="feat-icon">📅</div>
      <div class="feat-title">Appointment Scheduling</div>
      <div class="feat-desc">Schedule and manage patient appointments easily. Track statuses from pending to completed with real-time updates.</div>
    </div>
    <div class="feat-card reveal">
      <div class="feat-icon">📊</div>
      <div class="feat-title">Health Analytics</div>
      <div class="feat-desc">Get insights into patient demographics, chronic illness trends, and visit frequency to guide community health decisions.</div>
    </div>
    <div class="feat-card reveal">
      <div class="feat-icon">💊</div>
      <div class="feat-title">Chronic Illness Monitoring</div>
      <div class="feat-desc">Track patients with ongoing conditions like hypertension and diabetes, with alerts for overdue checkups.</div>
    </div>
    <div class="feat-card reveal">
      <div class="feat-icon">👴</div>
      <div class="feat-title">Senior Citizen Program</div>
      <div class="feat-desc">Dedicated module for senior citizen records, benefits tracking, and special health program management.</div>
    </div>
    <div class="feat-card reveal">
      <div class="feat-icon">🔐</div>
      <div class="feat-title">Secure Staff Access</div>
      <div class="feat-desc">Role-based login for doctors, nurses, midwives, and BHWs — keeping data safe and access controlled.</div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="how" id="how">
  <div class="section-label">How It Works</div>
  <h2 class="section-title reveal">Up and running in <em>minutes</em></h2>
  <p class="section-sub reveal">Simple onboarding so your team can focus on patient care, not software setup.</p>

  <div class="steps">
    <div class="step reveal">
      <div class="step-num">01</div>
      <div class="step-icon">✏️</div>
      <div class="step-title">Register Your Account</div>
      <div class="step-desc">Health center staff create accounts with their name, position, and credentials. Takes under a minute.</div>
      <div class="step-connector"></div>
    </div>
    <div class="step reveal">
      <div class="step-num">02</div>
      <div class="step-icon">🔐</div>
      <div class="step-title">Sign In Securely</div>
      <div class="step-desc">Log in with your username and password. Your session is protected and tied to your staff role.</div>
      <div class="step-connector"></div>
    </div>
    <div class="step reveal">
      <div class="step-num">03</div>
      <div class="step-icon">🏥</div>
      <div class="step-title">Manage & Monitor</div>
      <div class="step-desc">Add patients, schedule appointments, record vitals, and track health programs — all from your dashboard.</div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-section" id="about">
  <div class="cta-bg"></div>
  <div class="section-label">Get Started Today</div>
  <h2 class="section-title reveal">Ready to bring your health center <em>online?</em></h2>
  <p class="section-sub reveal">Join the team and start managing patient health records with clarity, care, and confidence.</p>
  <div class="cta-buttons reveal">
    <a href="/health_monitoring/barangay_health/register.php" class="btn-hero-primary">
      Create Staff Account
      <span class="arrow-icon">→</span>
    </a>
    <a href="/health_monitoring/barangay_health/login.php" class="btn-hero-secondary">Already registered? Sign In</a>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="footer-brand">
    <div class="footer-logo">🏥</div>
    <span class="footer-name">Barangay Health Center Management System</span>
  </div>
  <div class="footer-copy">&copy; 2026 All rights reserved</div>
  <div class="footer-links">
    <a href="/health_monitoring/barangay_health/login.php">Staff Login</a>
    <a href="/health_monitoring/barangay_health/register.php">Register</a>
  </div>
</footer>

<script>
// Scroll reveal
const reveals = document.querySelectorAll('.reveal');
const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry, i) => {
    if (entry.isIntersecting) {
      setTimeout(() => entry.target.classList.add('visible'), i * 80);
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.1 });

reveals.forEach(el => observer.observe(el));
</script>

</body>
</html>
