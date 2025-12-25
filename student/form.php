<?php

include("../config/db.php");
include("../header.php");

/* =====================
   CHECK LOGIN
   ===================== */
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

/* =====================
   FETCH DEADLINE
   ===================== */
$setting = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT submission_deadline FROM settings WHERE id=1")
);

$deadline = $setting['submission_deadline'];
$current_time = date("Y-m-d H:i:s");
$submission_open = ($current_time <= $deadline);

/* =====================
   CHECK EXISTING FORM
   ===================== */
$check = mysqli_query(
    $conn,
    "SELECT * FROM exam_forms WHERE student_id=$student_id"
);
?>

<link rel="stylesheet" href="../assets/css/style.css">

<!-- =====================
     DEADLINE DISPLAY
     ===================== -->
<div class="container" style="margin-bottom:20px;">
    <p><strong>Exam Form Submission Deadline:</strong></p>

    <p style="font-size:16px;">
        <?php echo date("d M Y, h:i A", strtotime($deadline)); ?>
    </p>

    <?php if ($submission_open) { ?>
        <p style="color:green; font-weight:bold;">
            ✅ Submission is OPEN
        </p>
    <?php } else { ?>
        <p style="color:red; font-weight:bold;">
            ❌ Submission is CLOSED
        </p>
    <?php } ?>
</div>

<?php
/* =====================
   CASE 1: FORM EXISTS
   ===================== */
if (mysqli_num_rows($check) > 0) {

    $data = mysqli_fetch_assoc($check);
    $subjectsArray = explode(",", $data['subjects']);
?>

    <div class="container">
        <h3>Examination Form</h3>

        <?php if (!$submission_open) { ?>
            <p style="color:red; font-weight:bold;">
                ⚠️ Submission period is over. You cannot edit or delete this form.
            </p>
        <?php } ?>

        <p><strong>Status:</strong>
            <span class="status <?php echo $data['status']; ?>">
                <?php echo $data['status']; ?>
            </span>
        </p>

        <h4>Selected Subjects</h4>
        <ul>
            <?php foreach ($subjectsArray as $sub) { ?>
                <li><?php echo $sub; ?></li>
            <?php } ?>
        </ul>

        <?php if ($data['status'] == "Pending" && $submission_open) { ?>
            <br>

            <a href="edit_form.php">Edit Form</a>
            <a href="delete_form.php"
                onclick="return confirm('Are you sure you want to delete this form?')"
                style="color:red; margin-left:15px;">
                Delete Form
            </a>
        <?php }
        ?><br><br>
        <a href="print_form.php" target="_blank">
            🖨️ Print Examination Form
        </a>

    </div>

<?php
    exit();
}

/* =====================
   CASE 2: NO FORM + DEADLINE CLOSED
   ===================== */
if (!$submission_open) {
?>
    <div class="container" align="center">
        <h3>Form Submission Closed</h3>
        <p>The deadline for submitting the examination form has passed.</p>
    </div>
<?php
    exit();
}

/* =====================
   CASE 3: SUBMIT NEW FORM
   ===================== */
if (isset($_POST['submit'])) {

    if (!isset($_POST['subjects']) || count($_POST['subjects']) < 1) {
        echo "<div class='container' style='color:red; text-align:center;'>
                Please select at least one subject.
              </div>";
    } else {
        $subjects = implode(",", $_POST['subjects']);

        mysqli_query(
            $conn,
            "INSERT INTO exam_forms (student_id, subjects)
             VALUES ($student_id, '$subjects')"
        );

        header("Location: form.php");
        exit();
    }
}

/* =====================
   FETCH SUBJECTS
   ===================== */
$res = mysqli_query($conn, "SELECT * FROM subjects");
?>

<!-- =====================
     NEW FORM UI
     ===================== -->
<div class="container">
    <h3>Examination Form</h3>

    <div style="background:#f9f9f9; padding:15px; border-radius:6px; margin-bottom:20px;">
        <p><strong>Name:</strong> <?php echo $_SESSION['student_name']; ?></p>
        <p><strong>Enrollment No:</strong> <?php echo $_SESSION['student_id']; ?></p>
        <p><strong>Form Status:</strong>
            <span class="status Pending">Not Submitted</span>
        </p>
    </div>

    <form method="post">
        <h4>Select Subjects</h4>

        <?php while ($row = mysqli_fetch_assoc($res)) { ?>
            <div>
                <input type="checkbox" name="subjects[]"
                    value="<?php echo $row['subject_name']; ?>">
                <?php echo $row['subject_name']; ?>
            </div>
        <?php } ?>

        <br>
        <button type="submit" name="submit">
            Submit Examination Form
        </button>
    </form>
</div>