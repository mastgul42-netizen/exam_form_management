<?php
include("../config/db.php");
include("../header.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
/* ===============================
   HANDLE DELETE STUDENT
   =============================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    mysqli_query($conn, "DELETE FROM students WHERE id=$id");

    header("Location: students.php");
    exit();
}


/* Handle Reject */
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];

    mysqli_query(
        $conn,
        "UPDATE students SET status='Rejected' WHERE id=$id"
    );

    header("Location: students.php");
    exit();
}

/* Handle Approve */
if (isset($_POST['approve'])) {
    $id       = (int)$_POST['student_id'];
    $enrollno = mysqli_real_escape_string($conn, $_POST['enroll_no']);

    mysqli_query(
        $conn,
        "UPDATE students 
         SET enroll_no='$enrollno', status='Approved'
         WHERE id=$id"
    );

    header("Location: students.php");
    exit();
}

/* Fetch students */
$res = mysqli_query(
    $conn,
    "SELECT * FROM students ORDER BY id DESC"
);

?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
    <h3>Student Applications</h3>

    <table>
        <tr>
            <th>Name</th>
            <th>DOB</th>
            <th>Semester</th>
            <th>Enrollment No</th>
            <th>Subjects Selected</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php
        function groupSubjects($conn, $subjectCSV)
        {

            if (!$subjectCSV) return [];

            $subjectList = explode(",", $subjectCSV);
            $grouped = [];

            foreach ($subjectList as $sub) {
                $sub = trim($sub);

                $q = mysqli_query(
                    $conn,
                    "SELECT category FROM subjects WHERE subject_name='$sub' LIMIT 1"
                );

                if ($r = mysqli_fetch_assoc($q)) {
                    $grouped[$r['category']][] = $sub;
                }
            }

            return $grouped;
        }

        while ($row = mysqli_fetch_assoc($res)) { ?>
            <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['dob']; ?></td>
                <td><?php echo $row['semester']; ?></td>
                <td><?php echo $row['enroll_no'] ?: '-'; ?></td>
                <td>
                    <?php
                    $subjectCSV = $row['selected_subjects'] ?? '';
                    $groupedSubjects = groupSubjects($conn, $subjectCSV);

                    foreach ($groupedSubjects as $category => $subs) {
                        echo "<strong>$category:</strong> " . implode(", ", $subs) . "<br>";
                    }
                    ?>
                <td>
                    <span class="status <?php echo $row['status']; ?>">
                        <?php echo $row['status']; ?>
                    </span>
                </td>
                <td>
                    <?php if ($row['status'] == 'Pending') { ?>

                        <!-- APPROVE -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="student_id" value="<?= $row['id'] ?>">
                            <input type="text" name="enroll_no" placeholder="Enroll No" required>
                            <button type="submit" name="approve">Approve</button>
                        </form>

                        <!-- REJECT -->
                        <a href="?reject=<?= $row['id'] ?>"
                            onclick="return confirm('Reject this student?')"
                            style="color:red;">Reject</a>

                    <?php } else { ?>

                        <!-- EDIT -->
                        <a href="edit_student.php?id=<?= $row['id'] ?>">Edit</a>

                        <!-- DELETE -->
                        <a href="?delete=<?= $row['id'] ?>"
                            onclick="return confirm('Delete this student permanently?')"
                            style="color:red; margin-left:10px;">
                            Delete
                        </a>

                    <?php } ?>
                </td>

            </tr>
        <?php } ?>
    </table>
</div>