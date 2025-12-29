<?php
include("../config/db.php");

/* ===============================
   LOGIN CHECK
   =============================== */
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

/* ===============================
   FETCH STUDENT + FORM DATA
   =============================== */
$data = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT 
            s.name,
            s.enroll_no,
            s.semester,
            s.major,
            s.selected_subjects,
            f.subjects,
            f.status,
            f.submitted_at,
            f.centre_code,
            f.centre_name
         FROM students s
         JOIN exam_forms f ON f.student_id = s.id
         WHERE s.id = $student_id"
    )
);


/* ===============================
   ALLOW PRINT ONLY IF APPROVED
   =============================== */
if (!$data || $data['status'] !== 'Approved') {
    echo "<h3 style='text-align:center;color:red;'>
            Examination form is not approved yet.
          </h3>";
    exit();
}

/* ===============================
   GROUP SUBJECTS BY CATEGORY
   =============================== */
$subjectNames = array_map('trim', explode(",", $data['selected_subjects']));
$grouped = [];

foreach ($subjectNames as $sub) {

    $safeSub = mysqli_real_escape_string($conn, $sub);

    $q = mysqli_query(
        $conn,
        "SELECT category FROM subjects
         WHERE subject_name='$safeSub'
         LIMIT 1"
    );

    if ($row = mysqli_fetch_assoc($q)) {
        $cat = strtoupper($row['category']);

        if ($cat === 'MAJOR') {
            $grouped['MAJOR'][] = $sub;
        } elseif ($cat === 'MINOR') {
            $grouped['MINOR'][] = $sub;
        } elseif ($cat === 'AEC') {
            $grouped['AEC'][] = $sub;
        } elseif ($cat === 'VAC') {
            $grouped['VAC'][] = $sub;
        } elseif ($cat === 'SAC' || $cat === 'SEC') {
            $grouped['SAC / SEC'][] = $sub;
        } elseif ($cat === 'MDC') {
            $grouped['MDC'][] = $sub;
        } else {
            $grouped['OTHERS'][] = $sub;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Examination Form</title>
    <style>
        body {
            font-family: Arial;
            margin: 40px;
        }

        h2,
        h3 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0 25px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
        }

        th {
            background: #f0f0f0;
        }

        .center {
            text-align: center;
        }

        .print-btn {
            text-align: center;
            margin-top: 20px;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
    </style>
</head>

<body>

    <h2>University Examination Form</h2>

    <table>
        <tr>
            <th>Name</th>
            <td><?= htmlspecialchars($data['name']) ?></td>
        </tr>
        <tr>
            <th>Enrollment No</th>
            <td><?= htmlspecialchars($data['enroll_no']) ?></td>
        </tr>
        <tr>
            <th>Semester</th>
            <td><?= htmlspecialchars($data['semester']) ?></td>
        </tr>
        <tr>
            <th>Major Stream</th>
            <td><?= htmlspecialchars($data['major']) ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td><?= htmlspecialchars($data['status']) ?></td>
        </tr>
        <tr>
            <th>Centre Code</th>
            <td><?= htmlspecialchars($data['centre_code']) ?></td>
        </tr>
        <tr>
            <th>Centre Name</th>
            <td><?= htmlspecialchars($data['centre_name']) ?></td>
        </tr>

        <tr>
            <th>Submitted On</th>
            <td><?= date("d M Y, h:i A", strtotime($data['submitted_at'])) ?></td>
        </tr>
    </table>

    <h3>Registered Subjects</h3>

    <!-- ===== MAJOR SUBJECTS (PRINT ONCE) ===== -->
    <?php if (!empty($grouped['MAJOR'])) { ?>
        <h4>Major Subjects</h4>
        <table>
            <tr>
                <th>#</th>
                <th>Subject</th>
            </tr>
            <?php $i = 1;
            foreach ($grouped['MAJOR'] as $s) { ?>
                <tr>
                    <td class="center"><?= $i++ ?></td>
                    <td><?= htmlspecialchars($s) ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>

    <!-- ===== OTHER CATEGORIES (NO MAJOR HERE) ===== -->
    <?php
    foreach (['MINOR', 'AEC', 'VAC', 'SAC / SEC', 'MDC', 'OTHERS'] as $cat) {
        if (empty($grouped[$cat])) continue;
    ?>
        <h4><?= $cat ?></h4>
        <table>
            <tr>
                <th>#</th>
                <th>Subject</th>
            </tr>
            <?php $i = 1;
            foreach ($grouped[$cat] as $s) { ?>
                <tr>
                    <td class="center"><?= $i++ ?></td>
                    <td><?= htmlspecialchars($s) ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>

    <div class="print-btn">
        <button onclick="window.print()">Print</button>
    </div>

</body>

</html>