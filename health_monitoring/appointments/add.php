<?php
$pageTitle    = "Schedule Appointment - Barangay Health Center";
$pageTopTitle = "📅 Schedule Appointment";
$pageTopSub   = "Fill in the details below to book a new appointment";
require_once('../includes/header.php');

$conn = getConnection();

// Pre-select patient if coming from patient view
$selected_patient = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;

// Get all patients for dropdown
$patients = $conn->query("
    SELECT patient_id, first_name, last_name
    FROM patients
    ORDER BY last_name, first_name
");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = (int)$_POST['patient_id'];
    $date       = sanitize($conn, $_POST['appointment_date']);
    $time       = sanitize($conn, $_POST['appointment_time']);
    $purpose    = sanitize($conn, $_POST['purpose']);
    $notes      = sanitize($conn, $_POST['notes']);

    if (!$patient_id || !$date || !$time || !$purpose) {
        setAlert('danger', 'Please fill in all required fields.');
    } else {
        $sql  = "INSERT INTO appointments (patient_id, appointment_date, appointment_time, purpose, notes, status)
                 VALUES (?, ?, ?, ?, ?, 'Scheduled')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $patient_id, $date, $time, $purpose, $notes);

        if ($stmt->execute()) {
            setAlert('success', 'Appointment scheduled successfully!');
            header('Location: index.php');
            exit;
        } else {
            setAlert('danger', 'Error scheduling appointment: ' . $conn->error);
        }
    }
}
?>

<div class="page-wrap">
  <div class="card">

    <div class="card-header">
      <h2 class="card-title">📅 Schedule New Appointment</h2>
      <a href="index.php" class="btn btn-outline">← Back to List</a>
    </div>

    <form method="POST" style="padding: 24px">

      <!-- Patient -->
      <div class="form-group">
        <label>Patient <span style="color:#c0392b">*</span></label>
        <select name="patient_id" class="form-control" required>
          <option value="">— Select Patient —</option>
          <?php if ($patients): while ($p = $patients->fetch_assoc()): ?>
            <option
              value="<?php echo $p['patient_id']; ?>"
              <?php echo $selected_patient == $p['patient_id'] ? 'selected' : ''; ?>
            >
              <?php echo htmlspecialchars($p['last_name'] . ', ' . $p['first_name']); ?>
            </option>
          <?php endwhile; endif; ?>
        </select>
      </div>

      <!-- Date & Time -->
      <div class="form-row">
        <div class="form-group">
          <label>Date <span style="color:#c0392b">*</span></label>
          <input
            type="date"
            name="appointment_date"
            class="form-control"
            min="<?php echo date('Y-m-d'); ?>"
            value="<?php echo isset($_POST['appointment_date']) ? htmlspecialchars($_POST['appointment_date']) : date('Y-m-d'); ?>"
            required
          >
        </div>
        <div class="form-group">
          <label>Time <span style="color:#c0392b">*</span></label>
          <input
            type="time"
            name="appointment_time"
            class="form-control"
            value="<?php echo isset($_POST['appointment_time']) ? htmlspecialchars($_POST['appointment_time']) : '08:00'; ?>"
            required
          >
        </div>
      </div>

      <!-- Purpose -->
      <div class="form-group">
        <label>Purpose <span style="color:#c0392b">*</span></label>
        <select name="purpose" class="form-control" required>
          <option value="">— Select Purpose —</option>
          <?php
          $purposes = [
            'General Checkup', 'Vaccination', 'Prenatal Checkup',
            'Blood Pressure Monitoring', 'Blood Sugar Monitoring',
            'Follow-up Consultation', 'Medical Certificate',
            'Wound Dressing', 'Other',
          ];
          foreach ($purposes as $p):
            $sel = (isset($_POST['purpose']) && $_POST['purpose'] === $p) ? 'selected' : '';
          ?>
            <option value="<?php echo $p; ?>" <?php echo $sel; ?>><?php echo $p; ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Notes -->
      <div class="form-group">
        <label>Notes</label>
        <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes or instructions..."><?php
          echo isset($_POST['notes']) ? htmlspecialchars($_POST['notes']) : '';
        ?></textarea>
      </div>

      <!-- Actions -->
      <div style="display:flex;gap:10px;padding-top:4px">
        <button type="submit" class="btn btn-primary">✅ Schedule Appointment</button>
        <a href="index.php" class="btn btn-outline">Cancel</a>
      </div>

    </form>
  </div>
</div>

<?php
$conn->close();
require_once '../includes/footer.php';
?>