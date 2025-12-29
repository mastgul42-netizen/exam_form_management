<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$BASE_URL = "/exam";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam Form Management System</title>
    <link rel="stylesheet" href="<?= $BASE_URL ?>/assets/css/style.css">
</head>

<body>

<header class="main-header">
    <div class="header-inner">

        <!-- LOGO / TITLE -->
        <div class="logo">
            <a href="<?= $BASE_URL ?>/">Exam Form System</a>
        </div>

        <!-- NAVIGATION -->
        <nav class="main-nav">

            <a href="<?= $BASE_URL ?>/">Home</a>

            <?php if (isset($_SESSION['admin'])) { ?>
                <a href="<?= $BASE_URL ?>/admin/dashboard.php">Dashboard</a>
                <a href="<?= $BASE_URL ?>/admin/logout.php" class="logout">Logout</a>

            <?php } elseif (isset($_SESSION['student_id'])) { ?>
                <a href="<?= $BASE_URL ?>/student/dashboard.php">Dashboard</a>
                <a href="<?= $BASE_URL ?>/student/logout.php" class="logout">Logout</a>

            <?php } else { ?>
                <a href="<?= $BASE_URL ?>/admin/login.php">Admin</a>
                <a href="<?= $BASE_URL ?>/student/">Student</a>
            <?php } ?>

        </nav>

    </div>
</header>
