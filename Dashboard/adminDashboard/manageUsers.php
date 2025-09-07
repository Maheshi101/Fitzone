<?php
    session_start();
    include("../../includes/dbConnect.php");
    // error_reporting(0);


    // Delete trainer
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM users WHERE userID =$id";
    mysqli_query($conn, $query);
    header("Location: manageUsers.php");
    exit();
}

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
            include("./adminSidebar.php");
        ?>
        
        <main class="main-content">

        <!-- Manage Users Section -->
        <section id="details-section" class="content-section hidden">
            <header>
                <h1>Manage Users</h1>
            </header>

            <table>
                <thead>
                    <tr>
                        <th>Full Name</th>
                        <th>TP</th>
                        <th>email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

<?php
// SQL query to select all data from the users table
$query = "SELECT userID, CONCAT_WS(' ', firstName, lastName) AS fullName, phoneNumber, email, role FROM users WHERE role = 'customer'";

// Execute the query
$result = $conn->query($query);

// Check if any users are found
if($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
        <!-- Only show delete button for customers -->
            <tr>
                <td><?= htmlspecialchars($row['fullName']) ?></td>
                <td><?= htmlspecialchars($row['phoneNumber']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td>
                  <a class="btn-action btn-primary" href="manageUsers.php?delete=<?= htmlspecialchars($row['userID']) ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan='4'>No Users found.</td>
    </tr>
<?php endif; ?>

<?php
// Close the database connection
$conn->close();
?>

                </tbody>
            </table>
        </section>

        </main>
    </div>
</body>
</html>
