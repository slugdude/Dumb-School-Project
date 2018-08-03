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

<?php
$id = $_POST["id"];
$date = $_POST["date"];
$start_time = $_POST["start_time"];
$end_time = $_POST["end_time"];
$surname = $_POST["surname"];
$forename = $_POST["forename"];
$email = $_POST["email"];
$tel_no = $_POST["tel_no"];
$type = $_POST["type"];
$notes = $_POST["notes"];

$db = mysqli_connect("localhost", "ReservationUser", "testPass", "ReservationSystem");

if (!$db) {
    echo "Connection failed:";
    exit;
}

//only allow new date/time if:
//start_time <= existing_start and end_time <= existing_start       (new reservation precedes old)
//or
//start_time >= existing_end and end_time >= existing_end           (new reservation succeeds old)

//Any other combination is invalid since every valid reservation should either precede or succede existing
//reservations. If there are no reservations that day, it should be valid anyway

//Technically this allows for 0 length reservations, however an error here will not be useful to the user.
//That should be validated separately.

$stmt = mysqli_prepare($db, 'SELECT ReservationID, Start_time, End_time FROM Reservations WHERE Date = ? AND ReservationID <> ?'); //ReservationID <> $id because otherwise it can fail because the reservation clashes with itself
mysqli_stmt_bind_param($stmt, "si", $date, $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $check_id, $check_start, $check_end);

while (mysqli_stmt_fetch($stmt)) {

    if (!(($start_time <= $check_start and $end_time <= $check_start) or ($start_time >= $check_end and $end_time >= $check_end))) { //If the new date/time clash with another reservation

        $clashError = 'The new date/time selected would clash with reservation <a href="ReservationDetails.php?&id=' . $check_id;
        $clashError .= '">' . $check_id . '</a> (' . substr($check_start, 0, 5) . '-' . substr($check_end, 0, 5) . ').';
        $clashError .= ' <a href="index.php">Go to home page</a>';
        echo $clashError;
        exit;

    }

}
mysqli_stmt_close($stmt);

//Create the new reservation

$stmt = mysqli_prepare($db, "UPDATE Reservations SET Date=?,Start_time=?,End_time=?,Surname=?,Forename=?,Email=?,Tel_No=?,Reservation_Type=?,Notes=? WHERE ReservationID = ?");
mysqli_stmt_bind_param($stmt, "sssssssssi", $date, $start_time, $end_time, $surname, $forename, $email, $tel_no, $type, $notes, $id);
mysqli_stmt_execute($stmt);

echo 'Reservation ID <a href="ReservationDetails.php?&id=' . $id; //Link back to reservation
echo '">' . $id;
echo  '</a> updated. <a href="index.php">Go to home page</a>.';

mysqli_stmt_close($stmt);
mysqli_close($db);

?>
</body>
</html>


