<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$msg = "";

/* ===============================
   FETCH DISTINCT MAJORS
   =============================== */
$majors = [];
$mres = mysqli_query(
    $conn,
    "SELECT DISTINCT major_stream 
     FROM subjects
     WHERE category='MAJOR' 
       AND major_stream IS NOT NULL
       AND major_stream != ''
     ORDER BY major_stream"
);

$majors = [];
while ($m = mysqli_fetch_assoc($mres)) {
    $majors[] = $m['major_stream'];
}


while ($m = mysqli_fetch_assoc($mres)) {
    $majors[] = $m['major'];
}

/* ===============================
   HANDLE ADD / UPDATE
   =============================== */
if (isset($_POST['save'])) {

    $major = mysqli_real_escape_string($conn, $_POST['major']);
    $code  = mysqli_real_escape_string($conn, $_POST['centre_code']);
    $name  = mysqli_real_escape_string($conn, $_POST['centre_name']);

    $check = mysqli_query(
        $conn,
        "SELECT id FROM major_centres WHERE major='$major'"
    );

    if (mysqli_num_rows($check) > 0) {

        mysqli_query(
            $conn,
            "UPDATE major_centres
             SET centre_code='$code',
                 centre_name='$name'
             WHERE major='$major'"
        );

        $msg = "<p style='color:green;'>Centre updated successfully.</p>";
    } else {

        mysqli_query(
            $conn,
            "INSERT INTO major_centres (major, centre_code, centre_name)
             VALUES ('$major', '$code', '$name')"
        );

        $msg = "<p style='color:green;'>Centre added successfully.</p>";
    }
}

/* ===============================
   FETCH EXISTING MAPPINGS
   =============================== */
$res = mysqli_query(
    $conn,
    "SELECT * FROM major_centres ORDER BY major"
);
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
    <h3>Major → Centre Mapping</h3>

    <?= $msg ?>

    <form method="post" style="margin-bottom:30px;">

        <label>Major</label>
        <select name="major" required>
            <option value="">-- Select Major --</option>
            <?php foreach ($majors as $m) { ?>
                <option value="<?= htmlspecialchars($m) ?>">
                    <?= htmlspecialchars($m) ?>
                </option>
            <?php } ?>
        </select>

        <label>Centre Code</label>
        <input type="text" name="centre_code"
            placeholder="e.g. 1203" required>

        <label>Centre Name</label>
        <input type="text" name="centre_name"
            placeholder="e.g. Govt Degree College Srinagar" required>

        <br><br>
        <button type="submit" name="save">
            Save Mapping
        </button>
    </form>

    <table>
        <tr>
            <th>Major</th>
            <th>Centre Code</th>
            <th>Centre Name</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($res)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['major']) ?></td>
                <td><?= htmlspecialchars($row['centre_code']) ?></td>
                <td><?= htmlspecialchars($row['centre_name']) ?></td>
            </tr>
        <?php } ?>
    </table>
</div>