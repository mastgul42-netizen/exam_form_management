<?php
include("../config/db.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

/* Fetch form */
$res = mysqli_query(
    $conn,
    "SELECT * FROM exam_forms WHERE student_id=$student_id"
);

if (mysqli_num_rows($res) == 0) {
    echo "No form found.";
    exit();
}

$data = mysqli_fetch_assoc($res);
$subjects = explode(",", $data['subjects']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Examination Form</title>

    <style>
        body {
            font-family: "Times New Roman", serif;
            background: #fff;
            margin: 40px;
        }

        .box {
            border: 2px solid #000;
            padding: 20px;
        }

        h2, h3 {
            text-align: center;
            margin-bottom: 10px;
        }

        .row {
            margin-bottom: 10px;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 200px;
        }

        ul {
            margin-top: 10px;
        }

        .footer {
            margin-top: 60px;
            display: flex;
            justify-content: space-between;
        }

        .sign {
            text-align: center;
            width: 200px;
        }

        @media print {
            button {
                display: none;
            }
        }
    </style>
</head>

<body>

<button onclick="window.print()">Print / Save as PDF</button>

<div class="box">
    <h2>UNIVERSITY EXAMINATION FORM</h2>
    <h3>Academic Session 2024-25</h3>

    <hr>

    <div class="row">
        <span class="label">Student Name:</span>
        <?php echo $_SESSION['student_name']; ?>
    </div>

    <div class="row">
        <span class="label">Enrollment No:</span>
        <?php echo $_SESSION['student_id']; ?>
    </div>

    <div class="row">
        <span class="label">Form Status:</span>
        <?php echo $data['status']; ?>
    </div>

    <hr>

    <h3>Subjects Applied For</h3>

    <ul>
        <?php foreach ($subjects as $sub) { ?>
            <li><?php echo $sub; ?></li>
        <?php } ?>
    </ul>

    <div class="footer">
        <div class="sign">
            ___________________<br>
            Student Signature
        </div>

        <div class="sign">
            ___________________<br>
            Controller of Examinations
        </div>
    </div>
</div>

</body>
</html>
