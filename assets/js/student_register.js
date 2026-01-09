   

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
