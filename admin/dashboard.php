<?php
include("../config/db.php");
include("../header.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* Total submitted forms */
$total = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM exam_forms")
);

/* Approved */
$approved = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS approved FROM exam_forms WHERE status='Approved'")
);

/* Pending */
$pending = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS pending FROM exam_forms WHERE status='Pending'")
);

/* Rejected */
$rejected = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS rejected FROM exam_forms WHERE status='Rejected'")
);


?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
    <h3>Admin Dashboard</h3>

    <div style="margin-bottom:20px;">
        <p><strong>Total Forms Submitted:</strong> <?php echo $total['total']; ?></p>
        <p style='color:green'><strong>Approved:</strong> <?php echo $approved['approved']; ?></p>
        <p style='color:cyan'><strong>Pending:</strong> <?php echo $pending['pending']; ?></p>
        <p style='color:red'><strong>Rejected:</strong> <?php echo $rejected['rejected']; ?></p>
    </div>



    <div class="dashboard">
        <div class="card">
            <h3>Major Centres</h3>
            <p>Assign exam centres to majors</p>
            <a href="major_centres.php" class="btn">
                Manage Centres
            </a>
        </div>
        <div class="card">
            <h4>Student Applications</h4>
            <a href="students.php">Open</a>
        </div>

        <div class="card">
            <h4>Manage Subjects</h4>
            <a href="subjects.php">Open</a>
        </div>

        <div class="card">
            <h4>View Exam Forms</h4>
            <a href="forms.php">Open</a>
        </div>

        <div class="card">
            <h4>Set Submission Deadline</h4>
            <a href="deadline.php">Open</a>
        </div>

        <div class="card">
            <h4>Logout</h4>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</div>