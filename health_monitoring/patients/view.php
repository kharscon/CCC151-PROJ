<?php
$pageTitle = "View Patient - Barangay Health Center";
require_once('../includes/header.php');
$conn = getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    setAlert('danger', 'Invalid patient ID.');
    header('Location: index.php');
    exit;
}

// Use prepared statement — safer than direct interpolation
$stmt = $conn->prepare("SELECT * FROM patients WHERE patient_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();

if (!$patient) {
    setAlert('danger', 'Patient not found.');
    header('Location: index.php');
    exit;
}

$dob = new DateTime($patient['date_of_birth']);
$now = new DateTime();
$age = $now->diff($dob)->y;

// Safe appointment query — wraps in error check
$appointments = false;
$aptResult = $conn->query(
    "SELECT * FROM appointments WHERE patient_id = $id ORDER BY appointment_date DESC LIMIT 5"
);
if ($aptResult) {
    $appointments = $aptResult;
}

// Safe health records query
$records = false;
$recResult = $conn->query(
    "SELECT * FROM health_records WHERE patient_id = $id ORDER BY record_date DESC, record_time DESC LIMIT 5"
);
if ($recResult) {
    $records = $recResult;
}

// Registered date — falls back gracefully if created_at doesn't exist
$registeredLabel = '—';
if (!empty($patient['created_at'])) {
    $registeredLabel = date('M Y', strtotime($patient['created_at']));
}

$catClass = match($patient['patient_category']) {
    'Child'  => 'badge-child',
    'Senior' => 'badge-senior',
    'PWD'    => 'badge-pwd',
    default  => 'badge-adult',
};
$catIcon = match($patient['patient_category']) {
    'Child'  => '🧒',
    'Senior' => '🧓',
    'PWD'    => '♿',
    default  => '🧑',
};
$initials = strtoupper(substr($patient['first_name'] ?? 'X', 0, 1) . substr($patient['last_name'] ?? 'X', 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Profile — Barangay Health Center</title>
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
    --sage:           #a8d5b5;
    --mist:           #d4ead9;
    --fog:            #edf7ef;
    --amber:          #d97706;
    --amber-light:    #fef3c7;
    --red-soft:       #fee2e2;
    --red-mid:        #dc2626;
    --text-dark:      #0d1f12;
    --text-mid:       #2e4a35;
    --text-soft:      #6b8f74;
    --white:          #ffffff;
    --card-bg:        rgba(255,255,255,0.94);
    --sidebar-w:      260px;
}

*{box-sizing:border-box;margin:0;padding:0;}

body{
    font-family:'DM Sans',sans-serif;
    background:var(--fog);
    color:var(--text-dark);
    min-height:100vh;
    overflow-x:hidden;
}

/* ======== SIDEBAR ======== */
.sidebar{
    width:var(--sidebar-w);
    background:var(--forest-deepest);
    position:fixed;top:0;left:0;bottom:0;
    display:flex;flex-direction:column;
    overflow:hidden;z-index:100;
}
.sidebar::before{
    content:'';position:absolute;inset:0;
    background:
        radial-gradient(ellipse at 20% 80%,rgba(45,125,70,.3) 0%,transparent 60%),
        radial-gradient(ellipse at 80% 20%,rgba(26,71,38,.4) 0%,transparent 50%);
    pointer-events:none;
}
.sidebar::after{
    content:'';position:absolute;
    bottom:-40px;right:-60px;
    width:220px;height:220px;
    border-radius:50% 0 50% 0;
    border:1px solid rgba(77,184,112,.12);
    transform:rotate(30deg);pointer-events:none;
}
.sidebar-logo{
    padding:28px 24px 24px;
    border-bottom:1px solid rgba(77,184,112,.15);
    position:relative;z-index:1;
}
.sidebar-logo .logo-icon{font-size:28px;display:block;margin-bottom:8px;}
.sidebar-logo h2{
    font-family:'Playfair Display',serif;
    font-size:15px;color:var(--white);line-height:1.3;font-weight:500;
}
.sidebar-logo span{
    font-size:11px;color:var(--moss);font-weight:300;
    letter-spacing:1.5px;text-transform:uppercase;display:block;margin-top:3px;
}
.nav-section{padding:20px 16px 8px;position:relative;z-index:1;flex:1;}
.nav-label{
    font-size:10px;letter-spacing:2px;text-transform:uppercase;
    color:rgba(168,213,181,.5);padding:0 10px;margin-bottom:8px;
}
.sidebar a{
    display:flex;align-items:center;gap:12px;
    color:rgba(255,255,255,.75);text-decoration:none;
    padding:11px 14px;border-radius:10px;margin-bottom:4px;
    font-size:14px;font-weight:400;transition:all .2s;position:relative;
}
.sidebar a:hover{background:rgba(77,184,112,.15);color:var(--white);}
.sidebar a.active{
    background:linear-gradient(135deg,rgba(58,156,88,.5),rgba(45,125,70,.3));
    color:var(--white);font-weight:500;
    box-shadow:inset 0 0 0 1px rgba(77,184,112,.3);
}
.sidebar a.active::before{
    content:'';position:absolute;left:0;top:50%;transform:translateY(-50%);
    width:3px;height:20px;background:var(--forest-light);border-radius:0 3px 3px 0;
}
.nav-icon{font-size:17px;width:20px;text-align:center;}
.sidebar-footer{
    padding:16px 16px 24px;
    border-top:1px solid rgba(77,184,112,.1);
    position:relative;z-index:1;
}
.sidebar-footer a{
    display:flex;align-items:center;gap:12px;
    color:rgba(255,255,255,.5);text-decoration:none;
    padding:10px 14px;border-radius:10px;font-size:14px;transition:all .2s;
}
.sidebar-footer a:hover{color:#ff8080;background:rgba(255,100,100,.1);}

/* ======== MAIN ======== */
.main{
    margin-left:var(--sidebar-w);
    padding:32px 36px;
    min-height:100vh;position:relative;
}
.main::before{
    content:'';position:fixed;
    top:0;left:var(--sidebar-w);right:0;bottom:0;
    background:
        radial-gradient(ellipse at 85% 10%,rgba(58,156,88,.08) 0%,transparent 50%),
        radial-gradient(ellipse at 10% 90%,rgba(45,125,70,.06) 0%,transparent 50%);
    pointer-events:none;z-index:0;
}
.main > *{position:relative;z-index:1;}

/* ======== TOPBAR ======== */
.topbar{
    display:flex;justify-content:space-between;align-items:center;
    margin-bottom:28px;
    animation:fadeUp .4s ease both;
}
.topbar-left{display:flex;align-items:center;gap:16px;}
.page-icon{
    width:52px;height:52px;
    background:linear-gradient(135deg,var(--forest-leaf),var(--forest-bright));
    border-radius:16px;
    display:flex;align-items:center;justify-content:center;
    font-size:24px;
    box-shadow:0 4px 16px rgba(45,125,70,.35);
}
.topbar-left h1{
    font-family:'Playfair Display',serif;
    font-size:26px;font-weight:700;
    color:var(--forest-deepest);line-height:1.2;
}
.topbar-left p{font-size:13px;color:var(--text-soft);margin-top:3px;font-weight:300;}
.topbar-right{display:flex;align-items:center;gap:12px;flex-wrap:wrap;}
.date-badge{
    background:var(--card-bg);border:1px solid rgba(58,156,88,.2);
    border-radius:50px;padding:8px 18px;font-size:13px;
    color:var(--text-mid);font-weight:500;
    box-shadow:0 2px 8px rgba(0,0,0,.05);
}
.btn-back{
    display:flex;align-items:center;gap:8px;
    background:var(--card-bg);border:1px solid rgba(58,156,88,.25);
    border-radius:50px;padding:10px 20px;
    color:var(--text-mid);font-size:13px;font-weight:500;
    text-decoration:none;transition:all .2s;
    box-shadow:0 2px 8px rgba(0,0,0,.05);
}
.btn-back:hover{background:var(--mist);border-color:var(--forest-leaf);}
.btn-edit-top{
    display:flex;align-items:center;gap:8px;
    background:linear-gradient(135deg,var(--forest-leaf),var(--forest-bright));
    border-radius:50px;padding:10px 22px;
    color:white;font-size:13px;font-weight:500;
    text-decoration:none;transition:all .2s;
    box-shadow:0 4px 14px rgba(45,125,70,.35);
}
.btn-edit-top:hover{transform:translateY(-1px);box-shadow:0 6px 18px rgba(45,125,70,.45);}
.btn-delete-top{
    display:flex;align-items:center;gap:8px;
    background:var(--card-bg);border:1px solid rgba(220,38,38,.25);
    border-radius:50px;padding:10px 20px;
    color:#dc2626;font-size:13px;font-weight:500;
    text-decoration:none;transition:all .2s;
}
.btn-delete-top:hover{background:var(--red-soft);}

/* ======== ALERT ======== */
.alert{
    padding:14px 20px;border-radius:12px;margin-bottom:20px;
    font-size:14px;font-weight:500;
    display:flex;align-items:center;gap:10px;
    animation:fadeUp .3s ease both;
}
.alert-success{background:rgba(58,156,88,.1);color:var(--forest-rich);border:1px solid rgba(58,156,88,.25);}
.alert-danger {background:var(--red-soft);color:#991b1b;border:1px solid #fca5a5;}

/* ======== HERO PROFILE CARD ======== */
.profile-hero{
    background:var(--card-bg);
    border-radius:20px;
    border:1px solid rgba(58,156,88,.15);
    box-shadow:0 4px 24px rgba(0,0,0,.06);
    padding:32px 36px;
    margin-bottom:24px;
    display:flex;align-items:center;gap:28px;
    position:relative;overflow:hidden;
    animation:fadeUp .4s ease .05s both;
}
.profile-hero::before{
    content:'';position:absolute;
    top:-60px;right:-60px;
    width:220px;height:220px;
    border-radius:50%;
    background:radial-gradient(circle,rgba(58,156,88,.08),transparent 70%);
    pointer-events:none;
}
.patient-avatar-big{
    width:90px;height:90px;flex-shrink:0;
    background:linear-gradient(135deg,var(--forest-leaf),var(--forest-bright));
    border-radius:24px;
    display:flex;align-items:center;justify-content:center;
    font-size:36px;font-weight:700;color:white;
    box-shadow:0 6px 20px rgba(45,125,70,.4);
    letter-spacing:-1px;
}
.profile-meta{flex:1;}
.profile-name{
    font-family:'Playfair Display',serif;
    font-size:30px;font-weight:700;
    color:var(--forest-deepest);line-height:1.1;
    margin-bottom:6px;
}
.profile-sub{
    display:flex;align-items:center;gap:12px;
    flex-wrap:wrap;margin-bottom:12px;
}
.profile-id{
    font-size:12px;color:var(--text-soft);
    background:var(--fog);border:1px solid var(--mist);
    border-radius:6px;padding:3px 10px;font-weight:500;
}
.profile-pills{display:flex;gap:8px;align-items:center;flex-wrap:wrap;}
.pill{
    display:inline-flex;align-items:center;gap:5px;
    font-size:12px;font-weight:500;padding:4px 12px;border-radius:50px;
}
.pill-category.badge-adult {background:rgba(58,156,88,.12);color:var(--forest-rich);}
.pill-category.badge-senior{background:var(--amber-light);color:#92400e;}
.pill-category.badge-child {background:rgba(59,130,246,.1);color:#1e40af;}
.pill-category.badge-pwd   {background:rgba(139,92,246,.1);color:#5b21b6;}
.pill-chronic-yes{background:var(--red-soft);color:var(--red-mid);}
.pill-chronic-no {background:rgba(58,156,88,.1);color:var(--forest-rich);}
.pill-gender{background:var(--fog);color:var(--text-mid);border:1px solid var(--mist);}
.profile-stats{
    display:flex;gap:24px;
    padding-top:14px;
    border-top:1px solid var(--mist);
    margin-top:8px;
}
.pstat{text-align:center;}
.pstat-val{font-size:22px;font-weight:700;color:var(--forest-deepest);font-family:'Playfair Display',serif;}
.pstat-lbl{font-size:11px;color:var(--text-soft);text-transform:uppercase;letter-spacing:1px;margin-top:2px;}
.pstat-sep{width:1px;background:var(--mist);align-self:stretch;}

/* ======== INFO GRID ======== */
.info-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
    margin-bottom:24px;
}

/* ======== SECTION CARDS ======== */
.section-card{
    background:var(--card-bg);
    border-radius:18px;
    border:1px solid rgba(58,156,88,.12);
    box-shadow:0 4px 18px rgba(0,0,0,.05);
    overflow:hidden;
    animation:fadeUp .4s ease both;
}
.section-card:nth-child(1){animation-delay:.08s;}
.section-card:nth-child(2){animation-delay:.12s;}
.section-card:nth-child(3){animation-delay:.16s;}
.section-card:nth-child(4){animation-delay:.20s;}

.section-head{
    display:flex;align-items:center;justify-content:space-between;
    padding:18px 24px;
    border-bottom:1px solid rgba(58,156,88,.1);
    background:linear-gradient(to right,rgba(58,156,88,.04),transparent);
}
.section-head-left{display:flex;align-items:center;gap:12px;}
.section-icon{
    width:36px;height:36px;
    border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    font-size:17px;
}
.si-green{background:rgba(58,156,88,.15);}
.si-blue {background:rgba(59,130,246,.1);}
.si-amber{background:rgba(217,119,6,.1);}
.si-red  {background:rgba(220,38,38,.1);}
.section-head h3{
    font-family:'Playfair Display',serif;
    font-size:16px;font-weight:600;color:var(--forest-deepest);
}
.section-body{padding:20px 24px;}

/* ======== INFO ROWS ======== */
.info-table{width:100%;border-collapse:collapse;}
.info-table tr{border-bottom:1px solid rgba(58,156,88,.07);}
.info-table tr:last-child{border-bottom:none;}
.info-table th{
    width:42%;padding:10px 0;
    font-size:12px;font-weight:500;
    color:var(--text-soft);text-transform:uppercase;letter-spacing:.8px;
    vertical-align:top;
}
.info-table td{
    padding:10px 0 10px 12px;
    font-size:14px;color:var(--text-dark);font-weight:400;
}
.info-table td strong{font-weight:600;}

/* ======== DATA TABLE ======== */
.bottom-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:20px;
    margin-bottom:24px;
}
.data-table{width:100%;border-collapse:collapse;}
.data-table thead tr{background:var(--fog);}
.data-table th{
    padding:10px 14px;
    font-size:11px;font-weight:600;
    color:var(--text-soft);text-transform:uppercase;letter-spacing:.8px;
    text-align:left;
}
.data-table td{
    padding:12px 14px;
    font-size:13px;color:var(--text-dark);
    border-bottom:1px solid rgba(58,156,88,.07);
}
.data-table tbody tr:last-child td{border-bottom:none;}
.data-table tbody tr:hover{background:rgba(58,156,88,.03);}

/* ======== BADGES ======== */
.badge{
    display:inline-flex;align-items:center;gap:4px;
    font-size:11px;font-weight:600;padding:3px 10px;border-radius:50px;
}
.badge-success{background:rgba(58,156,88,.12);color:var(--forest-rich);}
.badge-warning{background:var(--amber-light);color:#92400e;}
.badge-danger {background:var(--red-soft);color:var(--red-mid);}
.badge-info   {background:rgba(59,130,246,.1);color:#1e40af;}

/* ======== BTN SMALL ======== */
.btn-new-sm{
    display:inline-flex;align-items:center;gap:6px;
    background:linear-gradient(135deg,var(--forest-leaf),var(--forest-bright));
    color:white;text-decoration:none;
    font-size:12px;font-weight:500;
    padding:6px 14px;border-radius:50px;
    box-shadow:0 2px 8px rgba(45,125,70,.3);
    transition:all .2s;
}
.btn-new-sm:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(45,125,70,.4);}

/* ======== NOTICE (table not ready) ======== */
.table-notice{
    padding:14px 20px;margin:12px 20px;
    background:var(--amber-light);border:1px solid rgba(217,119,6,.25);
    border-radius:10px;font-size:13px;color:#92400e;
}

/* ======== EMPTY STATE ======== */
.empty-state{
    text-align:center;padding:32px 20px;
}
.empty-state .es-icon{font-size:36px;margin-bottom:10px;opacity:.5;}
.empty-state p{font-size:13px;color:var(--text-soft);}

/* ======== ANIMATIONS ======== */
@keyframes fadeUp{
    from{opacity:0;transform:translateY(14px);}
    to  {opacity:1;transform:translateY(0);}
}

/* ======== RESPONSIVE ======== */
@media(max-width:900px){
    .info-grid,.bottom-grid{grid-template-columns:1fr;}
    .profile-hero{flex-direction:column;text-align:center;}
    .profile-pills,.profile-sub{justify-content:center;}
    .profile-stats{justify-content:center;}
    .topbar-right{flex-direction:column;align-items:flex-start;}
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <span class="logo-icon">🌿</span>
        <h2>Barangay Health Center</h2>
        <span>Patient Management System</span>
    </div>
    <nav class="nav-section">
        <p class="nav-label">Main Menu</p>
        <a href="/health_monitoring/index.php"><span class="nav-icon">📊</span>Dashboard</a>
        <a href="/health_monitoring/patients/index.php" class="active"><span class="nav-icon">👥</span>Patients</a>
        <a href="/health_monitoring/appointments/index.php"><span class="nav-icon">📅</span>Appointments</a>
        <a href="/health_monitoring/staff/index.php"><span class="nav-icon">👤</span>Staff</a>
        <a href="/health_monitoring/health_records/index.php"><span class="nav-icon">🩺</span>Health Records</a>
        <p class="nav-label" style="margin-top:20px;">System</p>
        <a href="/health_monitoring/settings/index.php"><span class="nav-icon">⚙️</span>Settings</a>
    </nav>
    <div class="sidebar-footer">
        <a href="/health_monitoring/logout.php"><span class="nav-icon">↩</span>Logout</a>
    </div>
</aside>

<!-- MAIN -->
<main class="main">

    <!-- TOPBAR -->
    <div class="topbar">
        <div class="topbar-left">
            <div class="page-icon">👤</div>
            <div>
                <h1>Patient Profile</h1>
                <p>Viewing record for <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></p>
            </div>
        </div>
        <div class="topbar-right">
            <div class="date-badge">📅 <?php echo date('F d, Y'); ?></div>
            <a href="edit.php?id=<?php echo $id; ?>" class="btn-edit-top">✏ Edit Patient</a>
            <a href="delete.php?id=<?php echo $id; ?>"
               class="btn-delete-top"
               onclick="return confirm('Delete this patient record? This cannot be undone.')">🗑 Delete</a>
            <a href="index.php" class="btn-back">← Back to List</a>
        </div>
    </div>

    <?php
    // Show any flash alert set by add.php / edit.php
    // (assumes setAlert() stores in $_SESSION and header.php already displayed it,
    //  but if you haven't set that up yet, this is a fallback)
    if (!empty($_SESSION['alert_type']) && !empty($_SESSION['alert_msg'])):
        $aType = $_SESSION['alert_type'];
        $aMsg  = $_SESSION['alert_msg'];
        unset($_SESSION['alert_type'], $_SESSION['alert_msg']);
    ?>
    <div class="alert alert-<?php echo htmlspecialchars($aType); ?>">
        <?php echo $aType === 'success' ? '✅' : '⚠️'; ?>
        <?php echo htmlspecialchars($aMsg); ?>
    </div>
    <?php endif; ?>

    <!-- HERO PROFILE CARD -->
    <div class="profile-hero">
        <div class="patient-avatar-big"><?php echo $initials; ?></div>
        <div class="profile-meta">
            <div class="profile-name">
                <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
            </div>
            <div class="profile-sub">
                <span class="profile-id">Patient ID #<?php echo $patient['patient_id']; ?></span>
                <div class="profile-pills">
                    <span class="pill pill-category <?php echo $catClass; ?>">
                        <?php echo $catIcon . ' ' . htmlspecialchars($patient['patient_category']); ?>
                    </span>
                    <span class="pill pill-gender">
                        <?php echo $patient['gender'] === 'Male' ? '♂' : ($patient['gender'] === 'Female' ? '♀' : '⚧'); ?>
                        <?php echo htmlspecialchars($patient['gender']); ?>
                    </span>
                    <?php if ($patient['has_chronic_illness']): ?>
                        <span class="pill pill-chronic-yes">⚠ Chronic Illness</span>
                    <?php else: ?>
                        <span class="pill pill-chronic-no">✓ No Chronic Illness</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="profile-stats">
                <div class="pstat">
                    <div class="pstat-val"><?php echo $age; ?></div>
                    <div class="pstat-lbl">Years Old</div>
                </div>
                <div class="pstat-sep"></div>
                <div class="pstat">
                    <div class="pstat-val"><?php echo htmlspecialchars($patient['blood_type'] ?: '—'); ?></div>
                    <div class="pstat-lbl">Blood Type</div>
                </div>
                <div class="pstat-sep"></div>
                <div class="pstat">
                    <div class="pstat-val"><?php echo $registeredLabel; ?></div>
                    <div class="pstat-lbl">Registered</div>
                </div>
            </div>
        </div>
    </div>

    <!-- INFO GRID -->
    <div class="info-grid">

        <!-- Personal Info -->
        <div class="section-card">
            <div class="section-head">
                <div class="section-head-left">
                    <div class="section-icon si-green">🪪</div>
                    <h3>Personal Information</h3>
                </div>
            </div>
            <div class="section-body">
                <table class="info-table">
                    <tr>
                        <th>Full Name</th>
                        <td><strong><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Date of Birth</th>
                        <td><?php echo date('F d, Y', strtotime($patient['date_of_birth'])); ?></td>
                    </tr>
                    <tr>
                        <th>Gender</th>
                        <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                    </tr>
                    <tr>
                        <th>Category</th>
                        <td>
                            <span class="pill pill-category <?php echo $catClass; ?>">
                                <?php echo $catIcon . ' ' . htmlspecialchars($patient['patient_category']); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Contact Number</th>
                        <td><?php echo htmlspecialchars($patient['contact_number'] ?: 'N/A'); ?></td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td><?php echo htmlspecialchars($patient['address'] ?: 'N/A'); ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Medical Info -->
        <div class="section-card">
            <div class="section-head">
                <div class="section-head-left">
                    <div class="section-icon si-red">💊</div>
                    <h3>Medical Information</h3>
                </div>
            </div>
            <div class="section-body">
                <table class="info-table">
                    <tr>
                        <th>Blood Type</th>
                        <td><strong><?php echo htmlspecialchars($patient['blood_type'] ?: '—'); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Chronic Illness</th>
                        <td>
                            <?php if ($patient['has_chronic_illness']): ?>
                                <span class="badge badge-danger">⚠ Yes</span>
                                <?php if (!empty($patient['chronic_illness_details'])): ?>
                                    <div style="font-size:12px;color:var(--text-soft);margin-top:5px;">
                                        <?php echo htmlspecialchars($patient['chronic_illness_details']); ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="badge badge-success">✓ None</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Allergies</th>
                        <td><?php echo htmlspecialchars($patient['allergies'] ?: 'None reported'); ?></td>
                    </tr>
                    <tr>
                        <th>Emergency Contact</th>
                        <td>
                            <?php echo htmlspecialchars($patient['emergency_contact_name'] ?: 'N/A'); ?>
                            <?php if (!empty($patient['emergency_contact_number'])): ?>
                                <div style="font-size:12px;color:var(--text-soft);margin-top:3px;">
                                    📞 <?php echo htmlspecialchars($patient['emergency_contact_number']); ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- BOTTOM GRID: Appointments + Health Records -->
    <div class="bottom-grid">

        <!-- Appointments -->
        <div class="section-card">
            <div class="section-head">
                <div class="section-head-left">
                    <div class="section-icon si-blue">📅</div>
                    <h3>Recent Appointments</h3>
                </div>
                <a href="/health_monitoring/appointments/add.php?patient_id=<?php echo $id; ?>" class="btn-new-sm">＋ New</a>
            </div>
            <div class="section-body" style="padding:0;">
                <?php if ($appointments === false): ?>
                    <div class="table-notice">
                        ℹ️ Appointments table is not set up yet.
                    </div>
                <?php elseif ($appointments->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Purpose</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($apt = $appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($apt['appointment_date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($apt['appointment_time'])); ?></td>
                        <td style="max-width:120px;"><?php echo htmlspecialchars($apt['purpose'] ?? '—'); ?></td>
                        <td>
                            <?php
                                $status = $apt['status'] ?? 'Pending';
                                $bClass = $status === 'Completed' ? 'badge-success'
                                        : ($status === 'Cancelled' ? 'badge-danger' : 'badge-warning');
                            ?>
                            <span class="badge <?php echo $bClass; ?>"><?php echo htmlspecialchars($status); ?></span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <div class="es-icon">📅</div>
                    <p>No appointments yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Health Records -->
        <div class="section-card">
            <div class="section-head">
                <div class="section-head-left">
                    <div class="section-icon si-amber">🩺</div>
                    <h3>Recent Health Records</h3>
                </div>
                <a href="/health_monitoring/health_records/add.php?patient_id=<?php echo $id; ?>" class="btn-new-sm">＋ New</a>
            </div>
            <div class="section-body" style="padding:0;">
                <?php if ($records === false): ?>
                    <div class="table-notice">
                        ℹ️ Health records table is not set up yet.
                    </div>
                <?php elseif ($records->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>BP</th>
                            <th>Temp</th>
                            <th>Complaint</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($rec = $records->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($rec['record_date'])); ?></td>
                        <td style="font-weight:600;color:var(--forest-rich);">
                            <?php
                                $sys = $rec['blood_pressure_systolic'] ?? '—';
                                $dia = $rec['blood_pressure_diastolic'] ?? '—';
                                echo $sys . '/' . $dia;
                            ?>
                        </td>
                        <td><?php echo !empty($rec['temperature']) ? $rec['temperature'] . '°C' : '—'; ?></td>
                        <td style="max-width:100px;font-size:12px;color:var(--text-soft);">
                            <?php
                                $complaint = $rec['chief_complaint'] ?? '';
                                echo htmlspecialchars(strlen($complaint) > 28 ? substr($complaint, 0, 28) . '…' : ($complaint ?: '—'));
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <div class="es-icon">🩺</div>
                    <p>No health records yet.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</main>

<?php
$conn->close();
require_once '../includes/footer.php';
?>
</body>
</html>