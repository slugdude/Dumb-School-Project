<?php
session_start();

if (!isset($_SESSION['username']) or empty($_SESSION['username'])){
    header("location: login_form.php");
    exit;
}
?>

<!DOCTYPE HTML>
<html>
<body>

<?php
$db = mysqli_connect("localhost", "ReservationUser", "testPass", "ReservationSystem");

$stmt = mysqli_prepare($db, "SELECT password FROM Users WHERE username=?");
mysqli_stmt_bind_param($stmt, "s", $_SESSION["username"]);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $passwordHash);
mysqli_stmt_fetch($stmt);

if ($_POST["newPassword"] != $_POST["confirmPassword"]) { //Make sure the user didn't typo their password, that would be bad!

    echo 'Passwords do not match! <a href="change_password_form.php">Go Back</a>.';

} elseif (strlen($_POST["newPassword"]) < 8) {

    echo "Password must be at least 8 characters.";

} elseif  (password_verify($_POST["oldPassword"], $passwordHash)) { //Means if they left their account logged in, someone still can't change the pass.
    mysqli_stmt_close($stmt);
    $stmt = mysqli_prepare($db, 'UPDATE Users SET password=? WHERE username=?');
    mysqli_stmt_bind_param($stmt, "ss", password_hash($_POST["newPassword"], PASSWORD_DEFAULT), $_SESSION["username"]);
    mysqli_stmt_execute($stmt);
    echo "Password changed.";

} else {
    echo 'Old password incorrect! <a href="change_password_form.php">Go Back</a>.';
}

mysqli_stmt_close($stmt);


?>

<br><br>
<a href="index.php">Return to main page</a>

</body>
</html>
