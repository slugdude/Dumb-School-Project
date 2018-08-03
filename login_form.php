<?php
session_start();

if (isset($_SESSION['username']) and !empty($_SESSION['username'])){
    header("location: index.php");
    exit;
}
?>
<!DOCTYPE HTML>

<html>
<body>

<form method="POST" action="login.php">

<table>

    <tr>
        <td>Username: </td>
        <td><input type="text" name="username"></input></td>
    </tr>
    <tr>
        <td>Password: </td>
        <td><input type="password" name="password"></input></td>
    </tr>

</table>

<input type="submit">

</form>

