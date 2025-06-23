<?php 
include 'includes/db_connect.php'; 
include 'includes/header.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatBot - Sleep Talks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    <style>
        .chat-container { height: 100vh; display: flex; flex-direction: column; }
        .chat-box { flex: 1; overflow-y: auto; padding: 20px; }
        .chat-message { margin-bottom: 10px; padding: 10px; border-radius: 10px; }
        .user-message { background-color: #162887; color: white; align-self: flex-end; }
        .bot-message { background-color: #eaeaea; }
    </style>
</head>
<body class="bg-gray-100">
    <section class="chat-container">
        <div class="chat-box" id="chatBox">
            <div class="chat-message bot-message">Hello! I'm here to support you. How can I help today?</div>
        </div>
        <div class="chat-input p-4 bg-white border-top">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <textarea id="messageInput" class="form-control" placeholder="Type your message..."></textarea>
                    </div>
                    <div class="col-auto">
                        <button id="sendBtn" class="btn btn-primary">Send</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="js/chatbot.js"></script>
    <?php include 'includes/footer.php'; ?>
</body>
</html>
<?php $conn->close(); ?>