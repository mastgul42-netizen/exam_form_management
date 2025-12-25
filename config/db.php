<?php
session_start();

$conn = mysqli_connect("localhost", "root", "", "exam_form_system");
if(!$conn){
    die("Database connection failed");
}
?>
