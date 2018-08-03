<?php

session_start();
if (isset($_SESSION["username"]) and !empty($_SESSION["username"])) {
    header("location: index.php");
}

?>

<!DOCTYPE HTML>
<html>
<body>

<?php

$db = mysqli_connect("localhost", "ReservationUser", "testPass", "ReservationSystem");

$stmt = mysqli_prepare($db, "SELECT password FROM Users WHERE username=?");
mysqli_stmt_bind_param($stmt, "s", $_POST["username"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $passwordHash);

if (mysqli_stmt_fetch($stmt)) {

    if (password_verify($_POST["password"], $passwordHash)) { //Password correct?

        $_SESSION["username"] = $_POST["username"];
        if ($_SESSION["username"] == "admin") { //Are they admin?
            $_SESSION["admin"] = 1;
        } else {
            $_SESSION["admin"] = 0;
        }
        echo "Logged in as " . $_SESSION["username"] . '.<br><br>';
        echo '<a href="index.php">Continue</a>';

        if ($_POST["password"] == "test") { //Password is set to the default

            echo '<br><br>You should change your password. <a href="change_password_form.php">Change Password</a>';

        }

    } else {

        echo 'Invalid password!<br><br><a href="login_form.php">Go Back</a>';

    }

} else {

    echo 'User not found!<br><br><a href=login_form.php>Go Back</a>';

}
mysqli_stmt_close($stmt);
mysqli_close($db);
?>

</body>
</html>
