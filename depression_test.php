<?php
include 'includes/db_connect.php';
include 'includes/header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $score = array_sum($_POST['answers']);
    $stmt = $conn->prepare("INSERT INTO depression_tests (user_id, score) VALUES (?, ?)");
    $stmt->bind_param("ii", $_SESSION['user_id'], $score);
    $stmt->execute();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Depression Test - Mind Healer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <section class="full-screen">
        <div class="container">
            <h2 class="text-3xl font-bold text-center mb-5">Depression Test</h2>
            <form id="depressionTest" action="depression_test.php" method="POST">
                <?php
                $questions = [
                    "Feeling sad or down most of the time?",
                    "Losing interest in activities you once enjoyed?",
                    // Add 18 more relevant questions
                ];
                for ($i = 0; $i < 20; $i++): ?>
                    <div class="card shadow p-4 mb-4">
                        <h3 class="text-lg font-semibold">Question <?php echo $i + 1; ?>: <?php echo $questions[$i] ?? "Sample question about your mood?"; ?></h3>
                        <div class="form-check">
                            <input type="radio" name="answers[<?php echo $i; ?>]" value="0" class="form-check-input" required>
                            <label class="form-check-label">Not at all</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="answers[<?php echo $i; ?>]" value="1" class="form-check-input">
                            <label class="form-check-label">Sometimes</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="answers[<?php echo $i; ?>]" value="2" class="form-check-input">
                            <label class="form-check-label">Often</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="answers[<?php echo $i; ?>]" value="3" class="form-check-input">
                            <label class="form-check-label">Always</label>
                        </div>
                    </div>
                <?php endfor; ?>
                <button type="submit" class="btn btn-primary">Submit Test</button>
            </form>
            <?php if (isset($score)): ?>
                <div class="mt-5 alert alert-info">
                    Your score: <?php echo $score; ?>/60.
                    <?php
                    if ($score <= 15) echo "You seem to be doing well!";
                    elseif ($score <= 30) echo "Mild symptoms detected. Consider talking to someone.";
                    else echo "Please consult a professional for support.";
                    ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <script src="js/test.js"></script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>