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
<form method="POST" action="change_password.php">
<table>

    <tr>
        <td>Old Password: </td>
        <td><input type="password" name="oldPassword"></td>
    </tr>
    <tr>
        <td>New Password: </td>
        <td><input type="password" name="newPassword"></td>
    </tr>
    <tr>
        <td>Confirm New Password: </td>
        <td><input type="password" name="confirmPassword"></td>
    </tr>

</table>

<input type="submit">
</form>

</body>
</html>
