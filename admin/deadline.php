<?php
include("../config/db.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

/* Fetch current deadline */
$setting = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT submission_deadline FROM settings WHERE id=1")
);

$current_deadline = $setting['submission_deadline'];

/* Update deadline */
if (isset($_POST['save'])) {
    $deadline = $_POST['deadline'];

    mysqli_query(
        $conn,
        "UPDATE settings 
         SET submission_deadline='$deadline'
         WHERE id=1"
    );

    header("Location: deadline.php");
    exit();
}
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
    <h3>Set Exam Form Submission Deadline</h3>

    <form method="post">
        <label>Submission Deadline</label><br><br>

        <input type="datetime-local"
               name="deadline"
               value="<?php echo date('Y-m-d\TH:i', strtotime($current_deadline)); ?>"
               required>

        <br><br>
        <button type="submit" name="save">
            Save Deadline
        </button>
    </form>

    <br>
    <p><strong>Current Deadline:</strong><br>
        <?php echo date("d M Y, h:i A", strtotime($current_deadline)); ?>
    </p>
</div>
