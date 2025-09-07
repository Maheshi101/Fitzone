<?php
session_start();
include("../../includes/dbConnect.php");

// Check login
if (!isset($_SESSION['uID'])) {
    header("Location: ../../login.php");
    exit();
}

$userID = $_SESSION['uID'];

// Fetch logged-in user details
$userQuery = $conn->prepare("
    SELECT userID, firstName, lastName, phoneNumber, email, role, profileImg
    FROM users WHERE userID = ?");
$userQuery->bind_param("i", $userID);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

$fullName   = $user['firstName'] . " " . $user['lastName'];
$profileImg = !empty($user['profileImg']) ? "../../uploads/" . $user['profileImg'] : "../../assets/defaultProfile.jpg";
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
        <?php include("./staffSidebar.php"); ?>
        
        <main class="main-content">
            <section id="dashboard-section" class="content-section">
                <section class="dashboard">
                    <div class="card info-card">
                        <img src="<?= htmlspecialchars($profileImg) ?>" alt="Profile Image" class="profile-img">
                        <h2 class="name"><?= htmlspecialchars($fullName) ?></h2>
                        <p class="email"><?= htmlspecialchars($user['email']) ?></p>
                        <p class="phone"><?= htmlspecialchars($user['phoneNumber']) ?></p>
                        <br>
                        <!-- Go to separate Edit Profile page -->
                        <a href="editProfile.php" class="btn-action btn-primary">Edit</a>
                    </div>

                    <div class="card">
                        <h3>Notifications</h3>
                        <ul>
                            <?php
                            $notifQuery = "SELECT titleNB, messageNB FROM notificationBox ORDER BY id DESC";
                            $notifResult = $conn->query($notifQuery);
                            if ($notifResult && $notifResult->num_rows > 0) {
                                while ($notif = $notifResult->fetch_assoc()) {
                                    echo "<li><strong>" . htmlspecialchars($notif['titleNB']) . ":</strong><br>" . htmlspecialchars($notif['messageNB']) . "</li>";
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
