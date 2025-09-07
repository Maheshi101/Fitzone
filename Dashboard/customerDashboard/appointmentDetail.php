<?php
    session_start();
    include("../../includes/dbConnect.php");

    // It's a good practice to check if the user is logged in
    // and if the session ID is set before using it in a query.
    
    // if (!isset($_SESSION['uID'])) {
    //     // Redirect to login page or show an error
    //     echo "<script>alert('Please log in to view appointments.'); window.location.href='./login.php';</script>";
    //     exit;
    // }

    if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM bookappointment WHERE appointmentID =$id";
    mysqli_query($conn, $query);
    header("Location:./appointmentDetail.php");
    exit();
    }

    // Prepare a SQL query to fetch the user's appointments.
    // We join the 'bookappointment' table with the 'trainers' table to get the trainer's name.
    $sql = "SELECT ba.appointmentID, t.fullName AS trainerName, ba.sessionType, ba.appointmentDate, ba.appointmentTime, ba.appointmentMsg, ba.appointmentStatus
            FROM bookappointment ba
            INNER JOIN trainers t ON ba.trainerID = t.trainerID
            WHERE ba.userID = ?";

    $stmt = $conn->prepare($sql);

    // Check if the prepare statement failed
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }

    // Bind the user ID from the session to the query placeholder
    $stmt->bind_param("i", $_SESSION['uID']);

    // Execute the statement
    $stmt->execute();

    // Get the result set
    $result = $stmt->get_result();

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
            include("./customerSidebar.php");
        ?>
        
        <main class="main-content">

        <!-- Appointment Details Section -->
        <section id="details-section" class="content-section">
            <header>
                <h1>Appointment Details</h1>
            </header>

            <table>
                <thead>
                    <tr>
                        <th>Trainer Name</th>
                        <th>Session Type</th>
                        <th>Date & Time</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['trainerName']); ?></td>
                            <td><?php echo htmlspecialchars($row['sessionType']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointmentDate'] . ' ' . $row['appointmentTime']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointmentMsg']); ?></td>
                            <td><?php echo htmlspecialchars($row['appointmentStatus']); ?></td>
                            <td>
                                <!-- The buttons will need to be linked to a form or a JS function for editing and canceling -->
                                <a class="btn-action btn-primary" href="updateAppointment.php?update_id=<?= htmlspecialchars($row['appointmentID']) ?>">Edit</a> <br><br>
                                <a class="btn-action btn-primary" href="appointmentDetail.php?delete=<?= htmlspecialchars($row['appointmentID']) ?>" onclick="return confirm('Are you sure?')">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center;">No appointments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        </main>
    </div>

<?php
// Close the statement and connection at the end of the script
$stmt->close();
$conn->close();
?>

</body>
</html>
