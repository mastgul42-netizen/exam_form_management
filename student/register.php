<?php
include("../config/db.php");
include("../header.php");
$msg = "";

/* ===============================
   FETCH SUBJECT DATA
   =============================== */
$subjects = [];

$res = mysqli_query(
    $conn,
    "SELECT subject_name, category, major_stream FROM subjects"
);

while ($row = mysqli_fetch_assoc($res)) {
    $subjects[] = $row;
}

/* ===============================
   FETCH DISTINCT MAJORS
   =============================== */
$majors = [];
$res2 = mysqli_query(
    $conn,
    "SELECT DISTINCT major_stream 
     FROM subjects 
     WHERE major_stream IS NOT NULL"
);

while ($m = mysqli_fetch_assoc($res2)) {
    $majors[] = $m['major_stream'];
}

/* ===============================
   HANDLE FORM SUBMISSION
   =============================== */
if (isset($_POST['apply'])) {

    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $dob      = $_POST['dob'];
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    $semester = $_POST['semester'];
    $major    = $_POST['major'] ?? '';

    if (!$semester) {
        $msg = "<p style='color:red;'>Please select semester.</p>";
    } elseif (!$major) {
        $msg = "<p style='color:red;'>Please select major.</p>";
    } elseif (!isset($_POST['subjects']) || count($_POST['subjects']) < 1) {
        $msg = "<p style='color:red;'>Please select subjects.</p>";
    } else {

        $cleanSubjects = array_unique(
            array_map('trim', $_POST['subjects'])
        );

        $subjects_selected = implode(",", $cleanSubjects);


        mysqli_query(
            $conn,
            "INSERT INTO students 
             (name, dob, address, semester, major, selected_subjects, status)
             VALUES 
             ('$name', '$dob', '$address', '$semester', '$major', '$subjects_selected', 'Pending')"
        );

        $msg = "<p style='color:green;'>
                  Application submitted successfully.<br>
                  Wait for admin approval.
                </p>";
    }
}
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
    <h3>Student Enrollment Application</h3>

    <?php echo $msg; ?>

    <form method="post">

        <label>Full Name</label>
        <input type="text" name="name" required>

        <label>Date of Birth</label>
        <input type="date" name="dob" required>

        <label>Address</label>
        <textarea name="address" required></textarea>

        <label>Semester</label>
        <select id="semester" name="semester" required>
            <option value="">-- Select Semester --</option>
            <?php for ($i = 1; $i <= 8; $i++) { ?>
                <option value="<?php echo $i; ?>">
                    Semester <?php echo $i; ?>
                </option>
            <?php } ?>
        </select>

        <label>Major</label>
        <select id="major" name="major" required disabled>
            <option value="">-- Select Major --</option>
            <?php foreach ($majors as $m) { ?>
                <option value="<?php echo $m; ?>">
                    <?php echo $m; ?>
                </option>
            <?php } ?>
        </select>

        <hr>

        <div id="subjects-area"></div>

        <br>
        <button type="submit" name="apply">
            Apply for Enrollment
        </button>
        <div id="hidden-subjects"></div>

    </form>
    <script>
         const SUBJECTS = <?php echo json_encode($subjects); ?>;

    </script>
    <script src="../assets/js/student_register.js"></script>
</div>

