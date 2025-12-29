<?php
include("../config/db.php");

/* ===============================
   LOGIN CHECK
   =============================== */
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

/* ===============================
   FETCH STUDENT DETAILS
   =============================== */
$student = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT name, enroll_no, semester, selected_subjects
         FROM students
         WHERE id = $student_id"
    )
);

/* ===============================
   SUBMISSION SETTINGS (DEADLINE)
   =============================== */
$setting = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT submission_deadline FROM settings WHERE id = 1"
    )
);

$deadline = $setting['submission_deadline'] ?? null;
$current_time = date("Y-m-d H:i:s");
$submission_open = (!$deadline || $current_time <= $deadline);

/* ===============================
   CHECK IF FORM EXISTS
   =============================== */
$formRes = mysqli_query(
    $conn,
    "SELECT * FROM exam_forms WHERE student_id = $student_id"
);

$form = mysqli_fetch_assoc($formRes);
$form_status = $form['status'] ?? 'Not Submitted';

/* ===============================
   HANDLE FORM SUBMISSION
   =============================== */
if (isset($_POST['submit']) && $form_status === 'Not Submitted') {

    if (!$submission_open) {
        $error = "Form submission deadline has passed.";
    } else {
        mysqli_query(
            $conn,
            "INSERT INTO exam_forms (student_id, status)
             VALUES ($student_id, 'Pending')"
        );
        header("Location: form.php");
        exit();
    }
}
?>

<link rel="stylesheet" href="../assets/css/style.css">

<!-- ===============================
     STUDENT INFO CARD
     =============================== -->
<div class="container" style="margin-bottom:20px;">
    <h3>Examination Form</h3>

    <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
    <p><strong>Enrollment No:</strong> <?php echo htmlspecialchars($student['enroll_no']); ?></p>
    <p><strong>Semester:</strong> <?php echo htmlspecialchars($student['semester']); ?></p>

    <p><strong>Form Status:</strong>
    <?php if ($form_status === 'Not Submitted') { ?>
        <span class="status Pending">Not Submitted</span>
    <?php } else { ?>
        <span class="status <?php echo $form_status; ?>">
            <?php echo $form_status; ?>
        </span>
      <?php if ($form['status'] == 'Approved') { ?>
    <div style="background:#eef6ff; padding:10px; margin-top:10px;">
        <p><strong>Exam Centre</strong></p>
        <p>
            Centre Code: <?= htmlspecialchars($form['centre_code']) ?><br>
            Centre Name: <?= htmlspecialchars($form['centre_name']) ?>
        </p>
    </div>
<?php } ?>
  
    <?php } ?>
</p>


  <?php if ($deadline && $form_status === 'Not Submitted') { ?>
    <p><strong>Submission Deadline:</strong>
        <?php echo date("d M Y, h:i A", strtotime($deadline)); ?>
    </p>
<?php } ?>


<!-- ===============================
     SUBJECT LIST (READ-ONLY)
     =============================== -->
<div class="container">
    <h4>Registered Subjects</h4>

    <?php if (!empty($student['selected_subjects'])) { ?>
        <ul>
            <?php
            $subjects = explode(",", $student['selected_subjects']);
            foreach ($subjects as $sub) {
                echo "<li>" . htmlspecialchars(trim($sub)) . "</li>";
            }
            ?>
        </ul>
    <?php } else { ?>
        <p>No subjects found.</p>
    <?php } ?>
</div>

<!-- ===============================
     FORM ACTIONS
     =============================== -->
<?php if ($form_status === 'Not Submitted') { ?>

    <div class="container" style="margin-top:20px;">

        <?php if (!$submission_open) { ?>
            <p style="color:red; font-weight:bold;">
                ❌ Form submission period is closed.
            </p>
        <?php } else { ?>
            <form method="post">
                <button type="submit" name="submit">
                    Submit Examination Form
                </button>
            </form>
        <?php } ?>

    </div>

<?php } if ($form_status === 'Approved') { ?>

    <div class="container" style="margin-top:20px;">
        <a href="print_form.php" target="_blank">
            🖨️ Print Examination Form
        </a>
    </div>

<?php } ?>
