<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

mysqli_query($conn,
    "DELETE FROM exam_forms 
     WHERE student_id=$student_id AND status='Pending'"
);

header("Location: form.php");
exit();
