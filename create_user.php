<?php
session_start();

if (!isset($_SESSION['username']) or empty($_SESSION['username'])){
    header("location: login_form.php");
    exit;
}
if ($_SESSION['admin'] == 0) { //Only admin
    header("location: index.php");
    exit;
}
?>

<!DOCTYPE HTML>
<html>
<body>


<?php

$exit = 0;
if (empty($_POST["username"])) {
    echo "Please enter a username.<br><br>";
    $exit = 1;
}
if (empty($_POST["password"])) {
    echo "Please enter a password.<br><br>";
    $exit = 1;
} elseif (strlen($_POST["password"]) < 8) {
    echo "Password must be at least 8 characters.<br><br>";
    $exit = 1;
} elseif ($_POST["password"] != $_POST["confirmPassword"]) {
    echo "Passwords must match.<br><br>";
    $exit = 1;
}

?>

<a href="create_user_form.php">Go Back</a><br><br>

<?php

if ($exit) {
    exit;
}

$db = mysqli_connect("localhost", "ReservationUser", "testPass", "ReservationSystem");

if (!$db) {
    echo "Connection failed:";
    exit;
}

$stmt = mysqli_prepare($db, "SELECT username FROM Users WHERE username=?"); //Make sure there are no users with the same name
mysqli_stmt_bind_param($stmt, "s", $_POST["username"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $existing_user);

if (!mysqli_stmt_fetch($stmt)) { //If no users with that username were found (since username must be unique)
    mysqli_stmt_close($stmt);
    $stmt = mysqli_prepare($db, 'INSERT INTO Users (username,password) VALUES (?,?)');
    mysqli_stmt_bind_param($stmt, "ss", $_POST["username"],password_hash($_POST["password"], PASSWORD_DEFAULT));
    mysqli_stmt_execute($stmt);
    echo "User Created";

} else {
    echo "Username already exists.";
}
mysqli_stmt_close($stmt);
mysqli_close();

?>

</body>
</html>
