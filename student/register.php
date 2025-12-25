<?php
include("../config/db.php");

$msg = "";

if (isset($_POST['register'])) {

    $enroll = mysqli_real_escape_string($conn, $_POST['enroll_no']);
    $name   = mysqli_real_escape_string($conn, $_POST['name']);
    $dob    = $_POST['dob'];

    // check duplicate enrollment
    $check = mysqli_query(
        $conn,
        "SELECT * FROM students WHERE enroll_no='$enroll'"
    );

    if (mysqli_num_rows($check) > 0) {
        $msg = "<p class='error'>Enrollment number already registered</p>";
    } else {
        mysqli_query(
            $conn,
            "INSERT INTO students (enroll_no, name, dob)
             VALUES ('$enroll', '$name', '$dob')"
        );

        $msg = "<p class='success'>
Registration successful. Redirecting to login...
</p>
<script>
    setTimeout(function() {
        window.location.href = 'login.php';
    }, 2000);
</script>";
    }
}
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
    <h3>Student Registration</h3>

    <?php echo $msg; ?>

    <form method="post">
        <label>Enrollment Number</label>
        <input type="text" name="enroll_no" required>

        <label>Full Name</label>
        <input type="text" name="name" required>

        <label>Date of Birth</label>
        <input type="date" name="dob" required>

        <button type="submit" name="register">Register</button>
    </form>

    <br>
    <a href="login.php">Already registered? Login</a>
</div>