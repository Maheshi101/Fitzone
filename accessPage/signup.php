<?php
session_start();
session_regenerate_id();
include("../includes/dbConnect.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/jpeg" href="../assets/Favicon.jpg">
  <title>FitZone Sign Up</title>
  <link rel="stylesheet" href="./signup.css">

</head>
<body>

  <div class="container">
    <div class="form-section">
      <h2>Create Your Account</h2>
      <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" >
        <div class="form-group">
          <input type="text" name="firstName" placeholder="First Name" required>
        </div>
        <div class="form-group">
          <input type="text" name="lastName" placeholder="Last Name" required>
        </div>
        <div class="form-group">
          <input type="tel" name="phoneNumber" placeholder="phone Number" required>
        </div>
        <div class="form-group">
          <input type="email" name="email" placeholder="Email" required>
        </div>
        <div class="form-group">
          <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" name="signUpBTN">Sign Up</button>
      </form>
        <div class="links">
          <a href="../index.html">Home</a> | <a href="../index.html#contact-section"">Contact Us</a>
        </div>
    </div>

    <div class="image-section">
      <div class="overlay-text">
        <p>Already have an account ?</p>
        <a href="../accessPage/login.php" class="btn-login">Log In</a>
      </div>
    </div>

  </div>


<?php
    if(isset($_POST["signUpBTN"])){
      $fName = trim($_POST["firstName"]);
      $lName = trim($_POST["lastName"]);
      $pNum = trim($_POST["phoneNumber"]);
      $emailID = trim($_POST["email"]);
      $pwd = trim($_POST["password"]);
    
      $passwordHash = password_hash($pwd, PASSWORD_DEFAULT);

      $stmt = $conn->prepare("INSERT INTO users(firstName, lastName, phoneNumber, email, password) VALUES(?,?,?,?,?)");

      if($stmt == false){
        die();
        echo '<script> alert("Prepare Fail !") </script>';
      }

      $stmt->bind_param("ssiss", $fName, $lName, $pNum, $emailID, $passwordHash);

      if($stmt->execute()){
        echo  '<script> 
                alert("Account Created !"); 
                window.location.href="./login.php"; 
              </script>';
        
      }else {
              echo  '<script> 
                alert("Account Create Fail !"); 
              </script>';
      }

      $stmt->close();

    }

    $conn->close();
?>

</body>
</html>
