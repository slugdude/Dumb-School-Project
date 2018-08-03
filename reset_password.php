<?php
session_start();

if (!isset($_SESSION['username']) or empty($_SESSION['username'])){
    header("location: login_form.php");
    exit;
}
if ($_SESSION['admin'] == 0) { //Admin only
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE HTML>
<html>
<body>

<a href="reset_password_form.php">Go Back</a><br><br>

<?php

$db = mysqli_connect("localhost", "ReservationUser", "testPass", "ReservationSystem");

if (!$db) {
    echo "Connection failed:";
    exit;
}

$username = $_POST["username"];

$stmt = mysqli_prepare($db, "SELECT id FROM Users WHERE username=?");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id);

if (!mysqli_stmt_fetch($stmt)) {
    echo "User " . $username . " does not exist!";
} else {
    mysqli_stmt_close($stmt);
    $stmt = mysqli_prepare($db, "UPDATE Users SET password=? WHERE id=?"); //Change the password
    mysqli_stmt_bind_param($stmt, "si", password_hash("rougemont", PASSWORD_DEFAULT), $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo 'Password reset to "rougemont"';
}

mysqli_stmt_close($stmt);

?>
