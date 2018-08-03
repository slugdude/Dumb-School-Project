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
$db = mysqli_connect("localhost", "root", "testPass", "ReservationSystem");

if (!$db) {
    echo "Connection failed:";
    exit("");
}

$stmt = mysqli_prepare($db, 'SELECT * FROM Reservations WHERE ReservationID =?');
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id, $date, $start_time, $end_time, $surname, $forename, $email, $tel_no, $type, $notes);
mysqli_stmt_fetch($stmt);


if (!$id) {
    echo "Reservation with ID " . $_GET['id'] . " not found.";
    mysqli_stmt_close($stmt);
    mysqli_close($db);
    exit;
}

echo 'Editing Reservation ID ' . $id;

if ($type == "external") {
    $externalSelected = "selected";
    $internalSelected = "";
} else {
    $externalSelected = "";
    $internalSelected = "selected";
}


$table = '<form action="UpdateReservation.php" method="POST" id="update">';
$table .= '<input type="hidden" name="id" value="' . $id;
$table .= '"><table>';


$table .= '<tr><td>Date: </td><td><input type="date" name="date" value="' . $date;
$table .= '"></td></tr><tr><td>Start Time: </td><td><input type="time" name="start_time" value="' . substr($start_time, 0);
$table .= '"></td></tr><tr><td>End Time: </td><td><input type="time" name="end_time" value="' . substr($end_time, 0, 5);
$table .= '"></td></tr><tr><td>Surname: </td><td><input type="text" name="surname" value="' . $surname;
$table .= '"></td></tr><tr><td>Forename: </td><td><input type="text" name="forename" value="' . $forename;
$table .= '"></td></tr><tr><td>Email: </td><td><input type="email" name="email" value="' . $email;
$table .= '"></td></tr><tr><td>Telephone Number: </td><td><input type="text" name="tel_no" value="' . $tel_no;
$table .= '"></td></tr><tr><td>Reservation Type: </td><td>';
$table .= '<select name="type" form="update"> <option value="external" ' . $externalSelected;
$table .= '>External</option> <option value="internal" ' . $internalSelected . '>Internal</option> </select>';
$table .= '</td></tr><tr><td>Notes: </td><td><input type="text" name="notes" value="' . $notes;
$table .= '"></td></tr>';
$table .= '</table><input type="submit" value="Update"></form>';
echo $table;

mysqli_stmt_close($stmt);
mysqli_close($db);

?>

</body>
</html>

