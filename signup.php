<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/db_connect.php';
session_start();

$signup_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $is_doctor = isset($_POST['is_doctor']) ? 'doctor' : 'user';

    // Validate input
    if (empty($username) || empty($email) || empty($_POST['password'])) {
        $signup_error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signup_error = "Invalid email format.";
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        if (!$stmt) {
            $signup_error = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $signup_error = "Username or email already exists.";
            } else {
                // Insert user
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $username, $email, $password, $is_doctor);
                if ($stmt->execute()) {
                    $user_id = $conn->insert_id;
                    if ($is_doctor === 'doctor') {
                        $name = trim($_POST['name']);
                        $specialization = trim($_POST['specialization']);
                        if (empty($name) || empty($specialization)) {
                            $signup_error = "Name and specialization are required for doctors.";
                        } else {
                            $stmt = $conn->prepare("INSERT INTO doctors (user_id, name, specialization) VALUES (?, ?, ?)");
                            $stmt->bind_param("iss", $user_id, $name, $specialization);
                            if (!$stmt->execute()) {
                                $signup_error = "Failed to register doctor: " . $stmt->error;
                            }
                        }
                    }
                    if (empty($signup_error)) {
                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['role'] = $is_doctor;
                        header("Location: dashboard.php");
                        exit;
                    }
                } else {
                    $signup_error = "Signup failed: " . $stmt->error;
                }
                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Sleep Talks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <h2 class="text-2xl font-bold text-center mb-4">Sign Up</h2>
                    <form action="signup.php" method="POST">
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="is_doctor" class="form-check-input" id="isDoctor">
                            <label class="form-check-label" for="isDoctor">I am a doctor</label>
                        </div>
                        <div class="doctor-fields d-none">
                            <div class="mb-3">
                                <input type="text" name="name" class="form-control" placeholder="Full Name">
                            </div>
                            <div class="mb-3">
                                <input type="text" name="specialization" class="form-control" placeholder="Specialization">
                            </div>
                        </div>
                        <?php if ($signup_error): ?>
                            <div class="alert alert-danger"><?php echo $signup_error; ?></div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary w-100">Sign Up</button>
                    </form>
                    <p class="text-center mt-3">Already have an account? <a href="login.php" class="text-primary">Login</a></p>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('isDoctor').addEventListener('change', function() {
            document.querySelector('.doctor-fields').classList.toggle('d-none', !this.checked);
        });
    </script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>