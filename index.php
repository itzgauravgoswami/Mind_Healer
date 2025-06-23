<?php include 'includes/db_connect.php'; include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Sleep Talks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Hero Section -->
    <section class="hero-section full-screen">
        <div class="container">
            <h1 class="text-5xl font-bold mb-4">Welcome to Sleep Talks</h1>
            <p class="text-xl mb-6">Your journey to mental wellness starts here. Connect, heal, and grow with our AI chatbot, video conferencing, and professional support.</p>
            <a href="login.php" class="btn btn-primary btn-lg">Get Started</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-3xl font-bold text-center mb-5">Our Features</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card shadow p-4">
                        <h3 class="text-xl font-semibold">AI ChatBot</h3>
                        <p>Chat with our AI-powered bot to feel supported and never alone.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow p-4">
                        <h3 class="text-xl font-semibold">Video Conferencing</h3>
                        <p>Connect with others in real-time through video calls.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow p-4">
                        <h3 class="text-xl font-semibold">Book Appointments</h3>
                        <p>Schedule sessions with verified doctors for professional help.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>