<?php
session_start();
session_destroy();
echo '<script>
    alert("You are now logged out. See you again soon!");
    window.location.href = "./login.php";
</script>';
exit;
?>
