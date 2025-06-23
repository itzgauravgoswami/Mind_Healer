<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'includes/db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Debug: Log session data
error_log("Session Data: user_id=" . ($_SESSION['user_id'] ?? 'not set') . ", role=" . ($_SESSION['role'] ?? 'not set'));

$is_doctor = $conn->query("SELECT id, is_verified FROM doctors WHERE user_id = " . (int)$_SESSION['user_id'])->fetch_assoc();
$is_admin = $_SESSION['role'] === 'admin';

// Fetch user data for debugging
$user_data = $conn->query("SELECT username, role FROM users WHERE id = " . (int)$_SESSION['user_id'])->fetch_assoc();
if (!$user_data) {
    error_log("User not found in database for user_id=" . $_SESSION['user_id']);
    header("Location: login.php?error=User not found.");
    exit;
}

// Fetch dashboard data
$appointments = $conn->query("SELECT a.id, a.appointment_date, a.status, d.name FROM appointments a JOIN doctors d ON a.doctor_id = d.id WHERE a.user_id = " . (int)$_SESSION['user_id']);
$progress = $conn->query("SELECT note, created_at FROM progress WHERE user_id = " . (int)$_SESSION['user_id']);
$tests = $conn->query("SELECT score, created_at FROM depression_tests WHERE user_id = " . (int)$_SESSION['user_id']);
$blogs = $conn->query("SELECT id, title, content, created_at FROM blogs WHERE user_id = " . (int)$_SESSION['user_id']);

if ($is_doctor) {
    $doctor_appointments = $conn->query("SELECT a.id, a.appointment_date, a.status, u.username FROM appointments a JOIN users u ON a.user_id = u.id WHERE a.doctor_id = " . (int)$is_doctor['id']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mind Healer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .blog-card { transition: background-color 0.3s ease, transform 0.3s ease; }
        .blog-card:hover { background-color: #f8f9fa; transform: scale(1.02); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .btn:hover { opacity: 0.9; }
        a:hover { text-decoration: underline; color: #0d6efd; }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    <section class="full-screen">
        <div class="container">
            <h2 class="text-3xl font-bold text-center mb-5">Your Dashboard</h2>
            <?php if ($is_doctor && !$is_doctor['is_verified']): ?>
                <div class="alert alert-warning">Your account is unverified. Please wait for admin approval.</div>
            <?php endif; ?>
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
            <?php endif; ?>
            <p class="text-muted text-center">Logged in as: <?php echo htmlspecialchars($user_data['username']) . " (" . htmlspecialchars($user_data['role']) . ")"; ?></p>
            <div class="row">
                <!-- Appointments -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow p-4">
                        <h3 class="text-xl font-semibold">Your Appointments</h3>
                        <?php while ($appointment = $appointments->fetch_assoc()): ?>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <p>Doctor: <?php echo htmlspecialchars($appointment['name']); ?></p>
                                    <p>Date: <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
                                    <p>Status: <?php echo htmlspecialchars($appointment['status']); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <!-- Progress -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow p-4">
                        <h3 class="text-xl font-semibold">Your Progress</h3>
                        <?php while ($note = $progress->fetch_assoc()): ?>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <p><?php echo htmlspecialchars($note['note']); ?></p>
                                    <p>Date: <?php echo htmlspecialchars($note['created_at']); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                        <form action="add_progress.php" method="POST">
                            <textarea name="note" class="form-control mb-2" placeholder="Add a progress note" required></textarea>
                            <button type="submit" class="btn btn-primary">Add Note</button>
                        </form>
                    </div>
                </div>
                <!-- Depression Test Results -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow p-4">
                        <h3 class="text-xl font-semibold">Depression Test Results</h3>
                        <?php while ($test = $tests->fetch_assoc()): ?>
                            <div class="card mb-2">
                                <div class="card-body">
                                    <p>Score: <?php echo htmlspecialchars($test['score']); ?>/60</p>
                                    <p>Date: <?php echo htmlspecialchars($test['created_at']); ?></p>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <!-- Blogs (for all users) -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow p-4">
                        <h3 class="text-xl font-semibold">Your Blog Posts</h3>
                        <?php if ($blogs->num_rows === 0): ?>
                            <p>No blog posts yet.</p>
                        <?php else: ?>
                            <?php while ($blog = $blogs->fetch_assoc()): ?>
                                <div class="card mb-2 blog-card">
                                    <div class="card-body">
                                        <h4><a href="view_blog.php?id=<?php echo $blog['id']; ?>" class="text-primary"><?php echo htmlspecialchars($blog['title']); ?></a></h4>
                                        <p><?php echo htmlspecialchars(substr($blog['content'], 0, 100)); ?>...</p>
                                        <p>Date: <?php echo htmlspecialchars($blog['created_at']); ?></p>
                                        <a href="edit_blog.php?id=<?php echo $blog['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <button type="button" class="btn btn-sm btn-danger delete-blog-btn" data-blog-id="<?php echo $blog['id']; ?>" data-blog-title="<?php echo htmlspecialchars($blog['title']); ?>">Delete</button>
                                        <!-- Hidden Delete Form -->
                                        <form action="delete_blog.php" method="POST" id="deleteForm<?php echo $blog['id']; ?>" style="display: none;">
                                            <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); ?>">
                                        </form>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        <h3 class="text-xl font-semibold mt-4">Add a Blog Post</h3>
                        <form action="add_blog.php" method="POST">
                            <div class="mb-3">
                                <input type="text" name="title" class="form-control" placeholder="Blog Title" required>
                            </div>
                            <div class="mb-3">
                                <textarea name="content" class="form-control" placeholder="Blog Content" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Blog</button>
                        </form>
                    </div>
                </div>
                <!-- Doctor's Appointments -->
                <?php if ($is_doctor): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow p-4">
                            <h3 class="text-xl font-semibold">Manage Appointments</h3>
                            <?php while ($appointment = $doctor_appointments->fetch_assoc()): ?>
                                <div class="card mb-2">
                                    <div class="card-body">
                                        <p>Patient: <?php echo htmlspecialchars($appointment['username']); ?></p>
                                        <p>Date: <?php echo htmlspecialchars($appointment['appointment_date']); ?></p>
                                        <p>Status: <?php echo htmlspecialchars($appointment['status']); ?></p>
                                        <form action="update_appointment.php" method="POST">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                            <select name="status" class="form-select mb-2">
                                                <option value="pending">Pending</option>
                                                <option value="approved">Approved</option>
                                                <option value="rejected">Rejected</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </form>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Debug: Log page load
        console.log('Dashboard loaded');

        // Handle delete blog confirmation
        document.querySelectorAll('.delete-blog-btn').forEach(button => {
            button.addEventListener('click', function() {
                const blogId = this.getAttribute('data-blog-id');
                const blogTitle = this.getAttribute('data-blog-title');
                const confirmMessage = `Are you sure you want to delete "${blogTitle}"? This action cannot be undone.`;
                
                if (confirm(confirmMessage)) {
                    console.log(`Delete confirmed for blog ID: ${blogId}`);
                    document.getElementById(`deleteForm${blogId}`).submit();
                } else {
                    console.log(`Delete cancelled for blog ID: ${blogId}`);
                }
            });
        });
    </script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>