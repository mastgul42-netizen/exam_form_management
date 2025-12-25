<?php
include("../config/db.php");
include("../header.php");
    

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
    <h3>Welcome <?php echo $_SESSION['student_name']; ?></h3>

    <div class="dashboard">
        <div class="card">
            <h4>Fill/View Exam Form</h4>
            <a href="form.php">Open</a>
        </div>

        <div class="card">
            <h4>Logout</h4>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>

