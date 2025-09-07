<?php
    session_start();
    include("../../includes/dbConnect.php");
    // error_reporting(0);
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

        <!-- Book Appointment Section -->
        <section id="book-section" class="content-section hidden">
            <header>
                <h1>Book Appointment</h1>
            </header>

            <form class="appointment-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <div class="dashboard">   

                 <div>
                <label for="trainer">Trainer Name</label>
                <select id="trainer" name="trainer" required>
                    <option value="">-- Select Trainer --</option>
                        <?php
                            // Fetch the list of trainers from the database
                            $query = "SELECT trainerID, fullName FROM trainers";
                            $result = $conn->query($query);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    echo "<option value='" . $row['trainerID'] . "'>" . htmlspecialchars($row['fullName']) . "</option>";
                                }
                            } else {
                                echo "<option value=''>No trainers available</option>";
                            }
                        ?>
                </select>

                <label for="sessionType">Session Type</label>
                <select id="sessionType" name="sessionType" required>
                    <option value="">-- Select Session Type --</option>
                    <option value="Cardio & HIIT">Cardio & HIIT</option>
                    <option value="Strength & Conditioning">Strength & Conditioning</option>
                    <option value="Yoga & Stretching">Yoga & Stretching</option>
                    <option value="Bodybuilding">Bodybuilding</option>
                    <option value="Weightlifting & Powerlifting">Weightlifting & Powerlifting</option>
                    <option value="Zumba & Dance Fitness">Zumba & Dance Fitness</option>
                    <option value="Core & Abs">Core & Abs</option>
                    <option value="Mobility & Flexibility">Mobility & Flexibility</option>
                    <option value="Senior Fitness">Senior Fitness</option>
                    <option value="Stretch & Relax">Stretch & Relax</option>
                </select>

                </div>

                <div>
                <label for="date">Date</label>
                <input type="date" id="date" name="date" required>

                <label for="time">Time</label>
                <input type="time" id="time" name="time" required>
                </div>
                </div>
                <label for="message">Message (Optional)</label>
                <textarea id="message" name="message" placeholder="Enter your message" rows="3"></textarea>

                <button type="submit" class="btn-submit" name="sentAppointmentBTN" >Send</button>
            </form>


<?php
  if(isset($_POST["sentAppointmentBTN"])){

    $trainer = trim($_POST["trainer"]);
    $sessionType = trim($_POST["sessionType"]);
    $date = trim($_POST["date"]);
    $time = trim($_POST["time"]);
    $message = trim($_POST["message"]);
    $status='pending';
  
    $sql_appointment="INSERT INTO bookappointment(userID, trainerID, sessionType, appointmentDate, appointmentTime, appointmentMsg, appointmentStatus) VALUES(?,?,?,?,?,?,?)";

    $stmt_appointment=$conn->prepare($sql_appointment);

    $stmt_appointment->bind_param("iisssss", $_SESSION['uID'], $trainer, $sessionType, $date, $time, $message, $status);

    if($stmt_appointment == false){
      die();
      echo '<script> alert("Prepare Fail !") </script>';
    }

    if($stmt_appointment->execute()){
      echo  '<script> 
              alert("Appointment Created !"); 
              window.location.href="./appointment.php"; 
            </script>';
      
    }else {
            echo  '<script> 
              alert("Appointment Create Fail !"); 
            </script>';
    }

    $$stmt_appointment->close();

  }

  $conn->close();
?>
        </section>

        </main>
    </div>
</body>
</html>