<?php
include("../config/db.php");

/* ===============================
   ADMIN LOGIN CHECK
   =============================== */
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* ===============================
   APPROVE FORM
   =============================== */
if (isset($_GET['approve'])) {

    $form_id = (int)$_GET['approve'];

    // Fetch student's major
    $res = mysqli_query(
        $conn,
        "SELECT s.major
         FROM exam_forms f
         JOIN students s ON s.id = f.student_id
         WHERE f.id = $form_id"
    );

    if (!$row = mysqli_fetch_assoc($res)) {
        header("Location: forms.php");
        exit();
    }

    $major = mysqli_real_escape_string($conn, $row['major']);

    // Fetch centre for major
    $cres = mysqli_query(
        $conn,
        "SELECT centre_code, centre_name
         FROM major_centres
         WHERE major='$major'
         LIMIT 1"
    );

    if (!$centre = mysqli_fetch_assoc($cres)) {
        echo "<script>
                alert('Centre not configured for this major. Please configure it first.');
                window.location='forms.php';
              </script>";
        exit();
    }

    $centre_code = mysqli_real_escape_string($conn, $centre['centre_code']);
    $centre_name = mysqli_real_escape_string($conn, $centre['centre_name']);

    // Approve + assign centre
    mysqli_query(
        $conn,
        "UPDATE exam_forms
         SET status='Approved',
             centre_code='$centre_code',
             centre_name='$centre_name'
         WHERE id=$form_id"
    );

    header("Location: forms.php");
    exit();
}

/* ===============================
   REJECT FORM
   =============================== */
if (isset($_GET['reject'])) {

    $form_id = (int)$_GET['reject'];

    mysqli_query(
        $conn,
        "UPDATE exam_forms
         SET status='Rejected'
         WHERE id=$form_id"
    );

    header("Location: forms.php");
    exit();
}

/* ===============================
   FETCH FORMS
   =============================== */
$res = mysqli_query(
    $conn,
    "SELECT f.*, s.name,s.selected_subjects,s.major
     FROM exam_forms f
     JOIN students s ON s.id = f.student_id
     ORDER BY f.id DESC"
);
?>  

<?php include("../header.php"); ?>
<link rel="stylesheet" href="../assets/css/style.css">

<h3 align="center">Exam Forms</h3>

<div class="container">

<?php if (mysqli_num_rows($res) == 0) { ?>
    <h2 align="center">Nothing To Show</h2>
    <p align="center">No student has submitted the exam form.</p>
<?php exit(); } ?>

<table>
    <tr>
        <th>ID</th>
        <th>Student</th>
        <th>Stream</th>
        <th>Centre Code</th>
        <th>Centre Name</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

<?php while ($row = mysqli_fetch_assoc($res)) { ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= ($row['name']) ?></td>
    <td><?= ($row['major']) ?></td>
        <td><?= $row['centre_code']?></td>
    <td><?= $row['centre_name'] ?></td>

    <td>
        <span class="status <?= $row['status'] ?>">
            <?= $row['status'] ?>
        </span>
    </td>

    <td class="action">
        <a href="edit_form.php?id=<?= $row['id'] ?>">Edit</a> |
        <a href="delete_form.php?id=<?= $row['id'] ?>"
           onclick="return confirm('Delete this form permanently?')">
           Delete
        </a>

        <?php if ($row['status'] == 'Pending') { ?>
            <br><br>
            <a href="?approve=<?= $row['id'] ?>"class="approve">Approve</a> |
            <a href="?reject=<?= $row['id'] ?>"
               onclick="return confirm('Reject this form?')"class="reject">
               Reject
            </a>
        <?php } ?>
    </td>
</tr>
<?php } ?>

</table>
</div>
