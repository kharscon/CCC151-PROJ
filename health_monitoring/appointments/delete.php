<?php
session_start();
require_once('../includes/db.php');

$conn = getConnection();
$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Invalid appointment ID.'];
    header('Location: index.php');
    exit;
}

// Verify appointment exists
$check = $conn->query("SELECT appointment_id FROM appointments WHERE appointment_id = $id");

if ($check && $check->num_rows > 0) {
    $stmt = $conn->prepare("DELETE FROM appointments WHERE appointment_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['alert'] = ['type' => 'success', 'message' => 'Appointment deleted successfully.'];
    } else {
        $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Error deleting appointment: ' . $conn->error];
    }
} else {
    $_SESSION['alert'] = ['type' => 'danger', 'message' => 'Appointment not found.'];
}

$conn->close();
header('Location: index.php');
exit;
?>