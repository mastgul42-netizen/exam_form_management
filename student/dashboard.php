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

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h3>Student Dashboard</h3>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['student_name']); ?></span>
    </div>

    <!-- STUDENT ACTIONS -->
    <div class="dashboard action-dashboard">

        <div class="dashboard-card">
            <h4>Examination Form</h4>
            <p>Fill, view, or update your exam form</p>
            <a href="form.php" class="btn">Open</a>
        </div>

        <div class="dashboard-card">
            <h4>Print Form</h4>
            <p>print your submitted exam form details</p>
            <a href="print_form.php" class="btn">View</a>
        </div>

        <div class="dashboard-card">
            <h4>Logout</h4>
            <p>Securely log out of your account</p>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

    </div>

</div>
