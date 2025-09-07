<?php
    session_start();
    include("../../includes/dbConnect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../assets/Favicon.jpg" type="image/x-icon">
    <title>FitZone Dashboard | Staff</title>
    <link rel="stylesheet" href="../customerDashboard.css">
</head>
<body>
    <div class="container">

        <?php
            // Assuming staffSidebar.php includes the database connection logic
            // and sets a session variable for the logged-in staff's trainerID.
            include("./staffSidebar.php");
            
            // Get the current staff's trainerID
            $current_user_id = $_SESSION['uID'];
            $sql_trainer_id = "SELECT trainerID FROM trainers WHERE userID = ?";
            $stmt_trainer_id = $conn->prepare($sql_trainer_id);
            $current_trainer_id = null;
            if ($stmt_trainer_id) {
                $stmt_trainer_id->bind_param("i", $current_user_id);
                $stmt_trainer_id->execute();
                $result_trainer_id = $stmt_trainer_id->get_result();
                $trainer_row = $result_trainer_id->fetch_assoc();
                $current_trainer_id = $trainer_row['trainerID'] ?? null;
                $stmt_trainer_id->close();
            }

            if (!$current_trainer_id) {
                die("Trainer ID not found for the logged-in user.");
            }

            // --- FETCH MESSAGES FOR THE SPECIFIC TRAINER ---
            $sql = "SELECT m.messageID, m.subject, m.message, m.reply, u.firstName, u.lastName, u.phoneNumber
                    FROM messages AS m
                    JOIN users AS u ON m.userID = u.userID
                    WHERE m.trainerID = ?
                    ORDER BY m.messageID DESC";

            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                die("Error preparing statement: " . $conn->error);
            }
            $stmt->bind_param("i", $current_trainer_id);
            $stmt->execute();
            $result = $stmt->get_result();
        ?>
        
        <main class="main-content">

        <!-- Messages Section -->
        <section id="messages-section" class="content-section">

            <header>
                <h1>Messages</h1>
            </header>

            <table>
                <table>
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>TP</th>
                            <th>Message</th>
                            <th>Reply</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['firstName'] . ' ' . $row['lastName']) ?></td>
                                    <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
                                    <td><?= htmlspecialchars($row['message']) ?></td>
                                    <td><?= htmlspecialchars($row['reply']) ?></td>
                                    <td><?= empty($row['reply']) ? 'Pending' : 'Replied' ?></td>
                                    <td>
                                        <a class="btn-action btn-primary" href="staff-reply.php?reply_id=<?= htmlspecialchars($row['messageID']) ?>">Reply</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No messages for this trainer.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
<?php $conn->close(); ?>
