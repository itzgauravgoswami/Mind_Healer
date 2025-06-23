<?php
include 'includes/db_connect.php';
include 'includes/header.php';
$result = $conn->query("SELECT d.id, d.name, d.specialization, d.image, d.is_verified FROM doctors d JOIN users u ON d.user_id = u.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors - Sleep Talks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <section class="full-screen">
        <div class="container">
            <h2 class="text-3xl font-bold text-center mb-5">Our Doctors</h2>
            <div class="row">
                <?php while ($doctor = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow p-4">
                            <!-- <img src="<?php echo $doctor['image'] ?: 'images/default_doctor.jpg'; ?>" class="card-img-top" alt="Doctor"> -->
                            <div class="card-body">
                                <h3 class="text-xl font-semibold"><?php echo $doctor['name']; ?></h3>
                                <p><?php echo $doctor['specialization']; ?></p>
                                <span class="badge <?php echo $doctor['is_verified'] ? 'bg-success' : 'bg-warning'; ?>">
                                    <?php echo $doctor['is_verified'] ? 'Verified' : 'Unverified'; ?>
                                </span>
                                <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#bookModal<?php echo $doctor['id']; ?>">Book Appointment</button>
                            </div>
                        </div>
                    </div>
                    <!-- Appointment Modal -->
                    <div class="modal fade" id="bookModal<?php echo $doctor['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Book Appointment with <?php echo $doctor['name']; ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="book_appointment.php" method="POST">
                                        <input type="hidden" name="doctor_id" value="<?php echo $doctor['id']; ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id'] ?? ''; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">Appointment Date</label>
                                            <input type="datetime-local" class="form-control" name="appointment_date" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
                                </div>
                            </div>
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