<?php
require_once(__DIR__ . '/../barangay_health/config/database.php');

session_start();

$conn = getConnection();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    $stmt = $conn->prepare("DELETE FROM patients WHERE patient_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        setAlert('success', 'Patient deleted successfully.');
    } else {
        setAlert('danger', 'Error deleting patient.');
    }
}

$conn->close();
header('Location: index.php');
exit;
?>
