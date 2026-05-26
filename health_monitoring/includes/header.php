<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['staff_id'])) {
    header('Location: /health_monitoring/barangay_health/login.php');
    exit;
}

require_once __DIR__ . '/../config/database.php';

// Determine current page for active nav
$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir  = basename(dirname($_SERVER['PHP_SELF']));

function navActive($dir, $currentDir, $currentPage) {
    if ($dir === 'index' && $currentPage === 'index.php' && $currentDir === 'barangay_health') return 'active';
    if ($dir !== 'index' && $currentDir === $dir) return 'active';
    return '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle ?? 'Barangay Health Center'; ?></title>
  <link rel="stylesheet" href="/health_monitoring/css/style.css">
</head>
<body>

<!-- ── Sidebar Navigation ── -->
<nav class="navbar">
  <div class="nav-brand">
    <a href="/health_monitoring/barangay_health/">
      Barangay Health Center
    </a>
  </div>

  <ul class="nav-links">
    <li>
      <a href="/health_monitoring/barangay_health/" class="<?php echo navActive('index', $currentDir, $currentPage); ?>">
        <span class="icon">📊</span> Dashboard
      </a>
    </li>
    <li>
      <a href="/health_monitoring/patients/" class="<?php echo navActive('patients', $currentDir, $currentPage); ?>">
        <span class="icon">👥</span> Patients
      </a>
    </li>
    <li>
      <a href="/health_monitoring/appointments/" class="<?php echo navActive('appointments', $currentDir, $currentPage); ?>">
        <span class="icon">📅</span> Appointments
      </a>
    </li>
    <li>
      <a href="/health_monitoring/staff/" class="<?php echo navActive('staff', $currentDir, $currentPage); ?>">
        <span class="icon">👤</span> Staff
      </a>
    </li>
  </ul>

  <!-- Staff info + logout at bottom -->
  <div style="padding: 1rem 1.25rem; border-top: 1px solid rgba(255,255,255,.08); margin-top: auto;">
    <div style="display:flex; align-items:center; gap:.75rem; margin-bottom:.75rem;">
      <div style="width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--teal),#06d6a0);display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0;">
        👤
      </div>
      <div>
        <div style="color:white;font-weight:600;font-size:.85rem;line-height:1.2;"><?php echo htmlspecialchars($_SESSION['staff_name']); ?></div>
        <div style="color:rgba(255,255,255,.5);font-size:.75rem;"><?php echo htmlspecialchars($_SESSION['staff_position'] ?? 'Staff'); ?></div>
      </div>
    </div>
    <a href="/health_monitoring/barangay_health/logout.php"
       style="display:flex;align-items:center;gap:.5rem;color:rgba(255,255,255,.55);font-size:.83rem;text-decoration:none;padding:.45rem .5rem;border-radius:6px;transition:all .2s;"
       onmouseover="this.style.color='#f87171';this.style.background='rgba(239,68,68,.12)';"
       onmouseout="this.style.color='rgba(255,255,255,.55)';this.style.background='transparent';">
      🚪 Sign Out
    </a>
  </div>
</nav>

<!-- ── Main Content ── -->
<main class="container">
  <?php
  $alert = getAlert();
  if ($alert): ?>
    <div class="alert alert-<?php echo $alert['type']; ?>">
      <?php echo $alert['type'] === 'success' ? '✅' : '⚠️'; ?>
      <?php echo $alert['message']; ?>
    </div>
  <?php endif; ?>