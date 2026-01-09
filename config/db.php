<?php
if (session_status() == PHP_SESSION_NONE)
    session_start();

$conn = mysqli_connect("localhost", "root", "", "exam_form_system");
if(!$conn){
    die("Database connection failed");
}
?>
