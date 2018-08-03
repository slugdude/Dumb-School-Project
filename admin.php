<?php
session_start();

if (!isset($_SESSION['username']) or empty($_SESSION['username'])){
    header("location: login_form.php");
    exit;
}
if ($_SESSION['admin'] == 0) { //Only admin should be able to use this page.
    header("location: index.php");
    exit;
}

?>

<!DOCTYPE HTML>

<html>
<body>
<br>
<a href="index.php">Go Back</a><br><br>
<a href="create_user_form.php">Create Account</a>&emsp;<a href="delete_user_form.php">Delete Account</a><br><br>
<a href="reset_password_form.php">Reset Account Password</a>

</body>
</html>
