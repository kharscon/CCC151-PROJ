<?php
$pageTitle = "Edit Patient - Barangay Health Center";
require_once('../includes/header.php');
$conn = getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$patient = $conn->query("SELECT * FROM patients WHERE patient_id = $id")->fetch_assoc();

if (!$patient) {
    setAlert('danger', 'Patient not found.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name     = sanitize($conn, $_POST['first_name']);
    $last_name      = sanitize($conn, $_POST['last_name']);
    $dob            = sanitize($conn, $_POST['date_of_birth']);
    $gender         = sanitize($conn, $_POST['gender']);
    $contact        = sanitize($conn, $_POST['contact_number']);
    $address        = sanitize($conn, $_POST['address']);
    $emergency_name = sanitize($conn, $_POST['emergency_contact_name']);
    $emergency_number = sanitize($conn, $_POST['emergency_contact_number']);
    $category       = sanitize($conn, $_POST['patient_category']);
    $has_chronic    = isset($_POST['has_chronic_illness']) ? 1 : 0;
    $chronic_details = sanitize($conn, $_POST['chronic_illness_details']);
    $blood_type     = sanitize($conn, $_POST['blood_type']);
    $allergies      = sanitize($conn, $_POST['allergies']);

    $sql = "UPDATE patients SET
            first_name = ?, last_name = ?, date_of_birth = ?, gender = ?,
            contact_number = ?, address = ?, emergency_contact_name = ?,
            emergency_contact_number = ?, patient_category = ?, has_chronic_illness = ?,
            chronic_illness_details = ?, blood_type = ?, allergies = ?
            WHERE patient_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssi", $first_name, $last_name, $dob, $gender, $contact,
                      $address, $emergency_name, $emergency_number, $category,
                      $has_chronic, $chronic_details, $blood_type, $allergies, $id);

    if ($stmt->execute()) {
        setAlert('success', 'Patient updated successfully!');
        header('Location: view.php?id=' . $id);
        exit;
    } else {
        setAlert('danger', 'Error updating patient: ' . $conn->error);
    }
}

// Pre-compute age for the header badge
$dob_dt = new DateTime($patient['date_of_birth']);
$age    = (new DateTime())->diff($dob_dt)->y;
$blood_types = ['Unknown','A+','A-','B+','B-','AB+','AB-','O+','O-'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Patient — Barangay Health Center</title>
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
    --text-dark:      #0d1f12;
    --text-mid:       #2e4a35;
    --text-soft:      #6b8f74;
    --white:          #ffffff;
    --card-bg:        rgba(255,255,255,0.94);
    --sidebar-w:      260px;
    --input-border:   #c8e0cd;
    --input-focus:    #3a9c58;
    --amber:          #d97706;
    --amber-bg:       rgba(217,119,6,0.1);
    --red-soft:       #fee2e2;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'DM Sans', sans-serif;
    background: var(--fog);
    color: var(--text-dark);
    min-height: 100vh;
}

/* ======== SIDEBAR ======== */
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
        radial-gradient(ellipse at 20% 80%, rgba(45,125,70,0.3) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(26,71,38,0.4) 0%, transparent 50%);
    pointer-events: none;
}
.sidebar-logo {
    padding: 28px 24px 24px;
    border-bottom: 1px solid rgba(77,184,112,0.15);
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
    color: rgba(168,213,181,0.5); padding: 0 10px; margin-bottom: 8px;
}
.sidebar a {
    display: flex; align-items: center; gap: 12px;
    color: rgba(255,255,255,0.75); text-decoration: none;
    padding: 11px 14px; border-radius: 10px; margin-bottom: 4px;
    font-size: 14px; transition: all 0.2s; position: relative;
}
.sidebar a:hover { background: rgba(77,184,112,0.15); color: var(--white); }
.sidebar a.active {
    background: linear-gradient(135deg, rgba(58,156,88,0.5), rgba(45,125,70,0.3));
    color: var(--white); font-weight: 500;
    box-shadow: inset 0 0 0 1px rgba(77,184,112,0.3);
}
.sidebar a.active::before {
    content: ''; position: absolute; left: 0; top: 50%; transform: translateY(-50%);
    width: 3px; height: 20px; background: var(--forest-light); border-radius: 0 3px 3px 0;
}
.nav-icon { font-size: 17px; width: 20px; text-align: center; }
.sidebar-footer {
    padding: 16px 16px 24px;
    border-top: 1px solid rgba(77,184,112,0.1);
    position: relative; z-index: 1;
}
.sidebar-footer a {
    display: flex; align-items: center; gap: 12px;
    color: rgba(255,255,255,0.5); text-decoration: none;
    padding: 10px 14px; border-radius: 10px; font-size: 14px; transition: all 0.2s;
}
.sidebar-footer a:hover { color: #ff8080; background: rgba(255,100,100,0.1); }

/* ======== MAIN ======== */
.main {
    margin-left: var(--sidebar-w);
    padding: 36px 40px; min-height: 100vh; position: relative;
}
.main::before {
    content: ''; position: fixed; top: 0; left: var(--sidebar-w); right: 0; bottom: 0;
    background:
        radial-gradient(ellipse at 85% 10%, rgba(58,156,88,0.07) 0%, transparent 50%),
        radial-gradient(ellipse at 10% 90%, rgba(45,125,70,0.05) 0%, transparent 50%);
    pointer-events: none; z-index: 0;
}
.main > * { position: relative; z-index: 1; }

/* ======== PAGE HEADER ======== */
.page-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 28px;
    animation: fadeUp 0.4s ease both;
}
.page-header-left { display: flex; align-items: center; gap: 16px; }
.page-icon {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, var(--amber), #f59e0b);
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 24px;
    box-shadow: 0 4px 16px rgba(217,119,6,0.35);
}
.page-header h1 {
    font-family: 'Playfair Display', serif;
    font-size: 26px; font-weight: 700; color: var(--forest-deepest);
}
.page-header p { font-size: 13px; color: var(--text-soft); margin-top: 3px; }

.header-actions { display: flex; gap: 10px; align-items: center; }

.btn-back {
    display: inline-flex; align-items: center; gap: 8px;
    background: var(--white); color: var(--text-mid);
    border: 1px solid var(--input-border);
    padding: 10px 20px; border-radius: 50px;
    text-decoration: none; font-size: 14px; font-weight: 500;
    transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
.btn-back:hover { background: var(--fog); border-color: var(--moss); color: var(--forest-mid); }

/* ======== PATIENT IDENTITY STRIP ======== */
.identity-strip {
    display: flex; align-items: center; gap: 18px;
    background: linear-gradient(135deg, var(--forest-mid), var(--forest-rich));
    border-radius: 16px; padding: 20px 28px;
    margin-bottom: 20px; color: var(--white);
    box-shadow: 0 4px 20px rgba(26,71,38,0.35);
    animation: fadeUp 0.45s ease 0.05s both;
}
.identity-avatar {
    width: 54px; height: 54px; border-radius: 14px;
    background: rgba(255,255,255,0.15);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; flex-shrink: 0;
}
.identity-name {
    font-family: 'Playfair Display', serif;
    font-size: 20px; font-weight: 700; line-height: 1.2;
}
.identity-meta { font-size: 13px; color: rgba(255,255,255,0.7); margin-top: 4px; }
.identity-meta span {
    display: inline-block; margin-right: 16px;
}
.identity-badge {
    margin-left: auto;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    padding: 5px 14px; border-radius: 20px;
    font-size: 12px; font-weight: 600;
    letter-spacing: 0.5px;
}

/* ======== FORM CARD ======== */
.form-card {
    background: var(--card-bg);
    border-radius: 22px;
    box-shadow: 0 6px 30px rgba(0,0,0,0.07);
    border: 1px solid rgba(255,255,255,0.85);
    overflow: hidden;
    animation: fadeUp 0.5s ease 0.1s both;
}

/* ======== SECTIONS ======== */
.form-section {
    padding: 30px 36px;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}
.form-section:last-of-type { border-bottom: none; }

.section-heading {
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 22px;
}
.section-dot {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 17px; flex-shrink: 0;
}
.dot-personal  { background: rgba(58,156,88,0.12); }
.dot-emergency { background: rgba(217,119,6,0.12); }
.dot-medical   { background: rgba(168,85,247,0.12); }

.section-heading h3 {
    font-family: 'Playfair Display', serif;
    font-size: 17px; font-weight: 600; color: var(--forest-mid);
}
.section-heading p { font-size: 12px; color: var(--text-soft); margin-top: 2px; }

/* ======== FORM ROWS ======== */
.form-row { display: grid; gap: 16px; margin-bottom: 16px; }
.form-row.cols-2 { grid-template-columns: 1fr 1fr; }
.form-row.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
.form-row.cols-1 { grid-template-columns: 1fr; }

.form-group { display: flex; flex-direction: column; gap: 7px; }

.form-group label {
    font-size: 12px; font-weight: 600;
    letter-spacing: 0.5px; text-transform: uppercase;
    color: var(--text-mid);
}
.form-group label .req { color: var(--forest-leaf); margin-left: 2px; }

.form-control {
    background: var(--fog);
    border: 1.5px solid var(--input-border);
    border-radius: 10px; padding: 11px 15px;
    font-size: 14px; font-family: 'DM Sans', sans-serif;
    color: var(--text-dark); transition: all 0.2s; outline: none; width: 100%;
}
.form-control:focus {
    border-color: var(--input-focus);
    background: var(--white);
    box-shadow: 0 0 0 3px rgba(58,156,88,0.12);
}
.form-control::placeholder { color: #b0c8b8; }

select.form-control {
    cursor: pointer; appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236b8f74' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 14px center; padding-right: 38px;
}

textarea.form-control { resize: vertical; min-height: 80px; }

/* ======== CHANGE HIGHLIGHT ======== */
.form-control:not(:placeholder-shown):not(select) { border-color: var(--input-border); }

/* ======== CHECKBOX ROW ======== */
.checkbox-row {
    display: flex; align-items: center; gap: 12px;
    padding: 14px 18px;
    background: var(--fog); border: 1.5px solid var(--input-border);
    border-radius: 10px; cursor: pointer; transition: all 0.2s; user-select: none;
}
.checkbox-row:hover { border-color: var(--moss); background: var(--mist); }
.checkbox-row.checked { border-color: var(--forest-leaf); background: rgba(58,156,88,0.06); }
.checkbox-row input[type="checkbox"] {
    width: 18px; height: 18px; accent-color: var(--forest-leaf);
    cursor: pointer; flex-shrink: 0;
}
.checkbox-row .cb-label { font-size: 14px; font-weight: 500; color: var(--text-dark); }
.checkbox-row .cb-sub { font-size: 12px; color: var(--text-soft); margin-top: 1px; }

.chronic-details-wrap {
    overflow: hidden; max-height: 0; opacity: 0;
    transition: max-height 0.35s ease, opacity 0.3s ease, margin 0.3s ease;
    margin-top: 0;
}
.chronic-details-wrap.open { max-height: 200px; opacity: 1; margin-top: 16px; }

/* ======== FORM FOOTER ======== */
.form-footer {
    padding: 22px 36px;
    background: linear-gradient(135deg, rgba(26,71,38,0.04), rgba(58,156,88,0.04));
    border-top: 1px solid rgba(58,156,88,0.1);
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
}
.btn-update {
    display: inline-flex; align-items: center; gap: 10px;
    background: linear-gradient(135deg, var(--amber), #f59e0b);
    color: var(--white); border: none;
    padding: 13px 32px; border-radius: 50px;
    font-size: 15px; font-weight: 600; font-family: 'DM Sans', sans-serif;
    cursor: pointer; transition: all 0.2s;
    box-shadow: 0 4px 18px rgba(217,119,6,0.38);
}
.btn-update:hover { transform: translateY(-2px); box-shadow: 0 7px 24px rgba(217,119,6,0.5); }
.btn-update:active { transform: translateY(0); }

.btn-cancel {
    display: inline-flex; align-items: center; gap: 8px;
    background: transparent; color: var(--text-soft);
    border: 1.5px solid var(--input-border);
    padding: 12px 24px; border-radius: 50px;
    text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s;
}
.btn-cancel:hover { border-color: #dc2626; color: #dc2626; background: var(--red-soft); }

.footer-note { font-size: 12px; color: var(--text-soft); display: flex; align-items: center; gap: 6px; }

/* ======== ANIMATION ======== */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* ======== RESPONSIVE ======== */
@media (max-width: 960px) {
    .form-row.cols-3 { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 768px) {
    :root { --sidebar-w: 0px; }
    .sidebar { display: none; }
    .main { padding: 20px 16px; }
    .form-row.cols-2,
    .form-row.cols-3 { grid-template-columns: 1fr; }
    .form-section { padding: 22px 18px; }
    .form-footer { flex-direction: column; align-items: stretch; }
    .btn-update, .btn-cancel { justify-content: center; }
    .identity-strip { flex-wrap: wrap; }
    .identity-badge { margin-left: 0; }
    .page-header { flex-direction: column; align-items: flex-start; gap: 14px; }
}
</style>
</head>
<body>

<!-- ======== SIDEBAR ======== -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <span class="logo-icon">🌿</span>
        <h2>Barangay Health Center</h2>
        <span>Patient Management System</span>
    </div>
    <nav class="nav-section">
        <p class="nav-label">Main Menu</p>
        <a href="../index.php"><span class="nav-icon">📊</span>Dashboard</a>
        <a href="index.php" class="active"><span class="nav-icon">👥</span>Patients</a>
        <a href="#"><span class="nav-icon">📅</span>Appointments</a>
        <a href="#"><span class="nav-icon">👤</span>Staff</a>
        <p class="nav-label" style="margin-top:20px;">System</p>
        <a href="#"><span class="nav-icon">⚙️</span>Settings</a>
    </nav>
    <div class="sidebar-footer">
        <a href="../logout.php"><span class="nav-icon">↩</span>Logout</a>
    </div>
</aside>

<!-- ======== MAIN ======== -->
<main class="main">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <div class="page-header-left">
            <div class="page-icon">✏️</div>
            <div>
                <h1>Edit Patient</h1>
                <p>Update the information for this patient record</p>
            </div>
        </div>
        <div class="header-actions">
            <a href="view.php?id=<?= $id ?>" class="btn-back">← Back to Profile</a>
            <a href="index.php" class="btn-back">↩ Patient List</a>
        </div>
    </div>

    <!-- PATIENT IDENTITY STRIP -->
    <div class="identity-strip">
        <div class="identity-avatar">
            <?= $patient['gender'] === 'Female' ? '👩' : '👨' ?>
        </div>
        <div>
            <div class="identity-name">
                <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?>
            </div>
            <div class="identity-meta">
                <span>🪪 ID #<?= $patient['patient_id'] ?></span>
                <span>🎂 <?= $age ?> years old</span>
                <span>🩸 <?= htmlspecialchars($patient['blood_type'] ?: 'Unknown') ?></span>
            </div>
        </div>
        <div class="identity-badge">
            <?= match($patient['patient_category']) {
                'Child'  => '🧒 Child',
                'Senior' => '🧓 Senior',
                'PWD'    => '♿ PWD',
                default  => '🧑 Adult',
            } ?>
        </div>
    </div>

    <!-- FORM CARD -->
    <div class="form-card">
        <form method="POST">

            <!-- SECTION 1: Personal Information -->
            <div class="form-section">
                <div class="section-heading">
                    <div class="section-dot dot-personal">🪪</div>
                    <div>
                        <h3>Personal Information</h3>
                        <p>Basic identification and contact details</p>
                    </div>
                </div>

                <div class="form-row cols-2">
                    <div class="form-group">
                        <label>First Name <span class="req">*</span></label>
                        <input type="text" name="first_name" class="form-control"
                               value="<?= htmlspecialchars($patient['first_name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name <span class="req">*</span></label>
                        <input type="text" name="last_name" class="form-control"
                               value="<?= htmlspecialchars($patient['last_name']) ?>" required>
                    </div>
                </div>

                <div class="form-row cols-3">
                    <div class="form-group">
                        <label>Date of Birth <span class="req">*</span></label>
                        <input type="date" name="date_of_birth" class="form-control"
                               value="<?= $patient['date_of_birth'] ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Gender <span class="req">*</span></label>
                        <select name="gender" class="form-control" required>
                            <?php foreach (['Male','Female','Other'] as $g): ?>
                                <option value="<?= $g ?>" <?= $patient['gender'] == $g ? 'selected' : '' ?>><?= $g ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Patient Category <span class="req">*</span></label>
                        <select name="patient_category" class="form-control" required>
                            <option value="Child"  <?= $patient['patient_category'] == 'Child'  ? 'selected' : '' ?>>🧒 Child (0–17)</option>
                            <option value="Adult"  <?= $patient['patient_category'] == 'Adult'  ? 'selected' : '' ?>>🧑 Adult (18–59)</option>
                            <option value="Senior" <?= $patient['patient_category'] == 'Senior' ? 'selected' : '' ?>>🧓 Senior Citizen (60+)</option>
                            <option value="PWD"    <?= $patient['patient_category'] == 'PWD'    ? 'selected' : '' ?>>♿ PWD</option>
                        </select>
                    </div>
                </div>

                <div class="form-row cols-2">
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="contact_number" class="form-control"
                               value="<?= htmlspecialchars($patient['contact_number']) ?>"
                               placeholder="e.g. 09XX XXX XXXX">
                    </div>
                    <div class="form-group">
                        <label>Blood Type</label>
                        <select name="blood_type" class="form-control">
                            <?php foreach ($blood_types as $bt): ?>
                                <option value="<?= $bt ?>" <?= $patient['blood_type'] == $bt ? 'selected' : '' ?>><?= $bt ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row cols-1">
                    <div class="form-group">
                        <label>Home Address</label>
                        <textarea name="address" class="form-control" rows="2"
                                  placeholder="Street, Barangay, Municipality..."><?= htmlspecialchars($patient['address']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Emergency Contact -->
            <div class="form-section">
                <div class="section-heading">
                    <div class="section-dot dot-emergency">🚨</div>
                    <div>
                        <h3>Emergency Contact</h3>
                        <p>Person to notify in case of emergency</p>
                    </div>
                </div>

                <div class="form-row cols-2">
                    <div class="form-group">
                        <label>Contact Person Name</label>
                        <input type="text" name="emergency_contact_name" class="form-control"
                               value="<?= htmlspecialchars($patient['emergency_contact_name']) ?>">
                    </div>
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="emergency_contact_number" class="form-control"
                               value="<?= htmlspecialchars($patient['emergency_contact_number']) ?>">
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Medical Information -->
            <div class="form-section">
                <div class="section-heading">
                    <div class="section-dot dot-medical">💊</div>
                    <div>
                        <h3>Medical Information</h3>
                        <p>Chronic conditions and known allergies</p>
                    </div>
                </div>

                <div class="form-row cols-1" style="margin-bottom:0;">
                    <label class="checkbox-row <?= $patient['has_chronic_illness'] ? 'checked' : '' ?>"
                           id="chronicRow">
                        <input type="checkbox" name="has_chronic_illness" id="chronicCheck"
                               onchange="toggleChronic(this)"
                               <?= $patient['has_chronic_illness'] ? 'checked' : '' ?>>
                        <div>
                            <div class="cb-label">Patient has a chronic illness</div>
                            <div class="cb-sub">Check this to update illness details</div>
                        </div>
                    </label>
                </div>

                <div class="chronic-details-wrap <?= $patient['has_chronic_illness'] ? 'open' : '' ?>"
                     id="chronicDetails">
                    <div class="form-group">
                        <label>Chronic Illness Details</label>
                        <textarea name="chronic_illness_details" class="form-control" rows="2"
                                  placeholder="e.g. Diabetes Type 2, Hypertension, Asthma..."><?= htmlspecialchars($patient['chronic_illness_details']) ?></textarea>
                    </div>
                </div>

                <div class="form-row cols-1" style="margin-top:18px;">
                    <div class="form-group">
                        <label>Known Allergies</label>
                        <textarea name="allergies" class="form-control" rows="2"
                                  placeholder="List any known allergies..."><?= htmlspecialchars($patient['allergies']) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- FORM FOOTER -->
            <div class="form-footer">
                <div class="footer-note">✏️ Editing record for <strong style="color:var(--forest-mid);margin:0 4px;"><?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></strong></div>
                <div style="display:flex;gap:12px;align-items:center;">
                    <a href="view.php?id=<?= $id ?>" class="btn-cancel">✕ Cancel</a>
                    <button type="submit" class="btn-update">✓ Update Patient</button>
                </div>
            </div>

        </form>
    </div>

</main>

<script>
function toggleChronic(checkbox) {
    const details = document.getElementById('chronicDetails');
    const row     = document.getElementById('chronicRow');
    if (checkbox.checked) {
        details.classList.add('open');
        row.classList.add('checked');
    } else {
        details.classList.remove('open');
        row.classList.remove('checked');
    }
}
</script>

<?php
$conn->close();
require_once '../includes/footer.php';
?>
</body>
</html>