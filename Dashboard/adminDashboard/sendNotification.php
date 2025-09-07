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
    <title>FitZone Dashboard | Admin</title>
    <link rel="stylesheet" href="../customerDashboard.css">
</head>
<body>
    <div class="container">

        <?php
            include("./adminSidebar.php");
        ?>
        
        <main class="main-content">

        <!-- Send Notifications Section -->
        <section id="send-notifications-section" class="content-section hidden">
            <header><h1>Send Broadcast Notification</h1></header>
            <form class="appointment-form" method="POST">
                <label>Notification Title</label>
                <input type="text" placeholder="Enter notification title" name="title" required>
                <label>Message</label>
                <textarea placeholder="Enter your message" rows="5" name="noticMSG" required></textarea>
                <button type="submit" class="btn-submit" name="sentNoticBTN" >Send Notification</button>
            </form>

                <?php 
                    if(isset($_POST['sentNoticBTN'])){
                        $title=trim($_POST['title']);
                        $noticMSG=trim($_POST['noticMSG']);

                        $sql_notification="INSERT INTO notificationBox(titleNB, messageNB) VALUES(?,?)";
                        
                        $stmt_notification=$conn->prepare($sql_notification);

                        $stmt_notification->bind_param("ss", $title, $noticMSG);

                        if($stmt_notification->execute()){
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