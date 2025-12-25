<?php

include("../config/db.php");
include("../header.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'] ?? 0;

/* fetch form */
$form = mysqli_query(
    $conn,
    "SELECT * FROM exam_forms WHERE id=$id"
);

if (mysqli_num_rows($form) == 0) {
    header("Location: forms.php");
    exit();
}

$data = mysqli_fetch_assoc($form);
$selected = explode(",", $data['subjects']);

if (isset($_POST['update'])) {

    if (!isset($_POST['subjects']) || count($_POST['subjects']) < 1) {

        echo "<div class='container' style='color:red; text-align:center;'>
                At least one subject must be selected.
              </div>";

    } else {

        $subjects = implode(",", $_POST['subjects']);
        $status   = $_POST['status'];

        mysqli_query(
            $conn,
            "UPDATE exam_forms 
             SET subjects='$subjects', status='$status'
             WHERE id=$id"
        );

        header("Location: forms.php");
        exit();
    }
}



$res = mysqli_query($conn, "SELECT * FROM subjects");
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
    <h3>Edit Exam Form (Admin)</h3>

    <form method="post">
        <?php while ($row = mysqli_fetch_assoc($res)) { ?>
            <div>
                <input type="checkbox" name="subjects[]"
                    value="<?php echo $row['subject_name']; ?>"
                    <?php if (in_array($row['subject_name'], $selected)) echo "checked"; ?>>
                <?php echo $row['subject_name']; ?>
            </div>
        <?php } ?>
        <br>
        <label><strong>Form Status</strong></label><br>

        <select name="status" required>
            <option value="Pending" <?php if ($data['status'] == "Pending") echo "selected"; ?>>
                Pending
            </option>
            <option value="Approved" <?php if ($data['status'] == "Approved") echo "selected"; ?>>
                Approved
            </option>
            <option value="Rejected" <?php if ($data['status'] == "Rejected") echo "selected"; ?>>
                Rejected
            </option>
        </select>
<br>
        <br>
        <button type="submit" name="update">Update Form</button>
    </form>
</div>