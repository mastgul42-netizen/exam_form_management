<link rel="stylesheet" href="../assets/css/style.css">
<?php
include("../config/db.php");
include("../header.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* Approve */
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    mysqli_query($conn,
        "UPDATE exam_forms SET status='Approved' WHERE id=$id"
    );
}

/* Reject */
if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    mysqli_query($conn,
        "UPDATE exam_forms SET status='Rejected' WHERE id=$id"
    );
}

/* Fetch Forms */
$res = mysqli_query($conn,
    "SELECT exam_forms.*, students.name 
     FROM exam_forms, students 
     WHERE exam_forms.student_id = students.id"
);
if (mysqli_num_rows($res) == 0) {
   ?> <div class="container">
           <h2>Nothing To Show!</h2>  
           <p align = "center">none of the students submited form</p>
   </div>
<?php
exit();
}
?>

<h3>Exam Forms</h3>
<div class="container">
<table >
<tr>
    <th>ID</th>
    <th>Student</th>
    <th>Subjects</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($res)) { ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['name']; ?></td>
    <td><?php echo $row['subjects']; ?></td>
    <td>
    <span class="status <?php echo $row['status']; ?>">
        <?php echo $row['status']; ?>
    </span>
<td class="action">

    <a href="edit_form.php?id=<?php echo $row['id']; ?>">Edit</a> |

    <a href="delete_form.php?id=<?php echo $row['id']; ?>"
       onclick="return confirm('Delete this form permanently?')">
       Delete
    </a>

    <br><br>

    <?php if ($row['status'] == 'Pending') { ?>
        <a href="?approve=<?php echo $row['id']; ?>">Approve</a> |
        <a href="?reject=<?php echo $row['id']; ?>">Reject</a>
    <?php } ?>

</td>


</tr>
<?php } ?>
</table>
</div>
