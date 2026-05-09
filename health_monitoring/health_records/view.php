<?php
$pageTitle = "View Health Record - Barangay Health Center";
require_once('../includes/header.php'); 

$conn = getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$record = $conn->query("
    SELECT h.*, p.first_name, p.last_name, p.date_of_birth, p.gender
    FROM health_records h 
    JOIN patients p ON h.patient_id = p.patient_id 
    WHERE h.record_id = $id
")->fetch_assoc();

if (!$record) {
    setAlert('danger', 'Record not found.');
    header('Location: index.php');
    exit;
}

$dob = new DateTime($record['date_of_birth']);
$now = new DateTime();
$age = $now->diff($dob)->y;
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">🩺 Health Record Details</h2>
        <a href="index.php" class="btn btn-warning">← Back to List</a>
    </div>
    
    <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
        <strong>Patient:</strong> 
        <a href="/barangay_health/patients/view.php?id=<?php echo $record['patient_id']; ?>">
            <?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?>
        </a>
        | <strong>Age:</strong> <?php echo $age; ?> years old
        | <strong>Gender:</strong> <?php echo $record['gender']; ?>
        | <strong>Date:</strong> <?php echo date('F d, Y', strtotime($record['record_date'])); ?>
        at <?php echo date('h:i A', strtotime($record['record_time'])); ?>
    </div>
    
    <h3 style="color: #2c5f2d; margin-bottom: 1rem;">Vital Signs</h3>
    <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));">
        <div class="stat-card">
            <div class="stat-icon">🌡️</div>
            <div class="stat-number"><?php echo $record['temperature'] ?: '-'; ?></div>
            <div class="stat-label">Temperature (°C)</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💓</div>
            <div class="stat-number"><?php echo $record['blood_pressure_systolic'] ? $record['blood_pressure_systolic'] . '/' . $record['blood_pressure_diastolic'] : '-'; ?></div>
            <div class="stat-label">Blood Pressure</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💗</div>
            <div class="stat-number"><?php echo $record['pulse_rate'] ?: '-'; ?></div>
            <div class="stat-label">Pulse Rate (bpm)</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🫁</div>
            <div class="stat-number"><?php echo $record['respiratory_rate'] ?: '-'; ?></div>
            <div class="stat-label">Respiratory Rate</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🩸</div>
            <div class="stat-number"><?php echo $record['oxygen_saturation'] ? $record['oxygen_saturation'] . '%' : '-'; ?></div>
            <div class="stat-label">O₂ Saturation</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⚖️</div>
            <div class="stat-number"><?php echo $record['weight'] ?: '-'; ?></div>
            <div class="stat-label">Weight (kg)</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📏</div>
            <div class="stat-number"><?php echo $record['height'] ?: '-'; ?></div>
            <div class="stat-label">Height (cm)</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🍬</div>
            <div class="stat-number"><?php echo $record['blood_sugar'] ?: '-'; ?></div>
            <div class="stat-label">Blood Sugar (mg/dL)</div>
        </div>
    </div>
    
    <h3 style="color: #2c5f2d; margin: 1.5rem 0 1rem;">Clinical Notes</h3>
    <table>
        <tr>
            <th style="width: 200px;">Chief Complaint:</th>
            <td><?php echo nl2br(htmlspecialchars($record['chief_complaint'] ?: 'None recorded')); ?></td>
        </tr>
        <tr>
            <th>Diagnosis:</th>
            <td><?php echo nl2br(htmlspecialchars($record['diagnosis'] ?: 'None recorded')); ?></td>
        </tr>
        <tr>
            <th>Treatment Given:</th>
            <td><?php echo nl2br(htmlspecialchars($record['treatment_given'] ?: 'None recorded')); ?></td>
        </tr>
        <tr>
            <th>Attending Staff:</th>
            <td><?php echo htmlspecialchars($record['attending_staff'] ?: 'Not specified'); ?></td>
        </tr>
        <tr>
            <th>Additional Notes:</th>
            <td><?php echo nl2br(htmlspecialchars($record['notes'] ?: 'None')); ?></td>
        </tr>
    </table>
</div>

<?php 
$conn->close();
require_once '../includes/footer.php'; 
?>
