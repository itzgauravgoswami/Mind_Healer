<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'includes/db_connect.php';

// Check if blog ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: blog.php?error=Invalid blog ID.");
    exit;
}

$blog_id = $_GET['id'];

// Fetch blog post
$stmt = $conn->prepare("SELECT b.id, b.title, b.content, b.created_at, u.username FROM blogs b JOIN users u ON b.user_id = u.id WHERE b.id = ?");
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$blog = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$blog) {
    header("Location: blog.php?error=Blog not found.");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($blog['title']); ?> - Sleep Talks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .blog-container { padding: 2rem; background: #f9fafb; min-height: 100vh; }
        .blog-card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <section class="blog-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 blog-card">
                    <h1 class="text-3xl font-bold mb-3"><?php echo htmlspecialchars($blog['title']); ?></h1>
                    <p class="text-muted mb-3">Posted by <?php echo htmlspecialchars($blog['username']); ?> on <?php echo htmlspecialchars($blog['created_at']); ?></p>
                    <p><?php echo nl2br(htmlspecialchars($blog['content'])); ?></p>
                    <a href="blog.php" class="btn btn-primary mt-3">Back to Blogs</a>
                </div>
            </div>
        </div>
    </section>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>