<?php
include 'includes/db_connect.php';
include 'includes/header.php';
$result = $conn->query("SELECT u.id, u.username FROM users u JOIN online_users ou ON u.id = ou.user_id WHERE ou.is_online = TRUE");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Talk to Someone - Mind Healer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .video-container { height: 100vh; display: flex; align-items: center; justify-content: center; }
    </style>
</head>
<body class="bg-gray-100">
    <section class="full-screen">
        <div class="container">
            <h2 class="text-3xl font-bold text-center mb-5">Talk to Someone</h2>
            <div class="row">
                <?php while ($user = $result->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow p-4">
                            <h3 class="text-xl font-semibold"><?php echo $user['username']; ?></h3>
                            <button class="btn btn-primary mt-3" onclick="startVideoCall(<?php echo $user['id']; ?>)">Start Video Call</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>

    <!-- Video Call Interface -->
    <div class="video-container d-none" id="videoCall">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <video id="localVideo" autoplay playsinline class="w-100"></video>
                </div>
                <div class="col-md-6">
                    <video id="remoteVideo" autoplay playsinline class="w-100"></video>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-secondary" onclick="toggleMic()">Toggle Mic</button>
                <button class="btn btn-secondary" onclick="toggleCamera()">Toggle Camera</button>
                <button class="btn btn-danger" onclick="endCall()">End Call</button>
            </div>
        </div>
    </div>

    <script src="js/video.js"></script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>