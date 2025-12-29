<?php include("header.php"); ?>
<link rel="stylesheet" href="assets/css/style.css">

<div class="container">

    <!-- PAGE HEADER -->
    <div class="page-header">
        <h3>Welcome to Examination Portal</h3>
        <span>Select your role to continue</span>
    </div>

    <!-- ENTRY POINTS -->
    <div class="dashboard action-dashboard">

        <div class="dashboard-card">
            <h4>Administrator</h4>
            <p>Login to manage students, subjects, forms, and centres</p>
            <a href="admin/login.php" class="btn">Admin Login</a>
        </div>

        <div class="dashboard-card">
            <h4>Student Zone</h4>
            <p>Register, login, fill exam form, and view centre details</p>
            <a href="student/" class="btn">Enter Student Zone</a>
        </div>

    </div>

</div>

<?php include("footer.php"); ?>
