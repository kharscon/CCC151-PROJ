<?php
$pageTitle = "Health Records - Barangay Health Center";
require_once('../includes/header.php');
$conn = getConnection();

$search = isset($_GET['search']) ? sanitize($conn, $_GET['search']) : '';
$date_from = isset($_GET['date_from']) ? sanitize($conn, $_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? sanitize($conn, $_GET['date_to']) : '';

$where = "WHERE 1=1";
if ($search) {
    $where .= " AND (p.first_name LIKE '%$search%' OR p.last_name LIKE '%$search%')";
}
if ($date_from) {
    $where .= " AND h.record_date >= '$date_from'";
}
if ($date_to) {
    $where .= " AND h.record_date <= '$date_to'";
}

$records = $conn->query("
    SELECT h.*, p.first_name, p.last_name 
    FROM health_records h 
    JOIN patients p ON h.patient_id = p.patient_id 
    $where 
    ORDER BY h.record_date DESC, h.record_time DESC
    LIMIT 100
");
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">🩺 Health Records</h2>
        <a href="add.php" class="btn btn-primary">+ Add Health Record</a>
    </div>
    
    <form method="GET" class="search-box">
        <input type="text" name="search" class="form-control" placeholder="Search patient name..." value="<?php echo htmlspecialchars($search); ?>">
        <input type="date" name="date_from" class="form-control" style="max-width: 150px;" value="<?php echo $date_from; ?>" placeholder="From">
        <input type="date" name="date_to" class="form-control" style="max-width: 150px;" value="<?php echo $date_to; ?>" placeholder="To">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="index.php" class="btn btn-warning">Clear</a>
    </form>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Date/Time</th>
                    <th>Patient</th>
                    <th>BP</th>
                    <th>Temp</th>
                    <th>Pulse</th>
                    <th>Chief Complaint</th>
                    <th>Staff</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($records->num_rows > 0): ?>
                    <?php while ($row = $records->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?php echo date('M d, Y', strtotime($row['record_date'])); ?>
                            <br><small><?php echo date('h:i A', strtotime($row['record_time'])); ?></small>
                        </td>
                        <td>
                            <a href="/barangay_health/patients/view.php?id=<?php echo $row['patient_id']; ?>">
                                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                            </a>
                        </td>
                        <td>
                            <?php if ($row['blood_pressure_systolic']): ?>
                                <?php echo $row['blood_pressure_systolic'] . '/' . $row['blood_pressure_diastolic']; ?> mmHg
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['temperature'] ? $row['temperature'] . '°C' : '-'; ?></td>
                        <td><?php echo $row['pulse_rate'] ? $row['pulse_rate'] . ' bpm' : '-'; ?></td>
                        <td><?php echo htmlspecialchars(substr($row['chief_complaint'], 0, 40)); ?><?php echo strlen($row['chief_complaint']) > 40 ? '...' : ''; ?></td>
                        <td><?php echo htmlspecialchars($row['attending_staff'] ?: '-'); ?></td>
                        <td>
                            <a href="view.php?id=<?php echo $row['record_id']; ?>" class="btn btn-info btn-sm">View</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No health records found.</td>
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
