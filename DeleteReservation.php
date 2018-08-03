<?php
session_start();

if (!isset($_SESSION['username']) or empty($_SESSION['username'])){
    header("location: login_form.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<body>

<br>
<a href="index.php">Go Back</a>
<p>

<?php

$db = mysqli_connect("localhost", "ReservationUser", "testPass", "ReservationSystem");

if (!$db) {
    echo "Connection failed:";
    exit("");
}

$stmt = mysqli_prepare($db, "SELECT ReservationID FROM Reservations WHERE ReservationID=?"); //Make sure the reservation actually exists
mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id);

if (!mysqli_stmt_fetch($stmt)) { //If it doesn't exist, there's nothing to delete
    echo "Reservation with ID " . $_GET['id'] . " not found.";
    mysqli_close($db);
    exit;
}

mysqli_stmt_close($stmt);

$stmt = mysqli_prepare($db, "DELETE FROM Reservations WHERE ReservationID=?"); //Delete it
mysqli_stmt_bind_param($stmt, "i", $_GET['id']);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo "Deleted successfully.";

mysqli_close($db);

?>
