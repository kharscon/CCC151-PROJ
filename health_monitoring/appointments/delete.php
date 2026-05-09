<?php
$pageTitle = "Edit Appointment - Barangay Health Center";
require_once('../includes/header.php');
$conn = getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$appointment = $conn->query("
    SELECT a.*, p.first_name, p.last_name 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.patient_id 
    WHERE a.appointment_id = $id
")->fetch_assoc();

if (!$appointment) {
    setAlert('danger', 'Appointment not found.');
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = sanitize($conn, $_POST['appointment_date']);
    $time = sanitize($conn, $_POST['appointment_time']);
    $purpose = sanitize($conn, $_POST['purpose']);
    $status = sanitize($conn, $_POST['status']);
    $notes = sanitize($conn, $_POST['notes']);
    
    $sql = "UPDATE appointments SET appointment_date = ?, appointment_time = ?, 
            purpose = ?, status = ?, notes = ? WHERE appointment_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $date, $time, $purpose, $status, $notes, $id);
    
    if ($stmt->execute()) {
        setAlert('success', 'Appointment updated successfully!');
        header('Location: index.php');
        exit;
    } else {
        setAlert('danger', 'Error updating appointment.');
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">✏️ Edit Appointment</h2>
        <a href="index.php" class="btn btn-warning">← Back to List</a>
    </div>
    
    <form method="POST">
        <div class="form-group">
            <label>Patient</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?>" readonly>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Date *</label>
                <input type="date" name="appointment_date" class="form-control" value="<?php echo $appointment['appointment_date']; ?>" required>
            </div>
            <div class="form-group">
                <label>Time *</label>
                <input type="time" name="appointment_time" class="form-control" value="<?php echo $appointment['appointment_time']; ?>" required>
            </div>
            <div class="form-group">
                <label>Status *</label>
                <select name="status" class="form-control" required>
                    <option value="Scheduled" <?php echo $appointment['status'] == 'Scheduled' ? 'selected' : ''; ?>>Scheduled</option>
                    <option value="Completed" <?php echo $appointment['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                    <option value="Cancelled" <?php echo $appointment['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    <option value="No Show" <?php echo $appointment['status'] == 'No Show' ? 'selected' : ''; ?>>No Show</option>
                </select>
            </div>
        </div>
        
        <div class="form-group">
            <label>Purpose *</label>
            <input type="text" name="purpose" class="form-control" value="<?php echo htmlspecialchars($appointment['purpose']); ?>" required>
        </div>
        
        <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" class="form-control" rows="3"><?php echo htmlspecialchars($appointment['notes']); ?></textarea>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Update Appointment</button>
            <a href="index.php" class="btn btn-warning">Cancel</a>
        </div>
    </form>
</div>

<?php 
$conn->close();
require_once '../includes/footer.php'; 
?>
