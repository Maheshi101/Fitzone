<?php
session_start();
include("../../includes/dbConnect.php");

// --- Get trainerID for logged-in user ---
$trainerID = null;
$sql_trainer_id = "SELECT trainerID FROM trainers WHERE userID = ?";
$stmt_trainer_id = $conn->prepare($sql_trainer_id);
$stmt_trainer_id->bind_param("i", $_SESSION['uID']);
$stmt_trainer_id->execute();
$result_trainer_id = $stmt_trainer_id->get_result();

if ($result_trainer_id->num_rows > 0) {
    $row = $result_trainer_id->fetch_assoc();
    $trainerID = $row['trainerID'];
}
$stmt_trainer_id->close();

if ($trainerID === null) {
    echo "<tr><td colspan='7' style='text-align: center;'>You are not registered as a trainer.</td></tr>";
    echo "</tbody></table></section></main></div></body></html>";
    $conn->close();
    exit;
}

// --- Function to update appointment status ---
function updateAppointmentStatus($conn, $appointmentID, $status) {
    $sql = "UPDATE bookappointment SET appointmentStatus = ? WHERE appointmentID = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) die('Prepare failed: ' . htmlspecialchars($conn->error));
    $stmt->bind_param("si", $status, $appointmentID);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

// --- Handle Confirm/Reject button click ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_btn']) || isset($_POST['reject_btn'])) {
        $appointmentID = $_POST['appointmentID'];
        $status = isset($_POST['confirm_btn']) ? 'Confirmed' : 'Rejected';

        if (updateAppointmentStatus($conn, $appointmentID, $status)) {
            $_SESSION['status_msg'] = "Appointment #$appointmentID has been $status.";
        } else {
            $_SESSION['status_msg'] = "Failed to update appointment #$appointmentID.";
        }

        header("Location: " . $_SERVER['PHP_SELF']); // Refresh page to show updated status
        exit;
    }
}

// --- Get appointments for this trainer ---
$sql_appointments = "SELECT ba.appointmentID, u.firstName, u.lastName, u.phoneNumber, ba.sessionType, ba.appointmentDate, ba.appointmentTime, ba.appointmentStatus, ba.appointmentMsg
                     FROM bookappointment ba
                     INNER JOIN users u ON ba.userID = u.userID
                     WHERE ba.trainerID = ?";
$stmt_appointments = $conn->prepare($sql_appointments);
$stmt_appointments->bind_param("i", $trainerID);
$stmt_appointments->execute();
$result_appointments = $stmt_appointments->get_result();
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

<?php include("./staffSidebar.php"); ?>

<main class="main-content">
<section id="details-section" class="content-section">
    <header><h1>Appointment Details</h1></header>

    <?php
    if(isset($_SESSION['status_msg'])){
        echo "<p class='status-msg'>".$_SESSION['status_msg']."</p>";
        unset($_SESSION['status_msg']);
    }
    ?>

    <table>
        <thead>
            <tr>
                <th>From</th>
                <th>TP</th>
                <th>Date & Time</th>
                <th>Session Type</th>
                <th>Message</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_appointments->num_rows > 0): ?>
                <?php while ($row = $result_appointments->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['firstName'] . ' ' . $row['lastName']); ?></td>
                        <td><?php echo htmlspecialchars($row['phoneNumber']); ?></td>
                        <td><?php echo htmlspecialchars($row['appointmentDate'] . ' / ' . $row['appointmentTime']); ?></td>
                        <td><?php echo htmlspecialchars($row['sessionType']); ?></td>
                        <td><?php echo htmlspecialchars($row['appointmentMsg']); ?></td>
                        <td><?php echo htmlspecialchars($row['appointmentStatus']); ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="appointmentID" value="<?php echo $row['appointmentID']; ?>">
                                <button type="submit" class="btn-action btn-primary" name="confirm_btn">Confirm</button>
                                <br>
                                <button type="submit" class="btn-action btn-danger" name="reject_btn">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align:center;">No appointments found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
</main>
</div>

<?php
$stmt_appointments->close();
$conn->close();
?>
</body>
</html>
