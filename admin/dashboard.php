<?php
include("../config/db.php");
include("../header.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* ===============================
   EXAM FORM STATS
   =============================== */
$total = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM exam_forms")
);

$approved = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS approved FROM exam_forms WHERE status='Approved'")
);

$pending = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS pending FROM exam_forms WHERE status='Pending'")
);

$rejected = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS rejected FROM exam_forms WHERE status='Rejected'")
);

/* ===============================
   STUDENT REGISTRATION STATS
   =============================== */
$pending_students = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS pending_students 
         FROM students 
         WHERE status='Pending'"
    )
);

/* ===============================
   REGISTERED BUT NOT SUBMITTED
   =============================== */
$not_submitted = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT COUNT(*) AS not_submitted
         FROM students
         WHERE status='Approved'
         AND id NOT IN (
             SELECT student_id FROM exam_forms
         )"
    )
);
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h3>Admin Dashboard</h3>
        <span>Overview & Quick Actions</span>
    </div>

    <!-- ===============================
         STATS DASHBOARD
         =============================== -->
    <div class="dashboard stats-dashboard">

        <div class="dashboard-card highlight">
            <h2><?= $total['total']; ?></h2>
            <p>Total Exam Forms</p>
            <span class="stat-sub">Overall submissions received</span>
        </div>

        <div class="dashboard-card stat-approved">
            <h2><?= $approved['approved']; ?></h2>
            <p>Approved Forms</p>
            <span class="stat-sub">Ready for examination</span>
        </div>

        <div class="dashboard-card stat-pending">
            <h2><?= $pending['pending']; ?></h2>
            <p>Pending Exam Forms</p>
            <span class="stat-sub">Awaiting admin review</span>
        </div>

        <div class="dashboard-card stat-rejected">
            <h2><?= $rejected['rejected']; ?></h2>
            <p>Rejected Forms</p>
            <span class="stat-sub">Need correction</span>
        </div>

        <div class="dashboard-card stat-pending">
            <h2><?= $pending_students['pending_students']; ?></h2>
            <p>Pending Student Registrations</p>
            <span class="stat-sub">Awaiting enrollment approval</span>
            <a href="students.php">Review Now →</a>
        </div>

        <!-- ✅ NEW CARD -->
        <div class="dashboard-card stat-pending">
            <h2><?= $not_submitted['not_submitted']; ?></h2>
            <p>Registered but Not Submitted</p>
            <span class="stat-sub">Approved students without exam form</span>
        </div>

    </div>

    <!-- ===============================
         QUICK MANAGEMENT ACTIONS
         =============================== -->
    <div class="table-section">
        <h4>Quick Management</h4>

        <div class="dashboard action-dashboard">

            <div class="dashboard-card">
                <h4>Exam Forms</h4>
                <p>Review and approve exam forms</p>
                <a href="forms.php" class="btn">Open</a>
            </div>

            <div class="dashboard-card">
                <h4>Student Applications</h4>
                <p>Approve or reject student registrations</p>
                <a href="students.php" class="btn">Open</a>
            </div>

            <div class="dashboard-card">
                <h4>Manage Subjects</h4>
                <p>Add or update subject structure</p>
                <a href="subjects.php" class="btn">Open</a>
            </div>

            <div class="dashboard-card">
                <h4>Submission Deadline</h4>
                <p>Control exam form submission window</p>
                <a href="deadline.php" class="btn">Open</a>
            </div>

            <div class="dashboard-card">
                <h4>Major Centres</h4>
                <p>Assign exam centres to majors</p>
                <a href="major_centres.php" class="btn">Manage Centres</a>
            </div>

        </div>
    </div>

</div>
