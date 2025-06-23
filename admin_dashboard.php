<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $name = trim($_POST['name'] ?? '');

    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($name)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            $error = "Email already registered.";
        }
        $stmt->close();

        // Handle image upload
        $image_path = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed_types = ['image/jpeg', 'image/png'];
            $max_size = 2 * 1024 * 1024; // 2MB
            $file_type = mime_content_type($_FILES['image']['tmp_name']);
            $file_size = $_FILES['image']['size'];

            if (!in_array($file_type, $allowed_types)) {
                $error = "Only JPEG or PNG images are allowed.";
            } elseif ($file_size > $max_size) {
                $error = "Image size exceeds 2MB.";
            } else {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $filename = uniqid('doctor_') . '.' . $ext;
                $upload_dir = 'uploads/doctors/';
                $upload_path = $upload_dir . $filename;

                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_path = $upload_path;
                } else {
                    $error = "Failed to upload image.";
                }
            }
        }

        // Proceed if no errors
        if (!isset($error)) {
            // Insert into users table
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'doctor')");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($stmt->execute()) {
                $user_id = $conn->insert_id;

                // Insert into doctors table
                $stmt = $conn->prepare("INSERT INTO doctors (user_id, name, is_verified, image) VALUES (?, ?, 0, ?)");
                $stmt->bind_param("iss", $user_id, $name, $image_path);
                if ($stmt->execute()) {
                    header("Location: login.php?msg=Doctor registration successful. Await admin verification.");
                } else {
                    error_log("Doctor insert failed: " . $stmt->error);
                    $error = "Failed to register doctor.";
                }
                $stmt->close();
            } else {
                error_log("User insert failed: " . $stmt->error);
                $error = "Failed to register user.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Signup - Sleep Talks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .signup-container { height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(to right, #6b7280, #1f2937); }
        .signup-card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <section class="signup-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 signup-card">
                    <h2 class="text-2xl font-bold text-center mb-4">Doctor Signup</h2>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form action="doctor_signup.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Profile Image (JPEG/PNG, Max 2MB)</label>
                            <input type="file" name="image" class="form-control" accept="image/jpeg,image/png">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                    </form>
                    <p class="text-center mt-3"><a href="login.php" class="text-primary">Back to Login</a></p>
                </div>
            </div>
        </div>
    </section>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>