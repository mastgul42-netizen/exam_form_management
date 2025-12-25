<?php
include("../config/db.php");
include("../header.php");

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

$form = mysqli_query($conn,
    "SELECT * FROM exam_forms 
     WHERE student_id=$student_id AND status='Pending'"
);

if (mysqli_num_rows($form) == 0) {
    header("Location: form.php");
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
        mysqli_query($conn,
            "UPDATE exam_forms SET subjects='$subjects'
             WHERE student_id=$student_id"
        );
        header("Location: form.php");
        exit();
    }
}


$res = mysqli_query($conn, "SELECT * FROM subjects");
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
    <h3>Edit Examination Form</h3>

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
        <button type="submit" name="update">Update Form</button>
    </form>
</div>
