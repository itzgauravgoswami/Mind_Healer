<?php
include 'includes/db_connect.php';
include 'includes/header.php';
$result = $conn->query("SELECT title, content, created_at FROM tutorials ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tutorials - Mind Healer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <section class="full-screen">
        <div class="container">
            <h2 class="text-3xl font-bold text-center mb-5">Tutorials & Notes</h2>
            <div class="row">
                <?php while ($tutorial = $result->fetch_assoc()): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow p-4">
                            <h3 class="text-xl font-semibold"><?php echo $tutorial['title']; ?></h3>
                            <p class="text-muted">Posted on <?php echo $tutorial['created_at']; ?></p>
                            <p><?php echo substr($tutorial['content'], 0, 200) . '...'; ?></p>
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