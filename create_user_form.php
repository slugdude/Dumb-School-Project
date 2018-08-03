<?php
session_start();

if (!isset($_SESSION['username']) or empty($_SESSION['username'])){
    header("location: login_form.php");
    exit;
}
if ($_SESSION['admin'] == 0) {
    header("location: index.php");
    exit;
}

?>

<!DOCTYPE HTML>

<html>
<body>

<a href="admin.php">Go Back</a><br><br>

Create new Account:<br><br>

<form method="POST" action="create_user.php">

<table>

    <tr>
        <td>Username: </td>
        <td><input type="text" name="username"></input></td>
    </tr>
    <tr>
        <td>Password: </td>
        <td><input type="password" name="password"></input></td>
    </tr>
    <tr>
        <td>Confirm Password: </td>
        <td><input type="password" name="confirmPassword"></input></td>
    </tr>

</table>

<input type="submit">

</form>


</body>
</html>
