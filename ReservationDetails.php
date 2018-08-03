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

<br>
<a href="index.php">Go to main page</a>
<p>

<?php

$id = $_GET['id'];
$db = mysqli_connect("localhost", "ReservationUser", "testPass", "ReservationSystem");

if (!$db) {
    echo "Connection failed:";
    exit("");
}

$stmt = mysqli_prepare($db, 'SELECT * FROM Reservations WHERE ReservationID =?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id, $date, $start_time, $end_time, $surname, $forename, $email, $tel_no, $type, $notes);
mysqli_stmt_fetch($stmt);


if (!$id) { //Reservation doesn't exist
    echo "Reservation with ID " . $_GET['id'] . " not found.";
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit;
}


//The output:
$table = "<table><tr><td>Reservation ID: </td><td>" . $id . "</td></tr>";
$table .= "<tr><td>Date: </td><td>" . $date . "</td></tr>";
$table .= "<tr><td>Start Time: </td><td>" . substr($start_time, 0, 5) . "</td></tr>";
$table .= "<tr><td>End Time: </td><td>" . substr($end_time, 0, 5) . "</td></tr>";
$table .= "<tr><td>Surname: </td><td>" . $surname . "</td></tr>";
$table .= "<tr><td>Forename: </td><td>" . $forename . "</td></tr>";
$table .= "<tr><td>Email: </td><td>" . $email . "</td></tr>";
$table .= "<tr><td>Telephone Number: </td><td>" . $tel_no . "</td></tr>";
$table .= "<tr><td>Reservation Type: </td><td>" . ucfirst($type) . "</td></tr>";
$table .= "<tr><td>Notes: </td><td>" . $notes . "</td></tr>";
$table .= "</table>";
echo $table;

//Links like delete or update
$links = '<p><a href="index.php?&month=' . substr($date, 0, 7) . '&id=' . $id;
$links .= '">View on Calendar</a>&emsp;<a href="UpdateReservation_form.php?&id=' . $id;
$links .= '">Update Reservation</a>&emsp;<a href="DeleteReservation.php?&id=' . $_GET['id'];
$links .= '">Delete reservation</a></p>';

echo $links;
mysqli_stmt_close($stmt);
mysqli_close($db);

?>

</body>
</html>
