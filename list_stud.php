<?php
session_start();
 include 'db_connection.php';
 include 'includes/profile_pic.php';

 $username = $_SESSION['username'] ?? 'Vaishali'; 
 $role = $_SESSION['role'] ?? 'N/A';
 $mentor_data = $_SESSION['mentor_data'] ?? null;
 $dashboard_data = $_SESSION['dashboard_data'] ?? null;
 $profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image
 

$batch_sql = "SELECT DISTINCT batch_year FROM student_details ORDER BY batch_year DESC";
$batch_result = $conn->query($batch_sql);

$sql = "SELECT name, roll_number, degree, course, batch_year, email, phno FROM student_details";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PMS Mentor Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/liststud.css">
    <link rel="stylesheet" href="mentors.css">
    <script src="https://kit.fontawesome.com/0f4e2bc10d.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
    <div class="circle" onclick="document.querySelector('.file-upload').click()">
            <img class="profile-pic" src="<?php echo htmlspecialchars($profile_image); ?>" alt="Profile Picture">
            <div class="p-image">
                <i class="fa fa-camera upload-button"></i>
                <form id="uploadForm" enctype="multipart/form-data" action="stud_dash.php" method="POST">
                    <input class="file-upload" name="profile_pic" type="file" accept="image/*" onchange="document.getElementById('uploadForm').submit();" />
                </form>
            </div>
        </div><br>
        <h2 class="profile-email"><?php echo htmlspecialchars($username); ?></h2> <!-- Display email -->
        <p class="profile-role" style="text-align: center;"><?php echo htmlspecialchars($role); ?></p> <!-- Display role -->
                <ul>
            <li><a href="mentors_dash.php"><i class="fas fa-home"></i>Home</a></li>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropdown-btn"><i class="fas fa-user"></i> Students</a>
                <div class="dropdown-container">
                    <a href="add_stud.php"><i class="fas fa-user-plus"></i> Add Students</a>
                    <a href="list_stud.php"><i class="fas fa-list"></i> List Students</a>
                </div>
            </li>
            <li><a href="project_list.php"><i class="fas fa-address-card"></i>Projects</a></li>
            <li><a href="submission.html"><i class="fas fa-blog"></i>Submission</a></li>
            <li><a href="viewteams.php"><i class="fas fa-address-book"></i>Teams</a></li>
            <li><a href="cal.php"><i class="fas fa-calendar-alt"></i>Schedule</a></li>
        </ul>
    </div>
    <div class="main_header">
        <div class="header">
            <h1>LIST STUDENTS</h1>
            <div class="header_icons">
                <div class="search">
                    <input type="text" placeholder="Search..." />
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <i class="fa-solid fa-bell"></i>
            </div>
        </div>
        <br>
        <hr>
    </div>
</div>
<!-- Filter and Search -->
<div class="list-section">
    <div class="filter-container">
        <select id="batchYearFilter">
            <option value="all">Filter Batch</option>
            <?php while ($batch_row = $batch_result->fetch_assoc()) { ?>
                <option value="<?php echo $batch_row['batch_year']; ?>"><?php echo $batch_row['batch_year']; ?></option>
            <?php } ?>
        </select>

        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
    </div>


    <div class="table-container">
        <table id="studentsTable">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Roll Number</th>
                    <th>Degree</th>
                    <th>Course</th>
                    <th>Batch Year</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $serial_number = 1;
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $serial_number++ . "</td>";
                        echo "<td class='table-name'>" . htmlspecialchars($row['name']) . "</td>"; 
                        echo "<td>" . htmlspecialchars($row['roll_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['degree']) . "</td>";
                        echo "<td class='table-course'>" . htmlspecialchars($row['course']) . "</td>"; 
                        echo "<td class='table-batch-year'>" . htmlspecialchars($row['batch_year']) . "</td>";                     
                        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['phno']) . "</td>";
                        echo "<td>
                            <select class='action-dropdown' onchange='handleAction(this, \"" . htmlspecialchars($row['roll_number']) . "\")'>
                                <option value='' selected>Action</option>
                                <option value='view'>View</option>
                                <option value='delete'>Delete</option>
                            </select>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No students found</td></tr>";
                }
                ?>
            </tbody>

        </table>
    </div>
</div>
<script>
// Filter by Batch Year
document.getElementById('batchYearFilter').addEventListener('change', function() {
    var filterValue = this.value;
    var rows = document.querySelectorAll('#studentsTable tbody tr');

    rows.forEach(function(row) {
        var batchYear = row.querySelector('td:nth-child(6)').textContent;
        if (filterValue === 'all' || batchYear === filterValue) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

// Search Functionality
document.getElementById('searchInput').addEventListener('input', function() {
    var searchValue = this.value.toLowerCase();
    var rows = document.querySelectorAll('#studentsTable tbody tr');

    rows.forEach(function(row) {
        var rowText = row.textContent.toLowerCase();
        if (rowText.includes(searchValue)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
});

function handleAction(select, rollNumber) {
    const action = select.value;

    if (action === 'view') {
        // Redirect to the student profile page with roll number as a query parameter
        window.location.href = `view_stud_profiles.php?roll_number=${encodeURIComponent(rollNumber)}`;
    } else if (action === 'delete') {
        // Confirm deletion
        if (confirm('Are you sure you want to delete this student? This action cannot be undone.')) {
            // Make an AJAX request to delete the student from the database
            fetch(`delete_student.php?roll_number=${encodeURIComponent(rollNumber)}`, {
                method: 'DELETE' // or 'POST' depending on your backend setup
            })
            .then(response => {
                if (response.ok) {
                    alert('Student deleted successfully.'); // Alert for successful deletion
                    location.reload(); // Reload the page to see the changes
                } else {
                    alert('Failed to delete student. Please try again.'); // Alert for failure
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the student.'); // Alert for error
            });
        }
    }
}

</script>

</body>
</html>

<?php
$conn->close();
?>
