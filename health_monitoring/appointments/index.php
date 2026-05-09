<?php
$pageTitle = "Appointments - Barangay Health Center";
require_once('../includes/header.php');
$conn = getConnection();

$date_filter = isset($_GET['date']) ? sanitize($conn, $_GET['date']) : date('Y-m-d');
$status_filter = isset($_GET['status']) ? sanitize($conn, $_GET['status']) : '';

$where = "WHERE 1=1";
if ($date_filter) {
    $where .= " AND a.appointment_date = '$date_filter'";
}
if ($status_filter) {
    $where .= " AND a.status = '$status_filter'";
}

$appointments = $conn->query("
    SELECT a.*, p.first_name, p.last_name, p.contact_number 
    FROM appointments a 
    JOIN patients p ON a.patient_id = p.patient_id 
    $where 
    ORDER BY a.appointment_date, a.appointment_time
");
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">📅 Appointments</h2>
        <a href="add.php" class="btn btn-primary">+ Schedule Appointment</a>
    </div>
    
    <form method="GET" class="search-box">
        <input type="date" name="date" class="form-control" style="max-width: 200px;" value="<?php echo $date_filter; ?>">
        <select name="status" class="form-control" style="max-width: 200px;">
            <option value="">All Status</option>
            <option value="Scheduled" <?php echo $status_filter == 'Scheduled' ? 'selected' : ''; ?>>Scheduled</option>
            <option value="Completed" <?php echo $status_filter == 'Completed' ? 'selected' : ''; ?>>Completed</option>
            <option value="Cancelled" <?php echo $status_filter == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            <option value="No Show" <?php echo $status_filter == 'No Show' ? 'selected' : ''; ?>>No Show</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="index.php" class="btn btn-warning">Today</a>
    </form>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Patient</th>
                    <th>Contact</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($appointments->num_rows > 0): ?>
                    <?php while ($row = $appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($row['appointment_date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($row['appointment_time'])); ?></td>
                        <td>
                            <a href="/barangay_health/patients/view.php?id=<?php echo $row['patient_id']; ?>">
                                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                        <td><?php echo htmlspecialchars($row['purpose']); ?></td>
                        <td>
                            <span class="badge badge-<?php 
                                echo $row['status'] == 'Completed' ? 'success' : 
                                    ($row['status'] == 'Cancelled' ? 'danger' : 
                                    ($row['status'] == 'No Show' ? 'warning' : 'info')); 
                            ?>">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td class="action-buttons">
                            <a href="edit.php?id=<?php echo $row['appointment_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete.php?id=<?php echo $row['appointment_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this appointment?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No appointments found for this date.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php 
$conn->close();
require_once '../includes/footer.php'; 
?>
