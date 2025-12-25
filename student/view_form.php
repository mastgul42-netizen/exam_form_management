<?php
include("../config/db.php");
include("../includes/header.php");

$id=$_SESSION['student'];
$f=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM exam_forms WHERE student_id=$id"));
?>

<div class="card">
<h2>My Exam Form</h2>
<p><b>Semester:</b> <?= $f['semester'] ?></p>
<p><b>Subjects:</b><br><?= nl2br($f['subjects']) ?></p>
<span class="badge <?= strtolower($f['status']) ?>">
<?= $f['status'] ?>
</span>
</div>
