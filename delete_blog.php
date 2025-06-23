<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("Delete blog failed: User not logged in.");
    header("Location: login.php?error=Please log in to delete a blog.");
    exit;
}

// Check if request is POST and parameters are set
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['blog_id']) || !isset($_POST['csrf_token']) || !is_numeric($_POST['blog_id'])) {
    error_log("Delete blog failed: Invalid request.");
    header("Location: dashboard.php?error=Invalid request.");
    exit;
}

$blog_id = (int)$_POST['blog_id'];
$csrf_token = $_POST['csrf_token'];

// Debug: Log request details
error_log("Delete blog attempt: blog_id=$blog_id, user_id={$_SESSION['user_id']}, csrf_token=$csrf_token");

// Validate CSRF token
if (!isset($_SESSION['csrf_token']) || $csrf_token !== $_SESSION['csrf_token']) {
    error_log("Delete blog failed: Invalid CSRF token.");
    header("Location: dashboard.php?error=Invalid CSRF token.");
    exit;
}

// Verify blog ownership
$stmt = $conn->prepare("SELECT id FROM blogs WHERE id = ? AND user_id = ?");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    header("Location: dashboard.php?error=Database error occurred.");
    exit;
}
$stmt->bind_param("ii", $blog_id, $_SESSION['user_id']);
$stmt->execute();
$blog = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$blog) {
    error_log("Delete blog failed: Blog ID=$blog_id not found or user_id={$_SESSION['user_id']} not authorized.");
    header("Location: dashboard.php?error=You are not authorized to delete this blog or it does not exist.");
    exit;
}

// Delete the blog
$stmt = $conn->prepare("DELETE FROM blogs WHERE id = ? AND user_id = ?");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    header("Location: dashboard.php?error=Database error occurred.");
    exit;
}
$stmt->bind_param("ii", $blog_id, $_SESSION['user_id']);
if ($stmt->execute()) {
    unset($_SESSION['csrf_token']); // Clear used token
    header("Location: dashboard.php?msg=Blog post deleted successfully.");
} else {
    error_log("Execute failed: " . $stmt->error);
    header("Location: dashboard.php?error=Failed to delete blog post.");
}
$stmt->close();
$conn->close();
?>