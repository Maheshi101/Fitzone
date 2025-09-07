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

            <!-- Messages Section -->
            <section id="messages-section" class="content-section hidden">
                <header>
                    <h1>Messages</h1>
                </header>

                <table>
                    <thead>
                        <tr>
                            <th>From</th>
                            <th>Message</th>
                            <th>Reply</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $query_msg="SELECT  m.subject,  m.message,  m.reply,  t.fullName AS trainerName FROM messages AS m JOIN trainers AS t ON m.trainerID = t.trainerID JOIN users AS u  ON t.userID = u.userID WHERE  m.userID = '".$_SESSION['uID']."'";

                            $msg_Result=$conn->query($query_msg);

                            if($msg_Result->num_rows>0){
                                while($row=$msg_Result->fetch_assoc()){
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['trainerName']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['subject']) . " - " . htmlspecialchars($row['message']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['reply']) . "</td>";
                                    echo "</tr>";
                                }
                            }else {
                                echo "<tr><td colspan='4'>No messages found.</td></tr>";
                            }
                        ?>

                    </tbody>
                </table>

                <form class="appointment-form" method="POST" >
                    <div class="dashboard">   

                        <div>
                            <label for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" placeholder="Enter your Subject" required>
                        </div>

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
                        </div>

                    </div>

                    <label for="message">Message</label>

                    <textarea id="message" name="message" placeholder="Enter your message" rows="4"></textarea>

                    <button type="submit" class="btn-submit" name="send">Send</button>
                </form>
                
                <?php 
                    if(isset($_POST['send'])){
                        $subj=trim($_POST['subject']);
                        $trainers=trim($_POST['trainer']);
                        $msg=trim($_POST['message']);
                        $status="Pending";

                        $sql_msg="INSERT INTO messages(userID, trainerID, subject, message, status) VALUES(?,?,?,?,?)";
                        
                        $stmt_msg=$conn->prepare($sql_msg);

                        $stmt_msg->bind_param("iisss", $_SESSION['uID'], $trainers, $subj, $msg, $status);

                        if($stmt_msg->execute()){
                            echo '<script>
                            alert("Message Sent Successfully !");
                            </script>';
                        }
                        else{
                            echo '<script>
                            alert("Unsuccessfully !");
                            </script>';
                        }
                    }
                ?>
            </section>
        </main>
    </div>
</body>
</html>