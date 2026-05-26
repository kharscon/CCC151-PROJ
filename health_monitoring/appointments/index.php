<?php
$pageTitle = "Appointments - Barangay Health Center";
require_once('../includes/header.php');

$conn = getConnection();

$date_filter   = isset($_GET['date'])   ? sanitize($conn, $_GET['date'])   : date('Y-m-d');
$status_filter = isset($_GET['status']) ? sanitize($conn, $_GET['status']) : '';
$search        = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';

$where = "WHERE 1=1";
if ($date_filter)   $where .= " AND a.appointment_date = '$date_filter'";
if ($status_filter) $where .= " AND a.status = '$status_filter'";
if ($search)        $where .= " AND (p.first_name LIKE '%$search%' OR p.last_name LIKE '%$search%')";

$appointments = $conn->query("
    SELECT a.*, p.first_name, p.last_name, p.contact_number
    FROM appointments a
    JOIN patients p ON a.patient_id = p.patient_id
    $where
    ORDER BY a.appointment_time ASC
");

$total_appts     = $conn->query("SELECT COUNT(*) as c FROM appointments")->fetch_assoc()['c'];
$today_scheduled = $conn->query("SELECT COUNT(*) as c FROM appointments WHERE appointment_date = '" . date('Y-m-d') . "' AND status = 'Scheduled'")->fetch_assoc()['c'];
$total_completed = $conn->query("SELECT COUNT(*) as c FROM appointments WHERE status = 'Completed'")->fetch_assoc()['c'];
$total_cancelled = $conn->query("SELECT COUNT(*) as c FROM appointments WHERE status = 'Cancelled'")->fetch_assoc()['c'];

$appt_count = $appointments ? $appointments->num_rows : 0;
$staffName  = htmlspecialchars($_SESSION['staff_name'] ?? 'Staff');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Appointments — Barangay Health Center</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root {
    --forest-deepest: #0a1f0e;
    --forest-dark:    #122d18;
    --forest-mid:     #1a4726;
    --forest-rich:    #1e5c30;
    --forest-bright:  #2d7d46;
    --forest-leaf:    #3a9c58;
    --forest-light:   #4db870;
    --moss:           #6abf80;

    --red-deep:       #7f1d1d;
    --red-mid:        #991b1b;
    --red-rich:       #b91c1c;
    --red-bright:     #dc2626;
    --red-light:      #ef4444;
    --red-pale:       #fef2f2;
    --red-mist:       #fee2e2;

    --blue-deepest:   #061528;
    --blue-dark:      #0d2744;
    --blue-mid:       #1a4a7a;
    --blue-rich:      #1e5c9c;

    --amber:          #d97706;
    --amber-light:    #fef3c7;
    --text-dark:      #0d1828;
    --text-mid:       #2e3f5a;
    --text-soft:      #6b82a0;
    --white:          #ffffff;
    --card-bg:        rgba(255,255,255,0.93);
    --sidebar-w:      260px;
    --fog:            #edf7ef;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'DM Sans', sans-serif;
    background: #eef2f7;
    color: var(--text-dark);
    min-height: 100vh;
    overflow-x: hidden;
}

/* ══════════ SIDEBAR ══════════ */
.sidebar {
    width: var(--sidebar-w);
    background: var(--forest-deepest);
    position: fixed; top: 0; left: 0; bottom: 0;
    display: flex; flex-direction: column;
    overflow: hidden; z-index: 100;
}
.sidebar::before {
    content: ''; position: absolute; inset: 0;
    background:
        radial-gradient(ellipse at 20% 80%, rgba(45,125,70,.3) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(26,71,38,.4) 0%, transparent 50%);
    pointer-events: none;
}
.sidebar::after {
    content: ''; position: absolute;
    bottom: -40px; right: -60px;
    width: 220px; height: 220px;
    border-radius: 50% 0 50% 0;
    border: 1px solid rgba(77,184,112,.12);
    transform: rotate(30deg); pointer-events: none;
}
.sidebar-logo {
    padding: 28px 24px 24px;
    border-bottom: 1px solid rgba(77,184,112,.15);
    position: relative; z-index: 1;
}
.sidebar-logo .logo-icon { font-size: 28px; display: block; margin-bottom: 8px; }
.sidebar-logo h2 {
    font-family: 'Playfair Display', serif;
    font-size: 15px; color: var(--white); line-height: 1.3; font-weight: 500;
}
.sidebar-logo span {
    font-size: 11px; color: var(--moss); font-weight: 300;
    letter-spacing: 1.5px; text-transform: uppercase; display: block; margin-top: 3px;
}
.nav-section { padding: 20px 16px 8px; position: relative; z-index: 1; flex: 1; }
.nav-label {
    font-size: 10px; letter-spacing: 2px; text-transform: uppercase;
    color: rgba(168,213,181,.5); padding: 0 10px; margin-bottom: 8px;
}
.sidebar a {
    display: flex; align-items: center; gap: 12px;
    color: rgba(255,255,255,.75); text-decoration: none;
    padding: 11px 14px; border-radius: 10px; margin-bottom: 4px;
    font-size: 14px; font-weight: 400; transition: all .2s; position: relative;
}
.sidebar a:hover { background: rgba(77,184,112,.15); color: var(--white); }
.sidebar a.active {
    background: linear-gradient(135deg, rgba(58,156,88,.5), rgba(45,125,70,.3));
    color: var(--white); font-weight: 500;
    box-shadow: inset 0 0 0 1px rgba(77,184,112,.3);
}
.sidebar a.active::before {
    content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%);
    width: 3px; height: 20px; background: var(--forest-light); border-radius: 0 3px 3px 0;
}
.nav-icon { font-size: 17px; width: 20px; text-align: center; }
.sidebar-footer {
    padding: 16px 16px 24px;
    border-top: 1px solid rgba(77,184,112,.1);
    position: relative; z-index: 1;
}
.sidebar-footer a {
    display: flex; align-items: center; gap: 12px;
    color: rgba(255,255,255,.5); text-decoration: none;
    padding: 10px 14px; border-radius: 10px; font-size: 14px; transition: all .2s;
}
.sidebar-footer a:hover { color: #ff8080; background: rgba(255,100,100,.1); }

/* ══════════ MAIN ══════════ */
.main {
    margin-left: var(--sidebar-w);
    padding: 32px 36px;
    min-height: 100vh; position: relative;
}
.main::before {
    content: ''; position: fixed;
    top: 0; left: var(--sidebar-w); right: 0; bottom: 0;
    background:
        radial-gradient(ellipse at 85% 10%, rgba(220,38,38,.06) 0%, transparent 50%),
        radial-gradient(ellipse at 10% 90%, rgba(185,28,28,.04) 0%, transparent 50%);
    pointer-events: none; z-index: 0;
}
.main > * { position: relative; z-index: 1; }

/* ══════════ TOPBAR ══════════ */
.topbar {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 36px; animation: fadeUp .4s ease both;
}
.topbar-left { display: flex; align-items: center; gap: 16px; }
.page-icon {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, var(--red-bright), var(--red-mid));
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px; box-shadow: 0 4px 16px rgba(220,38,38,.35);
}
.topbar-left h1 {
    font-family: 'Playfair Display', serif;
    font-size: 28px; font-weight: 700;
    color: var(--blue-deepest); line-height: 1.2;
}
.topbar-left p { font-size: 14px; color: var(--text-soft); margin-top: 4px; font-weight: 300; }
.topbar-right { display: flex; align-items: center; gap: 14px; }
.date-badge {
    background: var(--card-bg); border: 1px solid rgba(220,38,38,.15);
    border-radius: 50px; padding: 8px 18px;
    font-size: 13px; color: var(--text-mid); font-weight: 500;
    box-shadow: 0 2px 8px rgba(0,0,0,.05);
}
.btn-add-top {
    display: inline-flex; align-items: center; gap: 8px;
    background: linear-gradient(135deg, var(--red-bright), var(--red-mid));
    color: white; text-decoration: none;
    padding: 10px 22px; border-radius: 50px;
    font-size: 13px; font-weight: 600;
    box-shadow: 0 4px 14px rgba(220,38,38,.4);
    transition: all .2s;
}
.btn-add-top:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(220,38,38,.5); }
.avatar {
    width: 42px; height: 42px;
    background: linear-gradient(135deg, var(--forest-leaf), var(--forest-mid));
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: white; font-weight: 600; font-size: 16px;
    box-shadow: 0 3px 12px rgba(45,125,70,.4);
}

/* ══════════ STAT CARDS ══════════ */
.cards {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 20px; margin-bottom: 32px;
}
.card {
    background: var(--card-bg); border-radius: 18px; padding: 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,.06); border: 1px solid rgba(255,255,255,.8);
    position: relative; overflow: hidden;
    transition: transform .2s ease, box-shadow .2s ease;
    animation: fadeUp .5s ease both;
}
.card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,.1); }
.card:nth-child(1){animation-delay:.05s;} .card:nth-child(2){animation-delay:.10s;}
.card:nth-child(3){animation-delay:.15s;} .card:nth-child(4){animation-delay:.20s;}
.card::after {
    content: ''; position: absolute; top: -30px; right: -30px;
    width: 100px; height: 100px; border-radius: 50%; opacity: .08;
}
.card-1::after { background: #3b82f6; }
.card-2::after { background: var(--amber); }
.card-3::after { background: #10b981; }
.card-4::after { background: var(--red-bright); }
.card-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; margin-bottom: 16px;
}
.card-1 .card-icon { background: rgba(59,130,246,.10); }
.card-2 .card-icon { background: rgba(245,158,11,.10); }
.card-3 .card-icon { background: rgba(16,185,129,.10); }
.card-4 .card-icon { background: rgba(220,38,38,.10); }
.card h3 {
    font-family: 'Playfair Display', serif;
    font-size: 38px; font-weight: 700; line-height: 1;
    color: var(--blue-deepest); margin-bottom: 6px;
}
.card p { font-size: 13px; color: var(--text-soft); font-weight: 400; }
.card-trend {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 12px; font-weight: 600; margin-top: 10px;
    padding: 3px 8px; border-radius: 20px;
}
.trend-blue   { background: rgba(59,130,246,.10);  color: #1d4ed8; }
.trend-amber  { background: rgba(245,158,11,.10);  color: #92400e; }
.trend-green  { background: rgba(16,185,129,.10);  color: #065f46; }
.trend-red    { background: rgba(220,38,38,.10);   color: var(--red-rich); }

/* ══════════ FILTER TOOLBAR ══════════ */
.toolbar {
    background: var(--card-bg); border-radius: 16px;
    padding: 16px 20px;
    display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
    box-shadow: 0 3px 16px rgba(0,0,0,.06); border: 1px solid rgba(255,255,255,.85);
    margin-bottom: 22px; animation: fadeUp .45s ease .08s both;
}
.search-wrap {
    flex: 1; min-width: 220px;
    background: var(--red-pale); border: 1.5px solid rgba(220,38,38,.2);
    border-radius: 11px; display: flex; align-items: center; gap: 10px;
    padding: 0 14px; transition: border-color .2s, box-shadow .2s;
}
.search-wrap:focus-within {
    border-color: var(--red-bright);
    box-shadow: 0 0 0 3px rgba(220,38,38,.10); background: white;
}
.search-wrap span { color: var(--text-soft); font-size: 15px; }
.search-wrap input {
    border: none; outline: none; background: transparent;
    padding: 11px 0; font-size: 14px; color: var(--text-dark);
    width: 100%; font-family: 'DM Sans', sans-serif;
}
.search-wrap input::placeholder { color: #c0a0a0; }
.filter-select, .filter-date {
    background: var(--red-pale); border: 1.5px solid rgba(220,38,38,.2);
    border-radius: 11px; padding: 11px 14px;
    font-size: 13px; color: var(--text-mid);
    font-family: 'DM Sans', sans-serif; outline: none; cursor: pointer;
    transition: border-color .2s; min-width: 160px;
    appearance: none;
}
.filter-select {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236b82a0' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 12px center;
    padding-right: 32px;
}
.filter-select:focus, .filter-date:focus {
    border-color: var(--red-bright); background-color: white;
    box-shadow: 0 0 0 3px rgba(220,38,38,.10);
}
.btn-filter-go {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 20px; border-radius: 11px;
    font-size: 14px; font-weight: 600; cursor: pointer;
    border: none; font-family: 'DM Sans', sans-serif;
    background: linear-gradient(135deg, var(--red-bright), var(--red-mid));
    color: white; box-shadow: 0 3px 10px rgba(220,38,38,.3);
    transition: all .2s; text-decoration: none;
}
.btn-filter-go:hover { opacity: .9; }
.btn-ghost {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 11px 18px; border-radius: 11px;
    font-size: 13px; font-weight: 500; cursor: pointer;
    background: var(--card-bg); color: var(--text-soft);
    border: 1.5px solid rgba(220,38,38,.15);
    font-family: 'DM Sans', sans-serif; text-decoration: none; transition: all .2s;
}
.btn-ghost:hover { color: var(--text-mid); background: var(--red-mist); border-color: var(--red-bright); }
.results-count { margin-left: auto; font-size: 13px; color: var(--text-soft); white-space: nowrap; }
.results-count strong { color: var(--red-rich); font-weight: 600; }

/* ══════════ TABLE CARD ══════════ */
.table-card {
    background: var(--card-bg); border-radius: 18px; overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.06); border: 1px solid rgba(255,255,255,.8);
    animation: fadeUp .5s ease .15s both;
}
.table-header {
    padding: 18px 24px;
    background: linear-gradient(135deg, var(--red-mid), var(--red-rich));
    color: white; display: flex; align-items: center; justify-content: space-between;
}
.table-header-title {
    display: flex; align-items: center; gap: 10px;
    font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 500;
}
.th-icon {
    width: 32px; height: 32px; background: rgba(255,255,255,.15);
    border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 15px;
}
.add-btn-header {
    display: inline-flex; align-items: center; gap: 7px;
    background: rgba(255,255,255,.15); color: white;
    border: 1px solid rgba(255,255,255,.25); border-radius: 20px; padding: 6px 16px;
    font-size: 13px; font-weight: 600; text-decoration: none; transition: all .2s;
}
.add-btn-header:hover { background: rgba(255,255,255,.28); }
.table-responsive { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
table thead th {
    background: rgba(254,242,242,.7); padding: 11px 18px;
    font-size: 11px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase;
    color: var(--text-soft); text-align: left; white-space: nowrap;
}
table tbody td {
    padding: 14px 18px; border-bottom: 1px solid rgba(0,0,0,.04);
    font-size: 14px; color: var(--text-dark); vertical-align: middle;
}
table tbody tr:last-child td { border-bottom: none; }
table tbody tr { transition: background .15s; }
table tbody tr:hover { background: rgba(220,38,38,.03); }

/* ══════════ CELLS ══════════ */
.appt-id {
    display: inline-block; font-size: 11px; font-family: monospace;
    color: var(--text-soft); background: var(--red-pale); padding: 2px 8px; border-radius: 20px;
}
.patient-link { font-weight: 600; color: var(--blue-mid); text-decoration: none; font-size: 13px; }
.patient-link:hover { text-decoration: underline; }
.contact-cell { font-size: 13px; color: var(--text-soft); }
.purpose-cell { font-size: 13px; color: var(--text-mid); max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.time-cell    { font-weight: 600; color: var(--red-rich); font-size: 13px; }
.date-cell    { font-size: 13px; color: var(--text-mid); }

/* ══════════ STATUS PILLS ══════════ */
.pill {
    display: inline-block; padding: 5px 12px; border-radius: 20px;
    font-size: 11px; font-weight: 700; letter-spacing: .3px; white-space: nowrap;
}
.pill-scheduled { background: #fef3c7; color: #92400e; }
.pill-completed { background: #d1fae5; color: #065f46; }
.pill-cancelled { background: #fee2e2; color: #991b1b; }
.pill-noshow    { background: #e0f2fe; color: #075985; }

/* ══════════ ACTION BTNS ══════════ */
.action-btns { display: flex; gap: 6px; flex-wrap: wrap; }
.btn-action {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 6px 12px; border-radius: 8px;
    font-size: 12px; font-weight: 500; font-family: 'DM Sans', sans-serif;
    text-decoration: none; transition: all .15s; white-space: nowrap;
    border: 1.5px solid transparent;
}
.btn-edit   { background: rgba(217,119,6,.1);  color: #92400e; border-color: rgba(217,119,6,.2); }
.btn-edit:hover   { background: rgba(217,119,6,.2); border-color: #d97706; }
.btn-delete { background: rgba(220,38,38,.08); color: #b91c1c; border-color: rgba(220,38,38,.15); }
.btn-delete:hover { background: rgba(220,38,38,.18); border-color: #dc2626; }

/* ══════════ EMPTY STATE ══════════ */
.empty-state { text-align: center; padding: 64px 20px; color: var(--text-soft); }
.empty-state .empty-icon { font-size: 52px; margin-bottom: 16px; }
.empty-state h3 { font-family: 'Playfair Display', serif; font-size: 20px; color: var(--text-mid); margin-bottom: 8px; }
.empty-state p { font-size: 14px; }

/* ══════════ ANIMATIONS ══════════ */
@keyframes fadeUp { from{opacity:0;transform:translateY(16px);} to{opacity:1;transform:translateY(0);} }

/* ══════════ RESPONSIVE ══════════ */
@media(max-width:1100px){ .cards{grid-template-columns:1fr 1fr;} }
@media(max-width:768px){
    :root{--sidebar-w:0px;} .sidebar{display:none;}
    .main{padding:20px 16px;}
    .topbar{flex-direction:column;align-items:flex-start;gap:14px;}
    .cards{grid-template-columns:1fr 1fr;gap:12px;}
    .toolbar{flex-direction:column;align-items:stretch;}
    .results-count{margin-left:0;}
}
@media(max-width:480px){ .cards{grid-template-columns:1fr;} }
</style>
</head>
<body>

<!-- ══════════ SIDEBAR ══════════ -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <span class="logo-icon">🌿</span>
        <h2>Barangay Health Center</h2>
        <span>Patient Management System</span>
    </div>
    <nav class="nav-section">
        <p class="nav-label">Main Menu</p>
        <a href="/health_monitoring/barangay_health/index.php"><span class="nav-icon">📊</span>Dashboard</a>
        <a href="/health_monitoring/patients/index.php"><span class="nav-icon">👥</span>Clients</a>
        <a href="/health_monitoring/appointments/index.php" class="active"><span class="nav-icon">📅</span>Appointments</a>
        <a href="/health_monitoring/staff/index.php"><span class="nav-icon">👤</span>Staff</a>
        <p class="nav-label" style="margin-top:20px;">System</p>
        <a href="/health_monitoring/settings/index.php"><span class="nav-icon">⚙️</span>Settings</a>
    </nav>
    <div class="sidebar-footer">
        <a href="/health_monitoring/barangay_health/logout.php"><span class="nav-icon">↩</span>Logout</a>
    </div>
</aside>

<!-- ══════════ MAIN ══════════ -->
<main class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="topbar-left">
            <div class="page-icon">📅</div>
            <div>
                <h1>Appointment Records</h1>
                <p>Browse, search, and manage all patient appointments</p>
            </div>
        </div>
        <div class="topbar-right">
            <div class="date-badge">🗓 <?= date('l, F j, Y') ?></div>
            <a href="add.php" class="btn-add-top">＋ Schedule Appointment</a>
            <div class="avatar"><?= strtoupper(substr($staffName, 0, 1)) ?></div>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="cards">
        <div class="card card-1">
            <div class="card-icon">📅</div>
            <h3><?= number_format($total_appts) ?></h3>
            <p>Total Appointments</p>
            <div class="card-trend trend-blue">— All time</div>
        </div>
        <div class="card card-2">
            <div class="card-icon">⏰</div>
            <h3><?= number_format($today_scheduled) ?></h3>
            <p>Scheduled Today</p>
            <div class="card-trend trend-amber">— Pending</div>
        </div>
        <div class="card card-3">
            <div class="card-icon">✅</div>
            <h3><?= number_format($total_completed) ?></h3>
            <p>Completed</p>
            <div class="card-trend trend-green">— Done</div>
        </div>
        <div class="card card-4">
            <div class="card-icon">❌</div>
            <h3><?= number_format($total_cancelled) ?></h3>
            <p>Cancelled</p>
            <div class="card-trend trend-red">— Cancelled</div>
        </div>
    </div>

    <!-- FILTER TOOLBAR -->
    <form method="GET" class="toolbar">
        <div class="search-wrap">
            <span>🔍</span>
            <input type="text" name="search"
                   placeholder="Search by patient name…"
                   value="<?= htmlspecialchars($search) ?>">
        </div>
        <input type="date" name="date" class="filter-date"
               value="<?= htmlspecialchars($date_filter) ?>">
        <select name="status" class="filter-select">
            <option value="">All Status</option>
            <?php foreach (['Scheduled','Completed','Cancelled','No Show'] as $s): ?>
                <option value="<?= $s ?>" <?= $status_filter === $s ? 'selected' : '' ?>><?= $s ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn-filter-go">🔍 Filter</button>
        <a href="index.php" class="btn-ghost">📆 Today</a>
        <span class="results-count"><strong><?= $appt_count ?></strong> appointment<?= $appt_count !== 1 ? 's' : '' ?> found</span>
    </form>

    <!-- TABLE CARD -->
    <div class="table-card">
        <div class="table-header">
            <div class="table-header-title">
                <div class="th-icon">📅</div>
                All Appointments
            </div>
            <a href="add.php" class="add-btn-header">＋ New Appointment</a>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Patient Name</th>
                        <th>Contact</th>
                        <th>Purpose</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($appointments && $appointments->num_rows > 0): ?>
                    <?php while ($row = $appointments->fetch_assoc()): ?>
                    <tr>
                        <td><span class="appt-id">#<?= $row['appointment_id'] ?></span></td>
                        <td class="date-cell"><?= date('M d, Y', strtotime($row['appointment_date'])) ?></td>
                        <td><span class="time-cell"><?= date('h:i A', strtotime($row['appointment_time'])) ?></span></td>
                        <td>
                            <a href="/health_monitoring/patients/view.php?id=<?= $row['patient_id'] ?>" class="patient-link">
                                <?= htmlspecialchars($row['last_name'] . ', ' . $row['first_name']) ?>
                            </a>
                        </td>
                        <td class="contact-cell"><?= htmlspecialchars($row['contact_number']) ?></td>
                        <td><div class="purpose-cell" title="<?= htmlspecialchars($row['purpose']) ?>"><?= htmlspecialchars($row['purpose']) ?></div></td>
                        <td>
                            <?php
                                $pillMap = [
                                    'Scheduled' => 'pill-scheduled',
                                    'Completed' => 'pill-completed',
                                    'Cancelled' => 'pill-cancelled',
                                    'No Show'   => 'pill-noshow',
                                ];
                                $pill = $pillMap[$row['status']] ?? 'pill-scheduled';
                            ?>
                            <span class="pill <?= $pill ?>"><?= htmlspecialchars($row['status']) ?></span>
                        </td>
                        <td>
                            <div class="action-btns">
                                <a href="edit.php?id=<?= $row['appointment_id'] ?>" class="btn-action btn-edit">✏️ Edit</a>
                                <a href="delete.php?id=<?= $row['appointment_id'] ?>"
                                   class="btn-action btn-delete"
                                   onclick="return confirm('Delete this appointment? This cannot be undone.')">🗑️ Delete</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-icon">📋</div>
                                <h3>No appointments found</h3>
                                <p><?= ($search || $status_filter) ? 'Try adjusting your filters.' : 'No appointments have been added yet.' ?></p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<?php
$conn->close();
require_once '../includes/footer.php';
?>
</body>
</html>