<?php
include 'includes/db_connect.php';
include 'includes/header.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$result = $conn->query("SELECT d.id, d.name, d.specialization, d.is_verified FROM doctors d JOIN users u ON d.user_id = u.id WHERE u.role = 'doctor'");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $stmt = $conn->prepare("UPDATE doctors SET is_verified = TRUE WHERE id = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $stmt->close();
    header("Location: admin.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Mind Healer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <section class="full-screen">
        <div class="container">
            <h2 class="text-3xl font-bold text-center mb-5">Admin Panel - Doctor Verification</h2>
            <div class="row">
                <?php while ($doctor = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow p-4">
                            <h3 class="text-xl font-semibold"><?php echo $doctor['name']; ?></h3>
                            <p><?php echo $doctor['specialization']; ?></p>
                            <span class="badge <?php echo $doctor['is_verified'] ? 'bg-success' : 'bg-warning'; ?>">
                                <?php echo $doctor['is_verified'] ? 'Verified' : 'Unverified'; ?>
                            </span>
                            <?php if (!$doctor['is_verified']): ?>
                                <form action="admin.php" method="POST">
                                    <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                                    <button type="submit" class="btn btn-primary mt-3">Verify Doctor</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>