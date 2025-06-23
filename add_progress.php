<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=Please log in to add progress.");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $note = trim($_POST['note'] ?? '');
    $user_id = $_SESSION['user_id'];

    // Validate input
    if (empty($note)) {
        header("Location: dashboard.php?error=Progress note cannot be empty.");
        exit;
    }

    // Prepare and execute query
    $stmt = $conn->prepare("INSERT INTO progress (user_id, note) VALUES (?, ?)");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        header("Location: dashboard.php?error=Database error occurred.");
        exit;
    }

    $stmt->bind_param("is", $user_id, $note);
    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=Progress note added successfully.");
    } else {
        error_log("Execute failed: " . $stmt->error);
        header("Location: dashboard.php?error=Failed to add progress note: " . $stmt->error);
    }
    $stmt->close();
} else {
    header("Location: dashboard.php?error=Invalid request method.");
}
$conn->close();
?>