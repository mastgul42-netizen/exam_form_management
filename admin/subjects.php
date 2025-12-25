<?php

include("../config/db.php");
include("../header.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* Add Subject */
if (isset($_POST['add'])) {
    $subject = $_POST['subject'];
    mysqli_query($conn, "INSERT INTO subjects (subject_name) VALUES ('$subject')");
}

/* Delete Subject */
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM subjects WHERE id=$id");
}

/* Fetch Subjects */
$res = mysqli_query($conn, "SELECT * FROM subjects");
?>
<link rel="stylesheet" href="../assets/css/style.css">

<h3>Manage Subjects</h3>

<form method="post">
    <input type="text" name="subject" placeholder="Enter subject name" required>
    <button type="submit" name="add">Add Subject</button>
</form>

<br>
<div class="container">
<table border="1" cellpadding="5">
<tr>
    <th>ID</th>
    <th>Subject Name</th>
    <th>Action</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($res)) { ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo $row['subject_name']; ?></td>
    <td class="action">
    <a href="?delete=<?php echo $row['id']; ?>"
       onclick="return confirm('Delete this subject?')">
       Delete
    </a>
</td>

    </td>
</tr>
<?php } ?>
</table>
</div>