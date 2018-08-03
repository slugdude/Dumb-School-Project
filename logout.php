<?php
session_start();

if (isset($_SESSION['username']) and !empty($_SESSION['username'])){
    session_unset(); //Unset session vars so the other pages know they logged out
    session_destroy();
}
header("location: login_form.php"); //Back to login page
?>
