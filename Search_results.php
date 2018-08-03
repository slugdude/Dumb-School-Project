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

$start_datetime = $_POST["Start_datetime"];
$end_datetime = $_POST["End_datetime"];
$surname = "%" . $_POST["Surname"] . "%";
$forename = "%" . $_POST["Forename"] . "%";
$email = "%" . $_POST["email"] . "%";
$tel_no = $_POST["Tel_No"];
$type = $_POST["type"];
$notes = "%". $_POST["notes"] . "%";

$db = mysqli_connect("localhost", "ReservationUser", "testPass", "ReservationSystem");

if (!$db) {
    echo "Connection failed:";
    exit;
}

if (empty($_POST["query"])) {

    //Can't have a start date without an end date and vice versa.
    if (empty($_POST["Start_datetime"]) and !empty($_POST["End_datetime"])) {
        echo "Start of time range must be specified if End of time range has been specified";
        exit;
    }

    if (!empty($_POST["Start_datetime"]) and empty($_POST["End_datetime"])) {
        echo "End of time range must be specified if Start of time range has been specified";
        exit;
    }

    $query = 'SELECT * FROM Reservations WHERE Surname LIKE ? AND Forename LIKE ? AND Email LIKE ?';

    if (!empty($_POST["Tel_No"])) { //Telephone number should be an exact match
        $query .= ' AND Tel_No = ?';
    }
    if (!empty($_POST["type"])) { //As should reservation type, as it's a dropdown list.
        $query .= ' AND Reservation_Type = ?';
    }
    $query .= ' AND Notes LIKE ?';


    if (!empty($_POST["Start_datetime"])) {


        //Format the dates as integers so they can be compared
        $start_datetime = substr($start_datetime, 0, 4) . substr($start_datetime, 5, 2) . substr($start_datetime, 8, 2) . substr($start_datetime, 11, 2) . substr($start_datetime, 14, 2);
        $end_datetime = substr($end_datetime, 0, 4) . substr($end_datetime, 5, 2) . substr($end_datetime, 8, 2) . substr($end_datetime, 11, 2) . substr($end_datetime, 14, 2);


        //append the date checking section to the query string
        $query .= ' AND CONCAT(DATE_FORMAT(Date, "%Y%m%d"), "", TIME_FORMAT(Start_time, "%H%i")) >= ? AND CONCAT(DATE_FORMAT(Date, "%Y%m%d"), "", TIME_FORMAT(Start_time, "%H%i")) <= ?';


    }

}

$stmt = mysqli_prepare($db, $query);


//This makes sure the correct parameters are bound depending on which fields the user specified to search for
switch (100 * (int)!empty($_POST["Tel_No"]) + 10 * (int)!empty($_POST["type"]) + (int)!empty($_POST["Start_datetime"])) {
    case 0:
        mysqli_stmt_bind_param($stmt, "ssss",  $surname,  $forename,  $email,  $notes );
        break;
    case 1:
        mysqli_stmt_bind_param($stmt, "ssssii",  $surname,  $forename,  $email,  $notes, $start_datetime, $end_datetime);
        break;
    case 10:
        mysqli_stmt_bind_param($stmt, "sssss",  $surname,  $forename,  $email, $type,  $notes );
        break;
    case 11:
        mysqli_stmt_bind_param($stmt, "sssssii",  $surname,  $forename,  $email, $type,  $notes, $start_datetime, $end_datetime);
        break;
    case 100:
        mysqli_stmt_bind_param($stmt, "sssss",  $surname,  $forename,  $email, $tel_no,  $notes );
        break;
    case 101:
       mysqli_stmt_bind_param($stmt, "sssssii",  $surname,  $forename,  $email, $tel_no,  $notes, $start_datetime, $end_datetime);
        break;
    case 110:
        mysqli_stmt_bind_param($stmt, "ssssss",  $surname,  $forename,  $email, $tel_no, $type,  $notes );
        break;
    case 111:
        mysqli_stmt_bind_param($stmt, "sssssii",  $surname,  $forename,  $email, $tel_no, $type,  $notes, $start_datetime, $end_datetime);
        break;
}

mysqli_stmt_execute($stmt);

mysqli_stmt_bind_result($stmt, $out_id, $out_date, $out_start, $out_end, $out_surname, $out_forename, $out_email, $out_tel, $out_type, $out_notes);

$head_p = '<p style="font-size:11px">';
$cell = '<td><p style="font-size:8px">';
echo "<table width=670px><tr><td>{$head_p}ID</p></td><td>{$head_p}Date</p></td><td>{$head_p}Start Time</p></td><td>{$head_p}End Time</p></td><td>{$head_p}Surname</p></td><td>{$head_p}Forename</p></td><td>{$head_p}Email</p></td><td>{$head_p}Telephone Number</p></td><td>{$head_p}Reservation Type</p></td><td width=100px>{$head_p}Notes</p></td></tr>";
echo '</p';
while (mysqli_stmt_fetch($stmt)) {
    echo "<tr>";

    $out_start = substr($out_start, 0, 5); //Don't output seconds
    $out_end = substr($out_end, 0, 5);

    $row = $cell . '<a href="ReservationDetails.php?&id=' . $out_id;
    $row .= '">' . $out_id . '</a></p></td>' . $cell . $out_date . '</p></td>' . $cell . $out_start . '</p></td>' . $cell . $out_end . '</p></td>' . $cell . $out_surname . '</p></td>' . $cell . $out_forename . '</p></td>' . $cell . $out_email . '</p></td>' . $cell . $out_tel . '</p></td>' . $cell . ucfirst($out_type) . '</p></td>' . $cell . $out_notes . '</p></td>';

    echo $row;

    echo "</tr>";

}
echo "</table>";

mysqli_stmt_close($stmt);
mysqli_close($db);

?>

</body>
</html>
