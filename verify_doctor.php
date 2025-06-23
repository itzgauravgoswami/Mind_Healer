<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'includes/db_connect.php';

if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php?error=Unauthorized access.");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doctor_id'])) {
    $doctor_id = (int)$_POST['doctor_id'];
    $stmt = $conn->prepare("UPDATE doctors SET is_verified = 1 WHERE id = ?");
    $stmt->bind_param("i", $doctor_id);
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?msg=Doctor verified successfully.");
    } else {
        error_log("Doctor verification failed: " . $stmt->error);
        header("Location: admin_dashboard.php?error=Failed to verify doctor.");
    }
    $stmt->close();
}
$conn->close();
?>