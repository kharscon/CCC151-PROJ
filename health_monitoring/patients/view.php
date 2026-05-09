<?php
$pageTitle = "View Patient - Barangay Health Center";
require_once('../includes/header.php');
$conn = getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$patient = $conn->query("SELECT * FROM patients WHERE patient_id = $id")->fetch_assoc();

if (!$patient) {
    setAlert('danger', 'Patient not found.');
    header('Location: index.php');
    exit;
}

// Calculate age
$dob = new DateTime($patient['date_of_birth']);
$now = new DateTime();
$age = $now->diff($dob)->y;

// Get appointments
$appointments = $conn->query("SELECT * FROM appointments WHERE patient_id = $id ORDER BY appointment_date DESC LIMIT 5");

// Get health records
$records = $conn->query("SELECT * FROM health_records WHERE patient_id = $id ORDER BY record_date DESC, record_time DESC LIMIT 5");
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">👤 Patient Profile</h2>
        <div>
            <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning">Edit</a>
            <a href="index.php" class="btn btn-primary">← Back to List</a>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <div>
            <h3 style="color: #2c5f2d; margin-bottom: 1rem;">Personal Information</h3>
            <table>
                <tr>
                    <th style="width: 40%;">Full Name:</th>
                    <td><strong><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></strong></td>
                </tr>
                <tr>
                    <th>Date of Birth:</th>
                    <td><?php echo date('F d, Y', strtotime($patient['date_of_birth'])); ?> (<?php echo $age; ?> years old)</td>
                </tr>
                <tr>
                    <th>Gender:</th>
                    <td><?php echo $patient['gender']; ?></td>
                </tr>
                <tr>
                    <th>Category:</th>
                    <td>
                        <span class="badge badge-<?php 
                            echo $patient['patient_category'] == 'Senior' ? 'warning' : 
                                ($patient['patient_category'] == 'Child' ? 'info' : 'success'); 
                        ?>">
                            <?php echo $patient['patient_category']; ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Contact Number:</th>
                    <td><?php echo htmlspecialchars($patient['contact_number'] ?: 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>Address:</th>
                    <td><?php echo htmlspecialchars($patient['address'] ?: 'N/A'); ?></td>
                </tr>
            </table>
        </div>
        
        <div>
            <h3 style="color: #2c5f2d; margin-bottom: 1rem;">Medical Information</h3>
            <table>
                <tr>
                    <th style="width: 40%;">Blood Type:</th>
                    <td><?php echo $patient['blood_type']; ?></td>
                </tr>
                <tr>
                    <th>Chronic Illness:</th>
                    <td>
                        <?php if ($patient['has_chronic_illness']): ?>
                            <span class="badge badge-danger">Yes</span>
                            <br><small><?php echo htmlspecialchars($patient['chronic_illness_details']); ?></small>
                        <?php else: ?>
                            <span class="badge badge-success">No</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Allergies:</th>
                    <td><?php echo htmlspecialchars($patient['allergies'] ?: 'None reported'); ?></td>
                </tr>
                <tr>
                    <th>Emergency Contact:</th>
                    <td>
                        <?php echo htmlspecialchars($patient['emergency_contact_name'] ?: 'N/A'); ?>
                        <?php if ($patient['emergency_contact_number']): ?>
                            <br><small><?php echo htmlspecialchars($patient['emergency_contact_number']); ?></small>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">📅 Recent Appointments</h3>
            <a href="/barangay_health/appointments/add.php?patient_id=<?php echo $id; ?>" class="btn btn-primary btn-sm">+ New</a>
        </div>
        <?php if ($appointments->num_rows > 0): ?>
            <table>
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
                        <td><?php echo htmlspecialchars($apt['purpose']); ?></td>
                        <td>
                            <span class="badge badge-<?php 
                                echo $apt['status'] == 'Completed' ? 'success' : 
                                    ($apt['status'] == 'Cancelled' ? 'danger' : 'warning'); 
                            ?>">
                                <?php echo $apt['status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center" style="padding: 1rem; color: #666;">No appointments yet.</p>
        <?php endif; ?>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">🩺 Recent Health Records</h3>
            <a href="/barangay_health/health_records/add.php?patient_id=<?php echo $id; ?>" class="btn btn-primary btn-sm">+ New</a>
        </div>
        <?php if ($records->num_rows > 0): ?>
            <table>
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
                        <td><?php echo $rec['blood_pressure_systolic'] . '/' . $rec['blood_pressure_diastolic']; ?></td>
                        <td><?php echo $rec['temperature']; ?>°C</td>
                        <td><?php echo htmlspecialchars(substr($rec['chief_complaint'], 0, 30)); ?>...</td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center" style="padding: 1rem; color: #666;">No health records yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php 
$conn->close();
require_once '../includes/footer.php'; 
?>
