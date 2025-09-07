<?php
session_start();
include("../../includes/dbConnect.php");

// Check login
if (!isset($_SESSION['uID'])) {
    echo "<script>alert('Please log in first.'); window.location.href='./login.php';</script>";
    exit;
}

if (isset($_GET['update_id'])) {
    $update_id = $_GET['update_id'];

    // Fetch appointment details for this user
    $sql = "SELECT ba.appointmentID, ba.trainerID, ba.appointmentDate, ba.appointmentTime, ba.appointmentMsg, ba.sessionType,
                   t.fullName AS trainerName
            FROM bookappointment ba
            INNER JOIN trainers t ON ba.trainerID = t.trainerID  
            WHERE ba.appointmentID = ? AND ba.userID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $update_id, $_SESSION['uID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointment = $result->fetch_assoc();

    if (!$appointment) {
        echo "<script>alert('Appointment not found or no permission.'); window.location.href='appointmentDetail.php';</script>";
        exit;
    }

    // Handle update form submission
    if (isset($_POST['update'])) {
        $trainer_id = $_POST['trainer_id'];
        $appointment_date = $_POST['appointment_date'];
        $appointment_time = $_POST['appointment_time'];
        $appointment_msg  = $_POST['appointment_msg'];
        $session_type     = $_POST['session_type'];

        $update_sql = "UPDATE bookappointment 
                       SET trainerID = ?, appointmentDate = ?, appointmentTime = ?, appointmentMsg = ?, sessionType = ?
                       WHERE appointmentID = ? AND userID = ?";

        $stmt2 = $conn->prepare($update_sql);
        $stmt2->bind_param("issssii", $trainer_id, $appointment_date, $appointment_time, $appointment_msg, $session_type, $update_id, $_SESSION['uID']);

        if ($stmt2->execute()) {
            echo "<script>alert('Appointment updated successfully!'); window.location.href='appointmentDetail.php';</script>";
        } else {
            echo "<script>alert('Error updating appointment.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../assets/Favicon.jpg" type="image/x-icon">
    <title>Edit Appointment</title>
    <link rel="stylesheet" href="../customerDashboard.css">
</head>
<body>
    <div class="container">
        <?php include("./customerSidebar.php"); ?>

        <main class="main-content" >
            <section id="book-section" class="content-section hidden">
            <h1>Edit Appointment</h1>
            <form class="appointment-form" method="POST" >
                <label for="trainer" >Trainer</label>
                <select name="trainer_id" id="trainer" required>
                    <?php
                        $trainers = $conn->query("SELECT trainerID, fullName FROM trainers");
                        while ($t = $trainers->fetch_assoc()) {
                            $selected = ($t['trainerID'] == $appointment['trainerID']) ? 'selected' : '';
                            echo "<option value='{$t['trainerID']}' $selected>".htmlspecialchars($t['fullName'])."</option>";
                        }
                    ?>
                </select><br><br>

                <label for="session_type">Session Type</label>
                <select id="session_type" name="session_type" required>
                    <option value="">-- Select Session Type --</option>
                    <?php
                        $sessionTypes = [
                            "Cardio & HIIT",
                            "Strength & Conditioning",
                            "Yoga & Stretching",
                            "Bodybuilding",
                            "Weightlifting & Powerlifting",
                            "Zumba & Dance Fitness",
                            "Core & Abs",
                            "Mobility & Flexibility",
                            "Senior Fitness",
                            "Stretch & Relax"
                        ];

                        foreach ($sessionTypes as $type) {
                            $selected = ($appointment['sessionType'] == $type) ? 'selected' : '';
                            echo "<option value='" . htmlspecialchars($type) . "' $selected>" . htmlspecialchars($type) . "</option>";
                        }
                    ?>
                </select><br><br>

                <label for="appointment_date">Date</label>
                <input type="date" id="appointment_date" name="appointment_date" value="<?= htmlspecialchars($appointment['appointmentDate']) ?>" required><br><br>

                <label for="appointment_time">Time</label>
                <input type="time" id="appointment_time" name="appointment_time" value="<?= htmlspecialchars($appointment['appointmentTime']) ?>" required><br><br>

                <label for="appointment_msg">Message</label>
                <textarea id="appointment_msg" name="appointment_msg"><?= htmlspecialchars($appointment['appointmentMsg']) ?></textarea><br><br>

                <button type="submit" name="update" class="btn-action btn-primary">Update Appointment</button>
                <a href="appointmentDetail.php" class="btn-action">Cancel</a>
            </form>
            </section>
        </main>
    </div>
</body>
</html>

<?php $conn->close(); ?>
