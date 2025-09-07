
<?php
session_start();

include("../includes/dbConnect.php");
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="icon" type="image/jpeg" href="../assets/Favicon.jpg">
  <title>FitZone Login</title>
  <link rel="stylesheet" href="./login.css"/>
</head>
<body>
  <div class="container">
    <div class="left-side">
      <div class="overlay">
        <h1>Create Account</h1>
        <a href="../accessPage/signup.php" class="btn-signup">Signup</a>
      </div>
    </div>
    <br><br>
    <div class="right-side">
      <div class="login-box">
        <h2>Log In</h2>
        <form method="POST" action="login.php">
          <label for="email">Email Address</label>
          <input type="email" name="userEmailID" id="email" placeholder="Enter your email" required />

          <label for="password">Password</label>
          <input type="password" name="userPwd" id="password" placeholder="Enter your password" required />

          <button type="submit" name="logInBTN" class="btn-login">LOG IN</button>

        </form>

        <div class="links">
          <a href="../index.html">Home</a> | <a href="../index.html#contact-section"">Contact Us</a>
      </div>
    </div>
  </div>
<?php
  if(isset($_POST["logInBTN"])){
    $uEID = trim($_POST["userEmailID"]);
    $uPwd = trim($_POST["userPwd"]);
  
    $sql = "SELECT * FROM users WHERE email=?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $uEID);

    $stmt->execute();

    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    if($row && password_verify($uPwd, $row['password'] )){
      $_SESSION['uID'] = $row['userID'];
      $_SESSION['uFN'] = $row['firstName'];
      $_SESSION['uRole'] = $row['role'];

      if($_SESSION['uRole'] == "customer"){
        echo '<script>
        alert("Login Successful !");
        window.location.href="../Dashboard/customerDashboard/dashboard.php"; 
        </script>';
      } elseif ($_SESSION['uRole'] == "staff"){
        echo '<script> 
        alert("Login Successful Staff !");
        window.location.href="../Dashboard/staffDashboard/staffDashboard.php";
        </script>';
      }elseif($_SESSION['uRole'] == "admin"){
        echo '<script> 
        alert("Login Successful Admin !"); 
        window.location.href="../Dashboard/adminDashboard/adminDashboard.php";
        </script>';
      }        
    }else{
      echo '<script> alert("Email or Password Incorrect !"); </script>';
    }

    $stmt->close();
  }

  $conn->close();
?>


</body>
</html>
