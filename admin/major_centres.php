<?php
include("../config/db.php");
include("../header.php");


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

while ($m = mysqli_fetch_assoc($mres)) {
    $majors[] = $m['major_stream'];
}

/* ===============================
   DELETE MAPPING
   =============================== */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    mysqli_query(
        $conn,
        "DELETE FROM major_centres WHERE id=$id"
    );

    header("Location: major_centres.php");
    exit();
}

/* ===============================
   EDIT MODE
   =============================== */
$edit = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    $edit = mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT * FROM major_centres WHERE id=$eid"
        )
    );
}

/* ===============================
   ADD / UPDATE LOGIC
   =============================== */
if (isset($_POST['save'])) {

    $id    = $_POST['id'] ?? null;
    $major = mysqli_real_escape_string($conn, $_POST['major']);
    $code  = mysqli_real_escape_string($conn, $_POST['centre_code']);
    $name  = mysqli_real_escape_string($conn, $_POST['centre_name']);

    /* ---------- CHECK DUPLICATE CENTRE CODE ---------- */
    $dupCode = mysqli_query(
        $conn,
        "SELECT id FROM major_centres
         WHERE centre_code='$code'
         AND id != '" . ($id ?? 0) . "'"
    );

    if (mysqli_num_rows($dupCode) > 0) {
        $msg = "<p style='color:red;'>Centre code already assigned to another major.</p>";
    } else {

        /* ---------- ADD MODE ---------- */
        if (!$id) {

            $exists = mysqli_query(
                $conn,
                "SELECT id FROM major_centres WHERE major='$major'"
            );

            if (mysqli_num_rows($exists) > 0) {
                $msg = "<p style='color:red;'>
                        Centre already assigned for this major.
                        Use Edit to modify.
                        </p>";
            } else {

                mysqli_query(
                    $conn,
                    "INSERT INTO major_centres (major, centre_code, centre_name)
                     VALUES ('$major', '$code', '$name')"
                );

                $msg = "<p style='color:green;'>Centre mapping added successfully.</p>";
            }

        }
        /* ---------- EDIT MODE ---------- */
        else {

            mysqli_query(
                $conn,
                "UPDATE major_centres
                 SET major='$major',
                     centre_code='$code',
                     centre_name='$name'
                 WHERE id=$id"
            );

            $msg = "<p style='color:green;'>Centre mapping updated successfully.</p>";
        }
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

    <div class="page-header">
        <h3>Major → Centre Mapping</h3>
        <span>Each major can have only one exam centre</span>
    </div>

    <?= $msg ?>

    <!-- ADD / EDIT FORM -->
    <form method="post" class="form-section">

        <?php if ($edit) { ?>
            <input type="hidden" name="id" value="<?= $edit['id'] ?>">
        <?php } ?>

        <label>Major</label>
        <select name="major" required <?= $edit ? '' : '' ?>>
            <option value="">-- Select Major --</option>
            <?php foreach ($majors as $m) { ?>
                <option value="<?= htmlspecialchars($m) ?>"
                    <?= ($edit && $edit['major'] === $m) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($m) ?>
                </option>
            <?php } ?>
        </select>

        <label>Centre Code</label>
        <input type="text" name="centre_code"
               value="<?= $edit['centre_code'] ?? '' ?>" required>

        <label>Centre Name</label>
        <input type="text" name="centre_name"
               value="<?= $edit['centre_name'] ?? '' ?>" required>

        <br>

        <button type="submit" name="save">
            <?= $edit ? 'Update Mapping' : 'Save Mapping' ?>
        </button>

        <?php if ($edit) { ?>
            <a href="major_centres.php" class="btn btn-danger">Cancel</a>
        <?php } ?>
    </form>

    <!-- EXISTING MAPPINGS -->
    <table>
        <tr>
            <th>Major</th>
            <th>Centre Code</th>
            <th>Centre Name</th>
            <th>Action</th>
        </tr>

        <?php while ($row = mysqli_fetch_assoc($res)) { ?>
            <tr>
                <td><?= htmlspecialchars($row['major']) ?></td>
                <td><?= htmlspecialchars($row['centre_code']) ?></td>
                <td><?= htmlspecialchars($row['centre_name']) ?></td>
                <td class="action">
                    <a href="?edit=<?= $row['id'] ?>">Edit</a> |
                    <a href="?delete=<?= $row['id'] ?>"
                       class="reject"
                       onclick="return confirm('Delete this centre mapping?')">
                        Delete
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>

</div>
