<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

mysqli_query($conn, "DELETE FROM exam_forms WHERE id=$id");

header("Location: forms.php");
exit();
