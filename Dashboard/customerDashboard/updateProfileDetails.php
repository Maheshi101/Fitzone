<?php
session_start();
include("../../includes/dbConnect.php");

// Check login
if (!isset($_SESSION['uID'])) {
    echo "<script>alert('Please log in first.'); window.location.href='./login.php';</script>";
    exit;
}

// Fetch current user details
$userID = $_SESSION['uID'];
$userQuery = $conn->prepare("SELECT firstName, lastName, email, profileImg FROM users WHERE userID = ?");
$userQuery->bind_param("i", $userID);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

// Handle form submission
if (isset($_POST['updateProfile'])) {
    $firstName = $_POST['firstName'];
    $lastName  = $_POST['lastName'];
    $email     = $_POST['email'];

    // Handle image upload
    $profileImg = $user['profileImg']; // current image
    if (!empty($_FILES['profileImg']['name'])) {
        $targetDir = "../../uploads/";
        $fileName = basename($_FILES['profileImg']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['profileImg']['tmp_name'], $targetFilePath)) {
                $profileImg = $fileName; // update image name
            } else {
                echo "<script>alert('Error uploading image.');</script>";
            }
        } else {
            echo "<script>alert('Invalid image type. Allowed: jpg, jpeg, png, gif.');</script>";
        }
    }

    // Update database
    $updateQuery = $conn->prepare("UPDATE users SET firstName = ?, lastName = ?, email = ?, profileImg = ? WHERE userID = ?");
    $updateQuery->bind_param("ssssi", $firstName, $lastName, $email, $profileImg, $userID);

    if ($updateQuery->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href='dashboard.php';</script>";
        exit;
    } else {
        echo "<script>alert('Error updating profile.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="shortcut icon" href="../../assets/Favicon.jpg" type="image/x-icon">
<title>Update Profile | FitZone</title>
<link rel="stylesheet" href="../customerDashboard.css">
</head>
<body>
<div class="container">
    <?php include("./customerSidebar.php"); ?>

    <main class="main-content">
        <section class="content-section">
            <h1>Update Profile</h1>
            <div class="card info-card">
                <form class="appointment-form" method="POST" enctype="multipart/form-data">
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" value="<?= htmlspecialchars($user['firstName']) ?>" required>

                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" value="<?= htmlspecialchars($user['lastName']) ?>" required>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

                    <label for="profileImg">Profile Image</label>
                    <input type="file" id="profileImg" name="profileImg" accept="image/*">

                    <button type="submit" name="updateProfile" class="btn-submit">Update Profile</button>
                    <a href="dashboard.php" class="btn-action btn-primary" style="display:block; text-align:center; margin-top:10px;">Cancel</a>
                </form>
            </div>
        </section>
    </main>
</div>
</body>
</html>

<?php $conn->close(); ?>
