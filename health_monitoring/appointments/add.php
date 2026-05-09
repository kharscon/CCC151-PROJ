<?php
$pageTitle = "Schedule Appointment - Barangay Health Center";
require_once('../includes/header.php');

$conn = getConnection();

// Get all patients for dropdown
$patients = $conn->query("SELECT patient_id, first_name, last_name FROM patients ORDER BY last_name, first_name");

$selected_patient = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = (int)$_POST['patient_id'];
    $date = sanitize($conn, $_POST['appointment_date']);
    $time = sanitize($conn, $_POST['appointment_time']);
    $purpose = sanitize($conn, $_POST['purpose']);
    $notes = sanitize($conn, $_POST['notes']);
    
    $sql = "INSERT INTO appointments (patient_id, appointment_date, appointment_time, purpose, notes) 
            VALUES (?, ?, ?, ?, ?)";
    
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
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">📅 Schedule New Appointment</h2>
        <a href="index.php" class="btn btn-warning">← Back to List</a>
    </div>
    
    <form method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Patient *</label>
                <select name="patient_id" class="form-control" required>
                    <option value="">Select Patient</option>
                    <?php while ($p = $patients->fetch_assoc()): ?>
                        <option value="<?php echo $p['patient_id']; ?>" <?php echo $selected_patient == $p['patient_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($p['last_name'] . ', ' . $p['first_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Date *</label>
                <input type="date" name="appointment_date" class="form-control" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label>Time *</label>
                <input type="time" name="appointment_time" class="form-control" required>
            </div>
        </div>
        
        <div class="form-group">
            <label>Purpose *</label>
            <select name="purpose" class="form-control" required>
                <option value="">Select Purpose</option>
                <option value="General Checkup">General Checkup</option>
                <option value="Vaccination">Vaccination</option>
                <option value="Prenatal Checkup">Prenatal Checkup</option>
                <option value="Blood Pressure Monitoring">Blood Pressure Monitoring</option>
                <option value="Blood Sugar Monitoring">Blood Sugar Monitoring</option>
                <option value="Follow-up Consultation">Follow-up Consultation</option>
                <option value="Medical Certificate">Medical Certificate</option>
                <option value="Wound Dressing">Wound Dressing</option>
                <option value="Other">Other</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Additional notes..."></textarea>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Schedule Appointment</button>
            <a href="index.php" class="btn btn-warning">Cancel</a>
        </div>
    </form>
</div>

<?php 
$conn->close();
require_once '../includes/footer.php'; 
?>
