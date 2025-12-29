<?php
include("../config/db.php");
include("../header.php");


if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* =====================
   HANDLE DELETE
   ===================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM subjects WHERE id=$id");
    header("Location: subjects.php");
    exit();
}

/* =====================
   HANDLE ADD SUBJECT
   ===================== */
if (isset($_POST['add'])) {

    $category = $_POST['category'];
    $major    = $_POST['major_stream'] ?: NULL;

    $lines = explode("\n", $_POST['subject_names']);

    foreach ($lines as $line) {
        $name = trim($line);
        if ($name === '') continue;

        $safeName = mysqli_real_escape_string($conn, $name);

        mysqli_query(
            $conn,
            "INSERT INTO subjects (subject_name, category, major_stream)
             VALUES (
                '$safeName',
                '$category',
                " . ($major ? "'$major'" : "NULL") . "
             )"
        );
    }

    header("Location: subjects.php");
    exit();
}


/* =====================
   FETCH SUBJECTS
   ===================== */
$res = mysqli_query(
    $conn,
    "SELECT * FROM subjects ORDER BY category, subject_name"
);

/* Fetch majors for dropdown */
$majors = [];
$mres = mysqli_query(
    $conn,
    "SELECT DISTINCT major_stream 
     FROM subjects 
     WHERE major_stream IS NOT NULL"
);

while ($m = mysqli_fetch_assoc($mres)) {
    $majors[] = $m['major_stream'];
}
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
    <h3>Manage Subjects (NEP-2020)</h3>

    <!-- ADD SUBJECT FORM -->
    <form method="post" style="margin-bottom:20px;">
        <h4>Add New Subject</h4>

        <label>Subject Name(s)</label>
        <textarea name="subject_names" rows="4"
            placeholder="Enter one subject per line" required></textarea>


        <label>Category</label>
        <select name="category" required>
            <option value="">-- Select Category --</option>
            <option value="AEC">AEC</option>
            <option value="VAC">VAC</option>
            <option value="SAC">SAC</option>
            <option value="SEC">SEC</option>
            <option value="MDC">MDC</option>
            <option value="MAJOR">MAJOR</option>
            <option value="MINOR">MINOR</option>
        </select>

        <label>Major Stream (only for MAJOR / MINOR)</label>
        <input type="text" name="major_stream"
            placeholder="e.g. Computer Applications">

        <button type="submit" name="add">
            Add Subject
        </button>
    </form>

    <!-- SUBJECT LIST -->
    <table>
        <tr>
            <th>Subject</th>
            <th>Category</th>
            <th>Major Stream</th>
            <th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($res)) { ?>
            <tr>
                <td><?php echo $row['subject_name']; ?></td>
                <td><?php echo $row['category']; ?></td>
                <td><?php echo $row['major_stream'] ?: '-'; ?></td>
                <td>
                    <a href="?delete=<?php echo $row['id']; ?>"
                        onclick="return confirm('Delete this subject?')"
                        style="color:red;">
                        Delete
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>