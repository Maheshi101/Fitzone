<?php 
$server="localhost";
$user="root";
$password="";
$dbase="fitzone7312";

$conn = mysqli_connect($server, $user, $password, $dbase );
if(!$conn){
	die();
}
// echo '<script type="text/javascript">alert("Database Connected");</script>';
 ?>


