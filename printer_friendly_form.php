<?php
session_start();

if (!isset($_SESSION['username']) or empty($_SESSION['username'])){
    header("location: login.html");
    exit;
}
?>

<!DOCTYPE html>
<html>
<body>

<p>
<a href="index.php">Go back</a>
</p>

<p>
<form action="week_output.php">
    <input type="week" name="week">
    <input type="submit" text="Submit">
<br>

</p>

</body>
</html>

