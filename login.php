<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'includes/db_connect.php';
session_start();

$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validate input
    if (empty($email) || empty($password)) {
        $login_error = "Email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $login_error = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
        if (!$stmt) {
            $login_error = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("s", $email);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($user = $result->fetch_assoc()) {
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['role'] = $user['role'];
                        header("Location: dashboard.php");
                        exit;
                    } else {
                        $login_error = "Invalid password.";
                    }
                } else {
                    $login_error = "User not found.";
                }
            } else {
                $login_error = "Login query failed: " . $stmt->error;
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
    <title>Login - Mind Healer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .login-container { height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(to right, #6b7280, #1f2937); }
        .login-card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <section class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 login-card">
                    <h2 class="text-2xl font-bold text-center mb-4">Login</h2>
                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <?php if ($login_error): ?>
                            <div class="alert alert-danger"><?php echo $login_error; ?></div>
                        <?php endif; ?>
                        <button type="submit" class="btn btn-primary w-100">Login</button>
                    </form>
                    <p class="text-center mt-3">Don't have an account? <a href="signup.php" class="text-primary">Sign Up</a></p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>