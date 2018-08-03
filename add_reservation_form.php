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

<a href="index.php">Go Back</a>
<br>

<form action="/insert.php" method="post" id="insertData">
<table>
    <tr>
        <td>Date:</td>
        <td><input type="date" name="date"></td>
    </tr>
    <tr>
        <td>Start time:</td>
        <td><input type="time" name="Start_time"></td>
    </tr>
    <tr>
        <td>End time:</td>
        <td><input type="time" name="End_time"></td>
    </tr>
    <tr>
        <td>Surname:</td>
        <td><input type="text" name="Surname"></td>
    </tr>
    <tr>
        <td>Forename:</td>
        <td><input type="text" name="Forename"></td>
    </tr>
    <tr>
        <td>Email:</td>
        <td><input type="email" name="email"></td>
    </tr>
    <tr>
        <td>Telephone Number:</td>
        <td><input type="text" name="Tel_No"></td>
    </tr>
    <tr>
        <td>Reservation Type:</td>
        <td>
            <select name="type" form="insertData">
                <option value="external">External</option>
                <option value="internal">Internal</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>Notes:</td>
        <td><input type="text" name="notes"></td>
    </tr>

</table>
<input type="submit" value="Add">
</form>

</body>
</html>
