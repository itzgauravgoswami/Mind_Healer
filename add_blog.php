<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("Add blog failed: User not logged in.");
    header("Location: login.php?error=Please log in to add a blog.");
    exit;
}

// Debug: Log user info
error_log("Add blog attempt by user_id=" . $_SESSION['user_id'] . ", role=" . ($_SESSION['role'] ?? 'not set'));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $user_id = (int)$_SESSION['user_id'];

    // Validate input
    if (empty($title) || empty($content)) {
        header("Location: dashboard.php?error=Blog title and content are required.");
        exit;
    }

    // Prepare and execute query
    $stmt = $conn->prepare("INSERT INTO blogs (user_id, title, content) VALUES (?, ?, ?)");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        header("Location: dashboard.php?error=Database error occurred.");
        exit;
    }

    $stmt->bind_param("iss", $user_id, $title, $content);
    if ($stmt->execute()) {
        header("Location: dashboard.php?msg=Blog post added successfully.");
    } else {
        error_log("Execute failed: " . $stmt->error);
        header("Location: dashboard.php?error=Failed to add blog post: " . $stmt->error);
    }
    $stmt->close();
} else {
    header("Location: dashboard.php?error=Invalid request method.");
}
$conn->close();
?>