<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$BASE_URL = "/exam";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Exam Form Management System</title>
    <link rel="stylesheet" href="<?= $BASE_URL ?>/assets/css/style.css">
</head>
<body>

<div style="background: rgba(6, 222, 49, 0.6); padding:12px;">
    <div style="max-width:1000px; margin:auto; display:flex; align-items:center;">
        
        <h3 style="color:white;">Exam Form System</h3>

        <div style="margin-left:auto;">
            <a href="<?= $BASE_URL ?>/" style="color:white; margin-right:15px;">Home</a>

            <?php if (isset($_SESSION['admin'])) { ?>
                <a href="<?= $BASE_URL ?>/admin/dashboard.php" style="color:white; margin-right:15px;">Dashboard</a>
                <a href="<?= $BASE_URL ?>/admin/logout.php" style="color:white;">Logout</a>

            <?php } elseif (isset($_SESSION['student_id'])) { ?>
                <a href="<?= $BASE_URL ?>/student/dashboard.php" style="color:white; margin-right:15px;">Dashboard</a>
                <a href="<?= $BASE_URL ?>/student/logout.php" style="color:white;">Logout</a>

            <?php } else { ?>
                <a href="<?= $BASE_URL ?>/admin/login.php" style="color:white; margin-right:15px;">Admin</a>
                <a href="<?= $BASE_URL ?>/student/" style="color:white;">Student</a>
            <?php } ?>
        </div>
    </div>
</div>
