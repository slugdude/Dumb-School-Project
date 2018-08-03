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
$date = $_POST["date"];
$start_time = $_POST["Start_time"];
$end_time = $_POST["End_time"];
$surname = $_POST["Surname"];
$forename = $_POST["Forename"];
$email = $_POST["email"];
$tel_no = $_POST["Tel_No"];
$type = $_POST["type"];
$notes = $_POST["notes"];

$db = mysqli_connect("localhost", "ReservationUser", "testPass", "ReservationSystem");

if (!$db) {
    echo "Connection failed:";
    exit;
}

if ($type == "internal" and (!empty($surname) or !empty($forename) or !empty($email) or !empty($tel_no))) {
    echo 'Contact details should not be specified for internal reservations. <a href="add_reservation_form.php">Go Back</a>';
    exit;
} elseif ($type == "external" and (empty($surname) or empty($forename) or empty($email) or empty($tel_no))) {
    echo 'Contact details for external reservation incomplete. <a href="add_reservation_form.php">Go Back</a>';
    exit;
}

//Email format and date/times are validated by the browser due to the use of the email, date and time input tags, so no need to validate again here.

//only allow if:
//start_time <= existing_start and end_time <= existing_start       (new reservation precedes old)
//or
//start_time >= existing_end and end_time >= existing_end           (new reservation succeeds old)

//Any other combination is invalid since every valid reservation should either precede or succede existing
//reservations. If there are no reservations that day, it should be valid anyway

//Technically this allows for 0 length reservations, however an error here will not be useful to the user.
//That should be validated separately.

$stmt = mysqli_prepare($db, 'SELECT ReservationID, TIME_FORMAT(Start_time, "%H%i"), TIME_FORMAT(End_time,"%H%i") FROM Reservations WHERE Date=?');
mysqli_stmt_bind_param($stmt, "s", $date);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $check_id, $check_start, $check_end);

while (mysqli_stmt_fetch($stmt)) {
    if (!(((int)str_replace(":","",$start_time) <= $check_start and (int)str_replace(":","",$end_time) <= $check_start) or ((int)str_replace(":","",$start_time) >= $check_end and (int)str_replace(":","",$end_time) >= $check_end))) { //If the new reservation clashes with an existing one

        $clashError = 'This reservation clashes with reservation <a href="ReservationDetails.php?&id=' . $check_id;
        $clashError .= '">' . $check_id . '</a> (' . substr($check_start, 0, 5) . '-' . substr($check_end, 0, 5) . ').';
        $clashError .= ' <a href="index.php">Go to home page</a>';
        echo $clashError;
        exit;

    }

}
mysqli_stmt_close($stmt);

//Create the new reservation

$stmt = mysqli_prepare($db, "INSERT INTO Reservations (Date,Start_time,End_time,Surname,Forename,Email,Tel_No,Reservation_Type,Notes) VALUES (?,?,?,?,?,?,?,?,?)");
mysqli_stmt_bind_param($stmt, "sssssssss", $date, $start_time, $end_time, $surname, $forename, $email, $tel_no, $type, $notes);
mysqli_stmt_execute($stmt);

$new_ID = mysqli_insert_id($db);

if ($new_ID == 0) { //Last ID is set to 0 if nothing was inserted, so that means an error occurred

    echo "Creating new reservation failed.<br>"; //Can happen if, for example, the date is invalid. Though this should be validated.

} else {

    echo 'Reservation created with ID <a href="ReservationDetails.php?&id=' . $new_ID; //Link to new reservation incase the user needs to check it.
    echo '">' . $new_ID;
    echo  '</a> <a href="index.php">Go to home page</a>.';

}

mysqli_stmt_close($stmt);
mysqli_close($db);

?>
</body>
</html>
