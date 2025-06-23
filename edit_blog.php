<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    error_log("Edit blog failed: User not logged in.");
    header("Location: login.php?error=Please log in to edit a blog.");
    exit;
}

// Check if blog ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    error_log("Edit blog failed: Invalid blog ID.");
    header("Location: dashboard.php?error=Invalid blog ID.");
    exit;
}

$blog_id = (int)$_GET['id'];

// Fetch blog and verify ownership
$stmt = $conn->prepare("SELECT id, title, content FROM blogs WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $blog_id, $_SESSION['user_id']);
$stmt->execute();
$blog = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$blog) {
    error_log("Edit blog failed: Blog ID=$blog_id not found or user_id={$_SESSION['user_id']} not authorized.");
    header("Location: dashboard.php?error=You are not authorized to edit this blog or it does not exist.");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');

    // Validate input
    if (empty($title) || empty($content)) {
        $error = "Blog title and content are required.";
    } else {
        $stmt = $conn->prepare("UPDATE blogs SET title = ?, content = ? WHERE id = ? AND user_id = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            header("Location: dashboard.php?error=Database error occurred.");
            exit;
        }

        $stmt->bind_param("ssii", $title, $content, $blog_id, $_SESSION['user_id']);
        if ($stmt->execute()) {
            header("Location: dashboard.php?msg=Blog post updated successfully.");
        } else {
            error_log("Execute failed: " . $stmt->error);
            $error = "Failed to update blog post: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog - Mind Healer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .edit-container { height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(to right, #6b7280, #1f2937); }
        .edit-card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <section class="edit-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 edit-card">
                    <h2 class="text-2xl font-bold text-center mb-4">Edit Blog Post</h2>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form action="edit_blog.php?id=<?php echo $blog_id; ?>" method="POST">
                        <div class="mb-3">
                            <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($blog['title']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <textarea name="content" class="form-control" rows="10" required><?php echo htmlspecialchars($blog['content']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Blog</button>
                    </form>
                    <p class="text-center mt-3"><a href="dashboard.php" class="text-primary">Back to Dashboard</a></p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>