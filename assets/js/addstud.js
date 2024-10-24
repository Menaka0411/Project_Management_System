document.addEventListener("DOMContentLoaded", function () {
    const dropdownBtns = document.querySelectorAll('.dropdown-btn');
    
    dropdownBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const dropdownContent = this.nextElementSibling;
            dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
        });
    });

    // Load existing marks when the page loads
    loadMarks();
    
    // Final upload button event listener
    document.getElementById('final-upload').addEventListener('click', function() {
        // Code to save entire form data to the database (this requires a server-side implementation)
        saveStudentData();
    });
});

function showInputFields(semester) {
    const insights = document.getElementById('course-insights');
    const title = document.getElementById('insight-title');
    title.textContent = `${semester} Course Insights`;
    insights.style.display = 'block';
}

function saveMarks() {
    const zerothReview = document.getElementById('zeroth-review').value;
    const firstReview = document.getElementById('first-review').value;
    const secondReview = document.getElementById('second-review').value;
    const thirdReview = document.getElementById('third-review').value;
    const finalReview = document.getElementById('final-review').value;

    const marksData = {
        zerothReview,
        firstReview,
        secondReview,
        thirdReview,
        finalReview
    };

    // Simulating saving marks locally
    localStorage.setItem('marksData', JSON.stringify(marksData));
    loadMarks();
}

function loadMarks() {
    const marksList = document.getElementById('marks-list');
    marksList.innerHTML = ''; // Clear existing marks

    const storedMarks = localStorage.getItem('marksData');
    if (storedMarks) {
        const marksData = JSON.parse(storedMarks);
        Object.keys(marksData).forEach(key => {
            const mark = marksData[key];
            const listItem = document.createElement('div');
            listItem.textContent = `${key.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase())}: ${mark}`;
            marksList.appendChild(listItem);
        });
    }
}

function saveStudentData() {
    const formData = new FormData(document.getElementById('student-form'));
    fetch('add_stud.php', {
        method: 'POST',
        body: JSON.stringify(Object.fromEntries(formData.entries())),
        headers: {
            'Content-Type': 'application/json'
        }
    }).then(response => response.json())
      .then(data => {
          if (data.status === 'success') {
              alert('Student saved successfully!');
          } else {
              alert('Error: ' + data.message);
          }
      })
      .catch(error => console.error('Error:', error));
}

document.getElementById('add-field').addEventListener('click', function() {
    var container = document.getElementById('attendance-container');
    var newRow = document.createElement('div');
    newRow.classList.add('attendance-row');
    newRow.innerHTML = `
        <label for="attendance_date[]">Date:</label>
        <input type="date" name="attendance_date[]" required>

        <label for="attendance_status[]">Status:</label>
        <input type="text" name="attendance_status[]" required>

        <label for="attendance_remarks[]">Remarks:</label>
        <input type="text" name="attendance_remarks[]" required>
    `;
    container.appendChild(newRow);
});

document.getElementById('remove-field').addEventListener('click', function() {
    var container = document.getElementById('attendance-container');
    if (container.children.length > 1) {
        container.removeChild(container.lastChild);
    }
});
function toggleInsights(sem) {
    const insightSection = document.getElementById('insights-sem' + sem);
    
    // Toggle the show class to reveal or hide the section
    if (insightSection.classList.contains('show')) {
        insightSection.classList.remove('show');
    } else {
        insightSection.classList.add('show');
    }
}
function validateBatchYear(input) {
    const regex = /^\d{4}-\d{4}$/;
    if (!regex.test(input.value)) {
        input.setCustomValidity("Please enter a valid batch year format (YYYY-YYYY).");
    } else {
        input.setCustomValidity(""); // Reset the validity
    }
}