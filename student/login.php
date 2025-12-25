<?php

include("../config/db.php");
include("../header.php");

$error = "";

if (isset($_POST['login'])) {
    $enroll = $_POST['enroll_no'];
    $dob = $_POST['dob'];

    $query = "SELECT * FROM students 
              WHERE enroll_no='$enroll' AND dob='$dob'";
    $res = mysqli_query($conn, $query);

    if (mysqli_num_rows($res) == 1) {
        $student = mysqli_fetch_assoc($res);
        $_SESSION['student_id'] = $student['id'];
        $_SESSION['student_name'] = $student['name'];

        header("Location: ../student/dashboard.php");
        exit();
    } else {
        $error = "Invalid Enrollment No or DOB";
    }
}
?>
<head>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<div class="container">
<h3>Student Login</h3>

<?php if ($error): ?>
    <p style="color:red;"><?php echo $error; ?></p>
<?php endif; ?>

<form method="post">
    Enrollment No:<br>
    <input type="text" name="enroll_no" required><br><br>

    Date of Birth:<br>
    <input type="date" name="dob" required><br><br>

    <button type="submit" name="login">Login</button>
</form>
</div>