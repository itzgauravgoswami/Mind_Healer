<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'includes/db_connect.php';

$blogs = $conn->query("SELECT b.id, b.title, b.content, b.created_at, u.username FROM blogs b JOIN users u ON b.user_id = u.id ORDER BY b.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogs - Mind Healer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .blog-container { padding: 2rem; background: #f9fafb; min-height: 100vh; }
        .blog-card { background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <section class="blog-container">
        <div class="container">
            <h2 class="text-3xl font-bold text-center mb-5">Mental Health Blogs</h2>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            <?php while ($blog = $blogs->fetch_assoc()): ?>
                <div class="blog-card">
                    <h3><a href="view_blog.php?id=<?php echo $blog['id']; ?>" class="text-primary"><?php echo htmlspecialchars($blog['title']); ?></a></h3>
                    <p><?php echo htmlspecialchars(substr($blog['content'], 0, 100)); ?>...</p>
                    <p class="text-muted">Posted by: <?php echo htmlspecialchars($blog['username']); ?> on <?php echo htmlspecialchars($blog['created_at']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    </section>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>