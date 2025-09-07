<?php
    session_start();
    include("../../includes/dbConnect.php");

    // Check login
    if (!isset($_SESSION['uID'])) {
        header("Location: ./login.php");
        exit;
    }

    $message = null;
    $status_message = ''; // For displaying success or error messages

    // Check if a message ID is provided in the URL
    if (isset($_GET['reply_id'])) {
        $reply_id = $_GET['reply_id'];

        // Get the current staff's trainerID
        $current_user_id = $_SESSION['uID'];
        $sql_trainer_id = "SELECT trainerID FROM trainers WHERE userID = ?";
        $stmt_trainer_id = $conn->prepare($sql_trainer_id);
        if ($stmt_trainer_id) {
            $stmt_trainer_id->bind_param("i", $current_user_id);
            $stmt_trainer_id->execute();
            $result_trainer_id = $stmt_trainer_id->get_result();
            $trainer_row = $result_trainer_id->fetch_assoc();
            $current_trainer_id = $trainer_row['trainerID'] ?? null;
            $stmt_trainer_id->close();
        }

        if (!$current_trainer_id) {
            die("You are not authorized to view this page.");
        }

        // Fetch message details for this trainer
        $sql = "SELECT m.messageID, m.subject, m.message, m.reply, u.firstName, u.lastName, u.email
                FROM messages m
                INNER JOIN users u ON m.userID = u.userID
                WHERE m.messageID = ? AND m.trainerID = ?";
        
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $status_message = "Error preparing statement: " . $conn->error;
        } else {
            $stmt->bind_param("ii", $reply_id, $current_trainer_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $message = $result->fetch_assoc();
            $stmt->close();
        }

        if (!$message) {
            header("Location: staffDashboard.php");
            exit;
        }
    }

    // Handle form submission for sending reply
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_reply'])) {
        $message_id_to_reply = $_POST['message_id'];
        $reply_content = $_POST['reply_content'];

        // Update the reply field and set status to 'Replied'
        $update_sql = "UPDATE messages SET reply = ?, status = 'Replied' WHERE messageID = ?";
        $stmt_update = $conn->prepare($update_sql);
        if ($stmt_update === false) {
            $status_message = "Error preparing update statement: " . $conn->error;
        } else {
            $stmt_update->bind_param("si", $reply_content, $message_id_to_reply);
            if ($stmt_update->execute()) {
                $status_message = "Reply sent successfully!";
                // Redirect after success to prevent form resubmission
                header("Location: staffDashboard.php?status=replied");
                exit;
            } else {
                $status_message = "Error sending reply: " . $stmt_update->error;
            }
            $stmt_update->close();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../assets/Favicon.jpg" type="image/x-icon">
    <title>Reply Message</title>
    <link rel="stylesheet" href="../customerDashboard.css">
    <style>
        /* CSS for a cleaner look and modal styling */
        body { font-family: 'Arial', sans-serif; background-color: #f4f4f4; margin: 0; }
        .container { display: flex; }
        .main-content { flex-grow: 1; padding: 2rem; }
        .content-section { background-color: #fff; padding: 2rem; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #333; margin-bottom: 1.5rem; }
        .reply-form-container { max-width: 600px; margin: 0 auto; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: #555; }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        textarea { resize: vertical; min-height: 150px; }
        .btn-submit {
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
            background-color: #4CAF50;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .btn-submit:hover { background-color: #45a049; }

        /* Modal styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #fefefe;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            max-width: 400px;
        }
        .modal-buttons { margin-top: 20px; }
        .modal-buttons button { margin: 0 10px; }
    </style>
</head>
<body>
    <div class="container">
        <?php include("./staffSidebar.php"); ?>

        <main class="main-content">
            <section id="reply-section" class="content-section">
                <h1>Reply to Message</h1>

                <?php if ($message): ?>
                    <div class="reply-form-container">
                        <div class="form-group">
                            <label>From:</label>
                            <p><?= htmlspecialchars($message['firstName'] . ' ' . $message['lastName']) ?></p>
                        </div>
                        <div class="form-group">
                            <label>Subject:</label>
                            <p><?= htmlspecialchars($message['subject']) ?></p>
                        </div>
                        <div class="form-group">
                            <label>Original Message:</label>
                            <p><?= nl2br(htmlspecialchars($message['message'])) ?></p>
                        </div>
                        <div class="form-group">
                            <label>Current Reply:</label>
                            <p><?= $message['reply'] ? nl2br(htmlspecialchars($message['reply'])) : 'No reply yet.' ?></p>
                        </div>

                        <form class="reply-form" method="POST" action="">
                            <input type="hidden" name="message_id" value="<?= htmlspecialchars($message['messageID']) ?>">
                            <div class="form-group">
                                <label for="reply_content">Your Reply:</label>
                                <textarea id="reply_content" name="reply_content" placeholder="Type your reply here..." required></textarea>
                            </div>
                            <button type="submit" class="btn-submit" name="send_reply">Send Reply</button>
                        </form>
                    </div>
                <?php else: ?>
                    <p>No message found with the provided ID or you do not have permission.</p>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <!-- Status Modal (for success/error messages) -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <p id="statusMessage"></p>
            <div class="modal-buttons">
                <button class="btn-action btn-primary" onclick="document.getElementById('statusModal').style.display='none';">OK</button>
            </div>
        </div>
    </div>

    <script>
        // Check for a status message in the URL and display the modal
        window.onload = function() {
            const urlParams = new URLSearchParams(window.location.search);
            const status = urlParams.get('status');
            if (status === 'replied') {
                const statusModal = document.getElementById('statusModal');
                const statusMessage = document.getElementById('statusMessage');
                statusMessage.textContent = 'Reply sent successfully!';
                statusModal.style.display = 'flex';
                // Clean the URL
                history.replaceState({}, '', window.location.pathname);
            }
        };
    </script>
</body>
</html>
<?php $conn->close(); ?>
