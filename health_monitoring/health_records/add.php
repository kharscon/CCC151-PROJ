<?php
$pageTitle = "Add Health Record - Barangay Health Center";
require_once('../includes/header.php');
$conn = getConnection();

$patients = $conn->query("SELECT patient_id, first_name, last_name FROM patients ORDER BY last_name, first_name");
$selected_patient = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_id = (int)$_POST['patient_id'];
    $record_date = sanitize($conn, $_POST['record_date']);
    $record_time = sanitize($conn, $_POST['record_time']);
    $temperature = $_POST['temperature'] ? (float)$_POST['temperature'] : null;
    $bp_systolic = $_POST['blood_pressure_systolic'] ? (int)$_POST['blood_pressure_systolic'] : null;
    $bp_diastolic = $_POST['blood_pressure_diastolic'] ? (int)$_POST['blood_pressure_diastolic'] : null;
    $pulse = $_POST['pulse_rate'] ? (int)$_POST['pulse_rate'] : null;
    $respiratory = $_POST['respiratory_rate'] ? (int)$_POST['respiratory_rate'] : null;
    $weight = $_POST['weight'] ? (float)$_POST['weight'] : null;
    $height = $_POST['height'] ? (float)$_POST['height'] : null;
    $blood_sugar = $_POST['blood_sugar'] ? (float)$_POST['blood_sugar'] : null;
    $oxygen = $_POST['oxygen_saturation'] ? (int)$_POST['oxygen_saturation'] : null;
    $complaint = sanitize($conn, $_POST['chief_complaint']);
    $diagnosis = sanitize($conn, $_POST['diagnosis']);
    $treatment = sanitize($conn, $_POST['treatment_given']);
    $staff = sanitize($conn, $_POST['attending_staff']);
    $notes = sanitize($conn, $_POST['notes']);
    
    $sql = "INSERT INTO health_records (patient_id, record_date, record_time, temperature, 
            blood_pressure_systolic, blood_pressure_diastolic, pulse_rate, respiratory_rate,
            weight, height, blood_sugar, oxygen_saturation, chief_complaint, diagnosis,
            treatment_given, attending_staff, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issdiiiiiddissss", $patient_id, $record_date, $record_time, $temperature,
                      $bp_systolic, $bp_diastolic, $pulse, $respiratory, $weight, $height,
                      $blood_sugar, $oxygen, $complaint, $diagnosis, $treatment, $staff, $notes);
    
    if ($stmt->execute()) {
        setAlert('success', 'Health record added successfully!');
        header('Location: index.php');
        exit;
    } else {
        setAlert('danger', 'Error adding health record: ' . $conn->error);
    }
}
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">🩺 Add Health Record</h2>
        <a href="index.php" class="btn btn-warning">← Back to List</a>
    </div>
    
    <form method="POST">
        <h3 style="margin-bottom: 1rem; color: #2c5f2d;">Patient & Visit Info</h3>
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
            <div class="form-group">
                <label>Date *</label>
                <input type="date" name="record_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label>Time *</label>
                <input type="time" name="record_time" class="form-control" value="<?php echo date('H:i'); ?>" required>
            </div>
        </div>
        
        <h3 style="margin: 1.5rem 0 1rem; color: #2c5f2d;">Vital Signs</h3>
        <div class="form-row">
            <div class="form-group">
                <label>Temperature (°C)</label>
                <input type="number" name="temperature" class="form-control" step="0.1" min="30" max="45" placeholder="e.g., 36.5">
            </div>
            <div class="form-group">
                <label>Blood Pressure (Systolic)</label>
                <input type="number" name="blood_pressure_systolic" class="form-control" min="60" max="250" placeholder="e.g., 120">
            </div>
            <div class="form-group">
                <label>Blood Pressure (Diastolic)</label>
                <input type="number" name="blood_pressure_diastolic" class="form-control" min="40" max="150" placeholder="e.g., 80">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Pulse Rate (bpm)</label>
                <input type="number" name="pulse_rate" class="form-control" min="30" max="200" placeholder="e.g., 72">
            </div>
            <div class="form-group">
                <label>Respiratory Rate (breaths/min)</label>
                <input type="number" name="respiratory_rate" class="form-control" min="8" max="40" placeholder="e.g., 16">
            </div>
            <div class="form-group">
                <label>Oxygen Saturation (%)</label>
                <input type="number" name="oxygen_saturation" class="form-control" min="70" max="100" placeholder="e.g., 98">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Weight (kg)</label>
                <input type="number" name="weight" class="form-control" step="0.1" min="1" max="300" placeholder="e.g., 65.5">
            </div>
            <div class="form-group">
                <label>Height (cm)</label>
                <input type="number" name="height" class="form-control" step="0.1" min="30" max="250" placeholder="e.g., 165">
            </div>
            <div class="form-group">
                <label>Blood Sugar (mg/dL)</label>
                <input type="number" name="blood_sugar" class="form-control" step="0.1" min="20" max="600" placeholder="e.g., 100">
            </div>
        </div>
        
        <h3 style="margin: 1.5rem 0 1rem; color: #2c5f2d;">Clinical Notes</h3>
        <div class="form-group">
            <label>Chief Complaint</label>
            <textarea name="chief_complaint" class="form-control" rows="2" placeholder="What is the patient's main concern?"></textarea>
        </div>
        
        <div class="form-group">
            <label>Diagnosis</label>
            <textarea name="diagnosis" class="form-control" rows="2" placeholder="Assessment/Diagnosis"></textarea>
        </div>
        
        <div class="form-group">
            <label>Treatment Given</label>
            <textarea name="treatment_given" class="form-control" rows="2" placeholder="Medications, procedures, advice given"></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label>Attending Staff</label>
                <input type="text" name="attending_staff" class="form-control" placeholder="Name of health worker">
            </div>
        </div>
        
        <div class="form-group">
            <label>Additional Notes</label>
            <textarea name="notes" class="form-control" rows="2" placeholder="Any other observations"></textarea>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Save Health Record</button>
            <a href="index.php" class="btn btn-warning">Cancel</a>
        </div>
    </form>
</div>

<?php 
$conn->close();
require_once '../includes/footer.php'; 
?>
