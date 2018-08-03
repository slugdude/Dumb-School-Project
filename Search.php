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

<form action="/Search_results.php" method="post" id="search">
<table>
    <tr>
        <td>Start of time range:</td>
        <td><input type="datetime-local" name="Start_datetime"></td>
    </tr>
    <tr>
        <td>End of time range:</td>
        <td><input type="datetime-local" name="End_datetime"></td>
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
            <select name="type" form="search">
                <option value=""></option>
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
<input type="submit" value="Search">
</form>

</body>
</html>
