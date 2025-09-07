<?php
session_start();
include("../../includes/dbConnect.php");

// Fetch logged-in user details
$userID = $_SESSION['uID'];
$userQuery = $conn->prepare("
    SELECT userID, CONCAT(firstName, ' ', lastName) AS fullName, phoneNumber, email, role 
    FROM users 
    WHERE userID = '".$_SESSION['uID']."'");
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../assets/Favicon.jpg" type="image/x-icon">
    <title>FitZone Dashboard | Customer</title>
    <link rel="stylesheet" href="../customerDashboard.css">
</head>
<body>
    <div class="container">

        <?php
            include("./adminSidebar.php");
        ?>
        
        <main class="main-content">

            <!-- Dashboard Section -->
            <section id="dashboard-section" class="content-section">
                <section class="dashboard">
                <div class="card info-card">
                    <img src="../../assets/defaultProfile.jpg" alt="Profile Image" class="profile-img">
                    <h2 class="name"><?= htmlspecialchars($user['fullName']) ?></h2>
                    <p class="email"><?= htmlspecialchars($user['email']) ?></p>
                </div>

                    <div>
                        <div class="card">
                        <h3>Notifications</h3>
                        <ul>
                            <?php
                            // Fetch notifications from the database
                            $notifQuery = "SELECT titleNB, messageNB FROM notificationBox ORDER BY id DESC";
                            $notifResult = $conn->query($notifQuery);

                            if ($notifResult && $notifResult->num_rows > 0) {
                                while ($notif = $notifResult->fetch_assoc()) {
                                    echo "<li><strong>" . htmlspecialchars($notif['titleNB']) . ":</strong> <br>" . htmlspecialchars($notif['messageNB']) . "</li>";
                                }
                            } else {
                                echo "<li>No notifications found.</li>";
                            }
                            ?>
                        </ul>
                    </div>
                </section>
            </section>
        </main>
    </div>
</body>
</html>