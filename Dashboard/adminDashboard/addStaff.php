<?php
session_start();
session_regenerate_id();

include("../../includes/dbConnect.php");
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../assets/Favicon.jpg" type="image/x-icon">
    <title>FitZone Dashboard | Admin</title>
    <link rel="stylesheet" href="../customerDashboard.css">
</head>
<body>
    <div class="container">

        <?php
            include("./adminSidebar.php");
        ?>
        
        <main class="main-content">

            <!-- Add Staff Section -->
            <section id="add-staff-section" class="content-section hidden">
                <header><h1>Add Staff Member</h1></header>
                <form class="appointment-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                    <label>First Name</label>
                    <input type="text" placeholder="Enter first name" name="tFirstName" required>
                    <label>Last Name</label>
                    <input type="text" placeholder="Enter last name" name="tLastName" required>
                    <label>Mobile Number</label>
                    <input type="tel" placeholder="07XXXXXXXX" name="tPhoneNumber" required>
                    <label>Email</label>
                    <input type="email" placeholder="Enter email" name="tEmail" required>
                    <label>Password</label>
                    <input type="password" placeholder="Enter password" name="tPassword" required>
                    <button type="submit" class="btn-submit" name="addStaffBTN" >Add Staff</button>
                </form>
            </section>

        </main>
    </div>

   

<?php
  if(isset($_POST["addStaffBTN"])){
    $fName = trim($_POST["tFirstName"]);
    $lName = trim($_POST["tLastName"]);
    $pNum = trim($_POST["tPhoneNumber"]);
    $emailID = trim($_POST["tEmail"]);
    $pwd = trim($_POST["tPassword"]);
    $role = "staff";
  
    $passwordHash = password_hash($pwd, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users(firstName, lastName, phoneNumber, email, password, role) VALUES(?,?,?,?,?,?)");
   
    if($stmt == false){
      die();
      echo '<script> alert("Prepare Fail !") </script>';
    }

    $stmt->bind_param("ssisss", $fName, $lName, $pNum, $emailID, $passwordHash, $role );

    if ($stmt->execute()) {
        // Get the last inserted user ID to use as a foreign key
        $newUserID = $stmt->insert_id;

        // Prepare and execute the second INSERT statement for the 'trainers' table
        $fullName = $fName . " " . $lName;
        $stmtTrainers = $conn->prepare("INSERT INTO trainers(fullName, userID) VALUES(?,?)");

        if ($stmtTrainers === false) {
            die("Prepare statement for trainers table failed: " . $conn->error);
        }

        $stmtTrainers->bind_param("si", $fullName, $newUserID);

        if ($stmtTrainers->execute()) {
            echo '<script> alert("Account Created Successfully!"); </script>';
        } else {
            echo '<script> alert("Account Created, but trainer entry failed!"); </script>';
        }
        $stmtTrainers->close();

    } else {
        echo '<script> alert("Account Creation Failed! Check for existing email."); </script>';
    }

    $stmt->close();

  }

  $conn->close();
?>


</body>
</html>