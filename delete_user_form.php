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

Delete Account:<br><br>

<form method="POST" action="delete_user.php">

<table>

    <tr>
        <td>Username: </td>
        <td><input type="text" name="username"></input></td>
    </tr>

</table>

<input type="submit">

</form>

</body>
</html>
