<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$id = (int)($_GET['id'] ?? 0);

/* ===============================
   FETCH STUDENT
   =============================== */
$student = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM students WHERE id=$id")
);

if (!$student) {
    echo "Student not found";
    exit();
}

/* ===============================
   FETCH ALL SUBJECTS
   =============================== */
$res = mysqli_query(
    $conn,
    "SELECT subject_name, category FROM subjects ORDER BY category, subject_name"
);

$subjectsByCategory = [];
while ($row = mysqli_fetch_assoc($res)) {
    $subjectsByCategory[$row['category']][] = $row['subject_name'];
}

/* Selected subjects (cleaned) */
$selectedSubjects = array_unique(
    array_map('trim', explode(",", $student['selected_subjects']))
);

/* ===============================
   HANDLE UPDATE
   =============================== */
if (isset($_POST['update'])) {

    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $semester = $_POST['semester'];
    $status   = $_POST['status'];

    $subjects = $_POST['subjects'] ?? [];

    // Clean subjects (VERY IMPORTANT)
    $subjects = array_unique(array_map('trim', $subjects));
    $subjectCSV = implode(",", $subjects);

    mysqli_query(
        $conn,
        "UPDATE students 
         SET name='$name',
             semester='$semester',
             status='$status',
             selected_subjects='$subjectCSV'
         WHERE id=$id"
    );

    header("Location: students.php");
    exit();
}
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
    <h3>Edit Student</h3>

    <form method="post">

        <label>Name</label>
        <input type="text" name="name"
               value="<?= htmlspecialchars($student['name']) ?>" required>

        <label>Semester</label>
        <select name="semester" required>
            <?php for ($i=1; $i<=8; $i++) { ?>
                <option value="<?= $i ?>"
                    <?= $student['semester']==$i?'selected':'' ?>>
                    Semester <?= $i ?>
                </option>
            <?php } ?>
        </select>

        <label>Status</label>
        <select name="status">
            <option value="Approved"
                <?= $student['status']=='Approved'?'selected':'' ?>>
                Approved
            </option>
            <option value="Rejected"
                <?= $student['status']=='Rejected'?'selected':'' ?>>
                Rejected
            </option>
        </select>

        <hr>

        <h4>Modify Subjects</h4>

        <?php foreach ($subjectsByCategory as $category => $subs) { ?>

            <strong><?= htmlspecialchars($category) ?></strong><br>

            <?php foreach ($subs as $s) { ?>
                <label style="display:block; margin-left:15px;">
                    <input type="checkbox" name="subjects[]"
                           value="<?= htmlspecialchars($s) ?>"
                           <?= in_array($s, $selectedSubjects) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($s) ?>
                </label>
            <?php } ?>

            <br>
        <?php } ?>

        <br>
        <button type="submit" name="update">
            Update Student
        </button>
    </form>
</div>
