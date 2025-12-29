<?php
include("../config/db.php");

$msg = "";

/* ===============================
   FETCH SUBJECT DATA
   =============================== */
$subjects = [];

$res = mysqli_query(
    $conn,
    "SELECT subject_name, category, major_stream FROM subjects"
);

while ($row = mysqli_fetch_assoc($res)) {
    $subjects[] = $row;
}

/* ===============================
   FETCH DISTINCT MAJORS
   =============================== */
$majors = [];
$res2 = mysqli_query(
    $conn,
    "SELECT DISTINCT major_stream 
     FROM subjects 
     WHERE major_stream IS NOT NULL"
);

while ($m = mysqli_fetch_assoc($res2)) {
    $majors[] = $m['major_stream'];
}

/* ===============================
   HANDLE FORM SUBMISSION
   =============================== */
if (isset($_POST['apply'])) {

    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $dob      = $_POST['dob'];
    $address  = mysqli_real_escape_string($conn, $_POST['address']);
    $semester = $_POST['semester'];
    $major    = $_POST['major'] ?? '';

    if (!$semester) {
        $msg = "<p style='color:red;'>Please select semester.</p>";
    } elseif (!$major) {
        $msg = "<p style='color:red;'>Please select major.</p>";
    } elseif (!isset($_POST['subjects']) || count($_POST['subjects']) < 1) {
        $msg = "<p style='color:red;'>Please select subjects.</p>";
    } else {

        $cleanSubjects = array_unique(
            array_map('trim', $_POST['subjects'])
        );

        $subjects_selected = implode(",", $cleanSubjects);


        mysqli_query(
            $conn,
            "INSERT INTO students 
             (name, dob, address, semester, major, selected_subjects, status)
             VALUES 
             ('$name', '$dob', '$address', '$semester', '$major', '$subjects_selected', 'Pending')"
        );

        $msg = "<p style='color:green;'>
                  Application submitted successfully.<br>
                  Wait for admin approval.
                </p>";
    }
}
?>

<link rel="stylesheet" href="../assets/css/style.css">

<div class="container">
    <h3>Student Enrollment Application</h3>

    <?php echo $msg; ?>

    <form method="post">

        <label>Full Name</label>
        <input type="text" name="name" required>

        <label>Date of Birth</label>
        <input type="date" name="dob" required>

        <label>Address</label>
        <textarea name="address" required></textarea>

        <label>Semester</label>
        <select id="semester" name="semester" required>
            <option value="">-- Select Semester --</option>
            <?php for ($i = 1; $i <= 8; $i++) { ?>
                <option value="<?php echo $i; ?>">
                    Semester <?php echo $i; ?>
                </option>
            <?php } ?>
        </select>

        <label>Major</label>
        <select id="major" name="major" required disabled>
            <option value="">-- Select Major --</option>
            <?php foreach ($majors as $m) { ?>
                <option value="<?php echo $m; ?>">
                    <?php echo $m; ?>
                </option>
            <?php } ?>
        </select>

        <hr>

        <h4>Subject Selection (NEP-2020)</h4>
        <div id="subjects-area"></div>

        <br>
        <button type="submit" name="apply">
            Apply for Enrollment
        </button>
        <div id="hidden-subjects"></div>

    </form>
</div>

<script>
    const SUBJECTS = <?php echo json_encode($subjects); ?>;

    const semesterSelect = document.getElementById("semester");
    const majorSelect = document.getElementById("major");
    const subjectArea = document.getElementById("subjects-area");

    /* ===============================
       MAIN CONTROLLER
       =============================== */
    function renderSubjects() {
        subjectArea.innerHTML = "";

        const semester = parseInt(semesterSelect.value);
        const major = majorSelect.value;

        if (!semester) {
            majorSelect.disabled = true;
            return;
        }

        majorSelect.disabled = false;

        /* ===== SEMESTER 1–3 ===== */
        if (semester <= 3) {

            if (major) {
                renderMinorRadioGroup(major);
            }

            renderRadioGroup(['AEC'], 'AEC');
            renderRadioGroup(['VAC'], 'VAC');
            renderRadioGroup(['SAC', 'SEC'], 'SAC / SEC');
            renderRadioGroup(['MDC'], 'MDC');
        }


        /* ===== SEMESTER 4–6 ===== */
        if (semester >= 4 && semester <= 6 && major) {
            renderMajorCheckboxGroup(major, 4);
            renderMinorRadioGroup(major);
        }

        /* ===== SEMESTER 7–8 ===== */
        if (semester >= 7 && major) {
            renderMajorCheckboxGroup(major, 4);
        }
    }

    /* ===============================
       HELPERS
       =============================== */

    function renderRadioGroup(categories, title) {

        const list = SUBJECTS.filter(s =>
            categories.includes(s.category)
        );

        if (!list.length) return;

        let groupName = title.replace(/\s+/g, '_'); // unique name

        let html = `<h5>${title} (Select ONE)</h5>`;
        list.forEach(s => {
            html += `
            <label>
                <input type="radio"
                       name="${groupName}"
                       value="${s.subject_name}"
                       required>
                ${s.subject_name}
            </label><br>
        `;
        });

        subjectArea.innerHTML += html;
    }



    function renderMajorCheckboxGroup(major, max) {

        const list = SUBJECTS.filter(s =>
            s.category === 'MAJOR' && s.major_stream === major
        );

        if (!list.length) return;

        let html = `<h5>Major Subjects (Select EXACTLY ${max})</h5>`;
        list.forEach(s => {
            html += `
            <label>
                <input type="checkbox" class="major-box"
                       name="subjects[]" value="${s.subject_name}">
                ${s.subject_name}
            </label><br>
        `;
        });

        subjectArea.innerHTML += html;
        limitCheckboxes("major-box", max);
    }

    function renderMinorRadioGroup(major) {

        const list = SUBJECTS.filter(s =>
            s.category === 'MINOR' && s.major_stream === major
        );

        if (!list.length) return;

        let html = `<h5>Minor (Select ONE)</h5>`;
        list.forEach(s => {
            html += `
            <label>
                <input type="radio" name="subjects[]" value="${s.subject_name}" required>
                ${s.subject_name}
            </label><br>
        `;
        });

        subjectArea.innerHTML += html;
    }

    function limitCheckboxes(className, max) {
        document.querySelectorAll("." + className).forEach(box => {
            box.addEventListener("change", () => {
                const checked = document.querySelectorAll("." + className + ":checked");
                if (checked.length > max) {
                    box.checked = false;
                    alert(`You must select exactly ${max} major subjects.`);
                }
            });
        });
    }

    /* ===============================
       EVENTS
       =============================== */
    semesterSelect.addEventListener("change", renderSubjects);
    majorSelect.addEventListener("change", renderSubjects);
    document.querySelector("form").addEventListener("submit", function() {

        const hiddenDiv = document.getElementById("hidden-subjects");
        hiddenDiv.innerHTML = "";

        // collect checked radios
        document.querySelectorAll("input[type=radio]:checked").forEach(r => {
            let input = document.createElement("input");
            input.type = "hidden";
            input.name = "subjects[]";
            input.value = r.value;
            hiddenDiv.appendChild(input);
        });

        // collect checked major checkboxes
        document.querySelectorAll(".major-box:checked").forEach(c => {
            let input = document.createElement("input");
            input.type = "hidden";
            input.name = "subjects[]";
            input.value = c.value;
            hiddenDiv.appendChild(input);
        });
    });
</script>