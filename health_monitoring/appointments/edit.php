<?php
$pageTitle    = "Edit Appointment - Barangay Health Center";
$pageTopTitle = "✏️ Edit Appointment";
$pageTopSub   = "Update the details of an existing appointment";
require_once('../includes/header.php');

$conn = getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    setAlert('danger', 'Invalid appointment ID.');
    header('Location: index.php');
    exit;
}

$result = $conn->query("
    SELECT a.*, p.first_name, p.last_name
    FROM appointments a
    JOIN patients p ON a.patient_id = p.patient_id
    WHERE a.appointment_id = $id
");

$appointment = $result ? $result->fetch_assoc() : null;

if (!$appointment) {
    setAlert('danger', 'Appointment not found.');
    header('Location: index.php');
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date    = sanitize($conn, $_POST['appointment_date']);
    $time    = sanitize($conn, $_POST['appointment_time']);
    $purpose = sanitize($conn, $_POST['purpose']);
    $status  = sanitize($conn, $_POST['status']);
    $notes   = sanitize($conn, $_POST['notes']);

    if (!$date || !$time || !$purpose || !$status) {
        setAlert('danger', 'Please fill in all required fields.');
    } else {
        $sql  = "UPDATE appointments
                 SET appointment_date = ?, appointment_time = ?, purpose = ?, status = ?, notes = ?
                 WHERE appointment_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $date, $time, $purpose, $status, $notes, $id);

        if ($stmt->execute()) {
            setAlert('success', 'Appointment updated successfully!');
            header('Location: index.php');
            exit;
        } else {
            setAlert('danger', 'Error updating appointment: ' . $conn->error);
        }
    }
    // Re-populate with POST values on error
    $appointment['appointment_date'] = $date;
    $appointment['appointment_time'] = $time;
    $appointment['purpose']          = $purpose;
    $appointment['status']           = $status;
    $appointment['notes']            = $notes;
}

$statuses = ['Scheduled', 'Completed', 'Cancelled', 'No Show'];
$purposes = [
    'General Checkup', 'Vaccination', 'Prenatal Checkup',
    'Blood Pressure Monitoring', 'Blood Sugar Monitoring',
    'Follow-up Consultation', 'Medical Certificate',
    'Wound Dressing', 'Other',
];
?>

<div class="page-wrap">
  <div class="card">

    <div class="card-header">
      <h2 class="card-title">✏️ Edit Appointment</h2>
      <a href="index.php" class="btn btn-outline">← Back to List</a>
    </div>

    <!-- Patient info strip -->
    <div style="
      background: #fdf0f0;
      border-bottom: 1px solid #f5d5d5;
      padding: 12px 24px;
      display: flex;
      align-items: center;
      gap: 12px;
    ">
      <div style="
        width: 38px; height: 38px;
        border-radius: 50%;
        background: #c0392b;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        flex-shrink: 0;
      ">
        <?php echo strtoupper(substr($appointment['first_name'], 0, 1) . substr($appointment['last_name'], 0, 1)); ?>
      </div>
      <div>
        <div style="font-weight: 700; color: #1a1a1a; font-size: 14px">
          <?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?>
        </div>
        <div style="font-size: 12px; color: #888">Appointment #<?php echo $id; ?></div>
      </div>
      <?php
        $pillMap = [
          'Completed' => 'pill-success',
          'Cancelled' => 'pill-danger',
          'No Show'   => 'pill-info',
          'Scheduled' => 'pill-warning',
        ];
        $pill = $pillMap[$appointment['status']] ?? 'pill-info';
      ?>
      <span class="status-pill <?php echo $pill; ?>" style="margin-left: auto">
        <?php echo htmlspecialchars($appointment['status']); ?>
      </span>
    </div>

    <form method="POST" style="padding: 24px">

      <!-- Hidden patient field (readonly display only) -->
      <div class="form-group">
        <label>Patient</label>
        <input
          type="text"
          class="form-control"
          value="<?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?>"
          readonly
        >
      </div>

      <!-- Date, Time, Status -->
      <div class="form-row">
        <div class="form-group">
          <label>Date <span style="color:#c0392b">*</span></label>
          <input
            type="date"
            name="appointment_date"
            class="form-control"
            value="<?php echo htmlspecialchars($appointment['appointment_date']); ?>"
            required
          >
        </div>
        <div class="form-group">
          <label>Time <span style="color:#c0392b">*</span></label>
          <input
            type="time"
            name="appointment_time"
            class="form-control"
            value="<?php echo htmlspecialchars($appointment['appointment_time']); ?>"
            required
          >
        </div>
        <div class="form-group">
          <label>Status <span style="color:#c0392b">*</span></label>
          <select name="status" class="form-control" required>
            <?php foreach ($statuses as $s): ?>
              <option value="<?php echo $s; ?>" <?php echo $appointment['status'] === $s ? 'selected' : ''; ?>>
                <?php echo $s; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Purpose -->
      <div class="form-group">
        <label>Purpose <span style="color:#c0392b">*</span></label>
        <select name="purpose" class="form-control" required>
          <?php foreach ($purposes as $p): ?>
            <option value="<?php echo $p; ?>" <?php echo $appointment['purpose'] === $p ? 'selected' : ''; ?>>
              <?php echo $p; ?>
            </option>
          <?php endforeach; ?>
        </select>
        <!-- Fallback: if purpose is a custom value not in the list -->
        <?php if (!in_array($appointment['purpose'], $purposes)): ?>
        <input
          type="text"
          name="purpose"
          class="form-control"
          style="margin-top: 8px"
          value="<?php echo htmlspecialchars($appointment['purpose']); ?>"
          placeholder="Or type a custom purpose"
        >
        <?php endif; ?>
      </div>

      <!-- Notes -->
      <div class="form-group">
        <label>Notes</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes..."><?php
          echo htmlspecialchars($appointment['notes'] ?? '');
        ?></textarea>
      </div>

      <!-- Actions -->
      <div style="display:flex;gap:10px;padding-top:4px">
        <button type="submit" class="btn btn-primary">💾 Update Appointment</button>
        <a href="index.php" class="btn btn-outline">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php
$conn->close();
require_once '../includes/footer.php';
?>