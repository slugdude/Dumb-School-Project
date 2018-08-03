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

if (empty($_GET['week'])) {
    echo "No week specified. <a href=printer_friendly_form.html>Go Back.</a>";
    exit;
}

$db = mysqli_connect("localhost", "ReservationUser", "testPass", "ReservationSystem");

if (!$db) {
    echo "Connection failed:";
    exit;
}

$year = substr($_GET['week'], 0, 4);
$week = substr($_GET['week'], 6, 2);

$date_start = date("Y-m-d", mktime(0, 0, 0, 1, ($week - 1)*7 + 1, $year)); //Date range to query, in the correct format.
$date_end = date("Y-m-d", mktime(0, 0, 0, 1, ($week - 1)*7 + 7, $year));

$query = 'SELECT * FROM Reservations WHERE Date >= ? AND Date <= ? ORDER BY Date, Start_time;';

$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "ss", $date_start, $date_end);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $out_id, $out_date, $out_start, $out_end, $out_surname, $out_forename, $out_email, $out_tel, $out_type, $out_notes);

$head_p = '<p style="font-size:11px">'; //Font size for the table headings
$cell = '<td><p style="font-size:8px">';

//Headings row
echo "<table width=670px><tr><td>{$head_p}ID</p></td><td>{$head_p}Date</p></td><td>{$head_p}Start Time</p></td><td>{$head_p}End Time</p></td><td>{$head_p}Surname</p></td><td>{$head_p}Forename</p></td><td>{$head_p}Email</p></td><td>{$head_p}Telephone Number</p></td><td>{$head_p}Reservation Type</p></td><td width=100px>{$head_p}Notes</p></td></tr>";

while (mysqli_stmt_fetch($stmt)) { //Output each row

    echo "<tr>";

    $out_start = substr($out_start, 0, 5); //Don't output seconds
    $out_end = substr($out_end, 0, 5);

    echo $cell . $out_id . '</p></td>' . $cell . $out_date . '</p></td>' . $cell . $out_start . '</p></td>' . $cell . $out_end . '</p></td>' . $cell . $out_surname . '</p></td>' . $cell . $out_forename . '</p></td>' . $cell . $out_email . '</p></td>' . $cell . $out_tel . '</p></td>' . $cell . ucfirst($out_type) . '</p></td>' . $cell . $out_notes . '</p></td>';

    echo "</tr>";

}
echo "</table>";

mysqli_close($db);

?>

</body>
</html>
