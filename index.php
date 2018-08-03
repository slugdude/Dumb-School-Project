<?php

//Create admin account if it doesn't exist

$db = mysqli_connect("localhost", "ReservationUser", "testPass", "ReservationSystem");

if (!$db) {
    echo "Connection failed:";
    exit;
}

$stmt = mysqli_prepare($db, 'SELECT id FROM Users WHERE username="admin"');
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $adminid);

if (!mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    $stmt = mysqli_prepare($db, 'INSERT INTO Users (username,password) VALUES ("admin",?)');
    $defaultPass = "rougemont";
    mysqli_stmt_bind_param($stmt, "s", password_hash("rougemont", PASSWORD_DEFAULT)); //Default password, user will be prompted to change this on login.
    mysqli_stmt_execute($stmt);
}
mysqli_stmt_close($stmt);

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
<a href="logout.php">Log Out</a>&emsp;<a href="change_password_form.php">Change Password</a>

<?php

if ($_SESSION["admin"] == 1) {
    echo '&emsp;<a href="admin.php">Manage Accounts</a>';
}

?>


<br>
<br>
<a href="printer_friendly_form.php">Printer-friendly Week View</a>&emsp;<a href="add_reservation_form.php">Add new reservation</a>

<?php

echo "<p>";

$month = "";
$month_timestamp = "";


//Find the timestamp required to display the desired month on the calendar.
if (empty($_GET["month"]) and empty($_GET["month_timestamp"])) {    //Nothing specified, use current month
    $month = date("Y-m");
    $month_timestamp = time();
} else if (empty($_GET["month_timestamp"]) and !(empty($_GET["month"]))) { //Month given as date, from the details page or the jump to form
    $month_year = $_GET["month"];
    $year_num = substr($month_year, 0, 4);
    $month_num = substr($month_year, 5, 2);
    $month_timestamp = mktime(0, 0, 1, $month_num, 1, $year_num);
} else { //Timestamp given directly
    $month_timestamp = $_GET["month_timestamp"];
    $month = date("Y-m", $month_timestamp);
}

$first_day_timestamp = mktime(0, 0, 1, date("n", $month_timestamp), 1, date("Y", $month_timestamp)); //Find the timestamp of the first day of the month


$weekday = intval(date("w", $first_day_timestamp)) - 1; //What day of the week the first day of the month falls on
if ($weekday == -1) { //makes monday == 0, through to sunday which is 6, instead of the default.
    $weekday = 6;
}

$week_timestamp = $first_day_timestamp - (24*60*60) * $weekday; //Start printing the calendar from the last week of the previous month (unless this month starts on Monday, in which case $weekday is 0.

//Find the timestamps of the previous and next months, for the arrow nav buttons

if (date("n", $month_timestamp) == 1) { //Month before January needs to wrap back to december, decrementing the year.
    $last_month_timestamp = mktime(0, 0, 0, 12, date("d", $month_timestamp), date("Y", $month_timestamp)-1);
} else { //otherwise just the last month as usual
    $last_month_timestamp = mktime(0, 0, 0, date("m", $month_timestamp)-1, date("d", $month_timestamp), date("Y", $month_timestamp));
}

if (date("n", $month_timestamp) == 12) { //Month after December needs to wrap to January of the next year
    $next_month_timestamp = mktime(0, 0, 0, 1, date("d", $month_timestamp), date("Y", $month_timestamp)+1);
} else { //otherwise just the next month as usual
    $next_month_timestamp = mktime(0, 0, 0, date("m", $month_timestamp)+1, date("d", $month_timestamp), date("Y", $month_timestamp));
}

?>
<form action="/index.php">

<a href="index.php">Back to current month</a>&emsp;Jump to month:

    <input type="month" name="month">
    <input type="submit" value="Go">


</form>

<br>
<br>

<table>

<?php

$td = "<td width=120px>"; //Sets the width of the columns so that the calendar doesn't look disproportinate.

//Month nav row will be at the top of the table, with the arrow buttons on either side and the month name and year in the middle
$month_nav_row = "<tr>{$td}<a href=index.php?&month_timestamp=" . $last_month_timestamp . "><--</a></td>{$td}</td>{$td}</td>{$td}<b>" . date("F", $month_timestamp) . " " . date("Y", $month_timestamp) . "</b></td>{$td}</td>{$td}</td>{$td}<a href=index.php?&month_timestamp=" . $next_month_timestamp . ">--></a></td></tr>";

echo $month_nav_row;

//The day of the week headings, in the next row.
echo "<tr> <td>Monday</td><td>Tuesday</td><td>Wednesday</td><td>Thursday</td><td>Friday</td><td>Saturday</td><td>Sunday</td> </tr>";

$stop = 0;
$looped_once = 0;

//Prints the bulk of the calendar
//There are two stopping conditions - if the next week's day isnt bigger than the end of last weeks (in other words, the month ends on a sunday
//This stopping condition is the one in the while loop.
//The other in the if statement at the end of the loop.
while (((date("j", $week_timestamp - (24*60*60)) <= date("j", $week_timestamp)) and $stop == 0) or $looped_once == 0) { // '<=' sign needs to be used to fix an off-by-one error caused by daylight savings time

    $day = 0;

    echo "<tr>";
    while ($day <= 6) {
        $bold = "";
        $unbold = "";
        if (date("Y-m-d", $week_timestamp + (24 * 60 * 60) * $day) == date("Y-m-d")) { //Makes the current day in the calendar appear in bold text
             $bold = "<b>";
             $unbold = "</b>";
        }

        echo "<td>" . $bold . date("j", $week_timestamp + (24 * 60 * 60) * $day) . $unbold . "</td>"; //Prints each day in it's own cell.
        $day += 1;
    }
    echo "</tr>";
    echo "<tr>";

    $day = 0;

    while ($day <= 6) { //This is for displaying the reservations that fall within each week, below the day number.
        $formatted_day = date("Y-m-d", $week_timestamp + (24 * 60 * 60) * $day);

        $stmt = mysqli_prepare($db, "SELECT ReservationID, Start_time, End_time FROM Reservations WHERE Date=? ORDER BY Start_time");
        mysqli_stmt_bind_param($stmt, "s", $formatted_day);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id, $start_time, $end_time);

        echo "<td>";

        $cell_filled = 0;

        while (mysqli_stmt_fetch($stmt)) { //Each reservation that falls onthis day.

            $line = 0;

            $bold = "";
            $unbold = "";

            if ($id == $_GET['id']) { //If the user came here from the reservation details page and clicked the view in calendar link, this makes it appear in bold.
                $bold = "<b>";
                $unbold = "</b>";
            }

            $printline = $bold . '<a href="';
            $printline .= 'ReservationDetails.php?&id=' . $id . '">'; //Each listing is also a link to it's own details page.
            $printline .= $id . ": " . substr($start_time, 0, 5) . "-" . substr($end_time, 0, 5) . "</a>" . $unbold;
            echo $printline; //Reservation links are in the format 'ID: StartTime-EndTime'
            $cell_filled = 1;

            echo "<br>";

        }
        mysqli_stmt_close($stmt);
        if($cell_filled == 0) {
            echo "&nbsp;"; //Makes sure each day has at least one line below it to stop empty weeks from bunching together
        }

        echo "</td>";

        $day += 1;

    }

    echo "</tr>";

    if (date("j", $week_timestamp) >= date("j", $week_timestamp + (6*24*60*60)) and $looped_once == 1) { //Unless it's the first week, in which we print the end of last month, if the day beggining of the week has a bigger number than the end, then this is the end of the month.
        $stop = 1;
    }
    $week_timestamp += (7*24*60*60); //Next week
    $looped_once = 1;

}

mysqli_close($db);

?>

</table>

</p>
<br>
<p>

<form action="/ReservationDetails.php" method="get">

Find reservation by ID: <input type="number" name="id" min=0> <input type="submit" value="Find">

</form>
<br>
<a href="/Search.php">Advanced search</a>

</p>
</body>
</html>
