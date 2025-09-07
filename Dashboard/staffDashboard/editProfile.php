<?php
session_start();
include("../../includes/dbConnect.php");

// Check login
if (!isset($_SESSION['uID'])) {
    header("Location: ../../login.php");
    exit();
}

$userID = $_SESSION['uID'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullName = $_POST['fullName'];
    $phone = $_POST['phoneNumber'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Split name
    $nameParts = explode(" ", $fullName, 2);
    $firstName = $nameParts[0];
    $lastName = isset($nameParts[1]) ? $nameParts[1] : "";

    // Handle profile image upload
    $profileImg = null;
    if (!empty($_FILES['profileImg']['name'])) {
        $targetDir = "../../uploads/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $fileName = time() . "_" . basename($_FILES["profileImg"]["name"]);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($_FILES["profileImg"]["tmp_name"], $targetFile)) {
            $profileImg = $fileName;
        }
    }

    // Update SQL
    $sql = "UPDATE users SET firstName=?, lastName=?, phoneNumber=? ";
    $params = [$firstName, $lastName, $phone];
    $types = "sss";

    if ($password) {
        $sql .= ", password=? ";
        $params[] = $password;
        $types .= "s";
    }
    if ($profileImg) {
        $sql .= ", profileImg=? ";
        $params[] = $profileImg;
        $types .= "s";
    }
    $sql .= "WHERE userID=?";
    $params[] = $userID;
    $types .= "i";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $stmt->close();

    $_SESSION['status_msg'] = "Profile updated successfully!";
    header("Location: ./staffDashboard.php");
    exit;
}

// Fetch user
$userQuery = $conn->prepare("SELECT userID, firstName, lastName, phoneNumber, email, profileImg FROM users WHERE userID = ?");
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
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../customerDashboard.css">
</head>
<body>
    <div class="container" >

    <!-- <?php include("./customerSidebar.php"); ?> -->
    <div class="edit-container">
        <h2>Edit Profile</h2>

        <img src="<?= htmlspecialchars($profileImg) ?>" alt="Profile Image">

        <?php if(isset($_SESSION['status_msg'])): ?>
            <p class="status-msg"><?= $_SESSION['status_msg']; unset($_SESSION['status_msg']); ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <label>Full Name:</label>
            <input type="text" name="fullName" value="<?= htmlspecialchars($fullName) ?>" required>

            <label>Phone Number:</label>
            <input type="text" name="phoneNumber" value="<?= htmlspecialchars($user['phoneNumber']) ?>" required>

            <label>Password (leave blank if unchanged):</label>
            <input type="password" name="password">

            <label>Profile Image:</label>
            <input type="file" name="profileImg" accept="image/*">

            <div style="display:flex; justify-content:space-between;">
                <button type="submit" name="update_profile" class="btn-action btn-primary">Save Changes</button>
                <a href="./staffDashboard.php" class="btn-action btn-danger">Cancel</a>
            </div>
        </form>
    </div>

    </div>
</body>
</html>
