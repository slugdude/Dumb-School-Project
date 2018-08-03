<?php
session_start();

if (!isset($_SESSION['username']) or empty($_SESSION['username'])){
    header("location: login_form.php");
    exit;
}
if ($_SESSION['admin'] == 0) { //Admin only
    header("location: index.php");
    exit;
}

?>

<!DOCTYPE HTML>

<html>
<body>

<a href="delete_user_form.php">Go Back</a><br><br>

<?php

$db = mysqli_connect("localhost", "ReservationUser", "testPass", "ReservationSystem");

if (!$db) {
    echo "Connection failed:";
    exit;
}

if (empty($_POST["id"])) {

    $username = $_POST["username"];

    if ($username == "admin") {
        echo "Cannot delete admin!";
        exit;
    }

    $stmt = mysqli_prepare($db, "SELECT id FROM Users WHERE username=?"); //Find the user to be deleted
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id);

    if (!mysqli_stmt_fetch($stmt)) {
        echo "User " . $username . " does not exist!";
    } else {
        echo "Are you sure you want to delete user " . $username . " (ID " . $id . ")?<br>"; //Some validation.

        //The actual deleting is done on the same page as this, with a form that redirects back here, just makes the deletion a little more seamless.

        $form = '<table><tr><td><form method="post" action="delete_user.php"><input type="hidden" name="id" value="' . $id;
        $form .= '"><input type="submit" value="Yes"></form></td><td><form action="delete_user_form.php"><input type="submit" value="No"></form></td></tr></table>';
        echo $form;

    }
    mysqli_stmt_close($stmt);
} else { //id was specified from the form that the user submitted from this very same page, confirming the deletion.
    $id = $_POST["id"];
    $stmt = mysqli_prepare($db, "DELETE FROM Users WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "User deleted.";
}
?>
