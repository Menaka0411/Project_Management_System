<?php
session_start();
 include 'db_connection.php';
 include 'includes/profile_pic.php';

 if ($_SESSION['role'] !== 'Staff') {
    $_SESSION['role'] = 'Staff'; 
}

$username = $_SESSION['username'] ?? 'Vaishali'; 
$role = $_SESSION['role']; 

$mentor_data = $_SESSION['mentor_data'] ?? null;
$dashboard_data = $_SESSION['dashboard_data'] ?? null;
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image

if (isset($_GET['roll_number'])) 
{
        $roll_number = $_GET['roll_number'];
        $sql = "SELECT id, name, roll_number, degree, course, batch_year, email, phno FROM student_details WHERE roll_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $roll_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $student = $result->fetch_assoc();
        } else {
            $student = null; 
        }
} 
else 
{
    $student = null; 
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_marks'])) {
    $roll_number = $_POST['roll_number'];
    $semester = $_POST['semester'];
    $year = $_POST['year'];
    $review_0 = $_POST['review_0'];
    $review_1 = $_POST['review_1'];
    $review_2 = $_POST['review_2'];
    $review_3 = $_POST['review_3'];
    $final_review = $_POST['final_review'];

    $sql = "INSERT INTO student_project_marks (roll_number, semester, year, review_0, review_1, review_2, review_3, final_review)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissiiii", $roll_number, $semester, $year, $review_0, $review_1, $review_2, $review_3, $final_review);
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'>Marks submitted successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . htmlspecialchars($stmt->error) . "</p>";
    }
    
}
if (isset($_POST['submit'])) {
    $roll_number = $_POST['roll_number']; 
    $week_number = $_POST['week_number'];
    $review_number = $_POST['review_number'];
    $status = $_POST['status'];
    $attendance_date = $_POST['attendance_date'];
    

    $query = "INSERT INTO attendance (roll_number, week_number, review_number, status, attendance_date) 
    VALUES (?, ?, ?, ?, ?)";

        if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("siiss", $roll_number, $week_number, $review_number, $status, $attendance_date);

        if ($stmt->execute()) {
        echo "Attendance recorded successfully!";
        } else {
        echo "Error: " . $stmt->error;
        }

        
        } else {
        echo "Error preparing statement: " . $conn->error;
        }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
    <title>PMS Mentor Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/viewteams.css">
    <link rel="stylesheet" href="assets/css/view-stud-profile.css">
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
            <li><a href="projects.php"><i class="fas fa-address-card"></i>Projects</a></li>
            <li><a href="receive"><i class="fas fa-blog"></i>Submission</a></li>
            <li><a href="viewteams.php"><i class="fas fa-address-book"></i>Teams</a></li>
            <li><a href="cal.php"><i class="fas fa-calendar-alt"></i>Schedule</a></li>
        </ul>
    </div>

    <div class="main_header">
        <div class="header">
            <h1>STUDENTS DATA</h1>
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

<section class="main-content">
<div class="back-arrow-container">
        <a href="list_stud.php" class="back-arrow">
            <i class="fas fa-arrow-left"></i>
        </a>
    </div>   
     <div class="left-column">
        <section id="statistics-data">
            <div class="profile-container">
                <h2><?php echo htmlspecialchars($student['name']); ?></h2>
                <div class="student-info">
                    <div class="info-pair"><strong>Roll Number:</strong> <span><?php echo htmlspecialchars($student['roll_number']); ?></span></div>
                    <div class="info-pair"><strong>Degree:</strong>  <span><?php echo htmlspecialchars($student['degree']); ?></span></div>
                    <div class="info-pair"><strong>Course:</strong>  <span><?php echo htmlspecialchars($student['course']); ?></span></div>
                    <div class="info-pair"><strong>Batch Year:</strong>  <span><?php echo htmlspecialchars($student['batch_year']); ?></span></div>
                    <div class="info-pair"><strong>Email:</strong>  <span><?php echo htmlspecialchars($student['email']); ?></span></div>
                    <div class="info-pair"><strong>Phone Number:</strong>  <span><?php echo htmlspecialchars($student['phno']); ?></span></div>
                </div>
            </div>
        </section>

        <section id="attendance">
            <section class="marks-entry-form-attendance">
                <h2>Record Attendance</h2>
                <form method="POST" action="">
                    <label for="roll_number">Roll Number:</label>
                    <input type="text" name="roll_number" required>
                    <label for="week_number">Week Number:</label>
                    <input type="number" id="week_number" name="week_number" min="1" required>
                    <label for="review_number">Review Number:</label>
                    <select id="review_number" name="review_number" required>
                        <option value="0">Review 0</option>
                        <option value="1">Review 1</option>
                        <option value="2">Review 2</option>
                        <option value="3">Review 3</option>
                    </select>
                    <label for="status">Status:</label>
                    <select id="status" name="status" required>
                        <option value="present">Present</option>
                        <option value="absent">Absent</option>
                    </select>
                    <label for="attendance_date">Attendance Date:</label>
                    <input type="date" name="attendance_date" required><br>
                    <button type="submit" name="submit">Submit Attendance</button>
                </form>
            </section>
        </section>
    </div>

    <div class="right-column">
        <section id="marks">
            <div class="marks-entry-form">
                <form method="POST" action="">
                    <h2>Enter Project Marks</h2>
                    <label for="roll_number">Roll Number:</label>
                    <input type="text" id="roll_number" name="roll_number" value="<?php echo htmlspecialchars($student['roll_number']); ?>" readonly>
                    <label for="semester">Semester:</label>
                    <select id="semester" name="semester">
                        <option value="1">1st Semester</option>
                        <option value="2">2nd Semester</option>
                        <option value="3">3rd Semester</option>
                        <option value="4">4th Semester</option>
                        <option value="5">5th Semester</option>
                        <option value="6">6th Semester</option>
                        <option value="7">7th Semester</option>
                        <option value="8">8th Semester</option>
                    </select>
                    <label for="year">Year:</label>
                    <input type="number" id="year" name="year" value="<?php echo date('Y'); ?>">
                    <label for="review_0">Review 0 Marks:</label>
                    <input type="number" id="review_0" name="review_0" min="0" max="100">
                    <label for="review_1">Review 1 Marks:</label>
                    <input type="number" id="review_1" name="review_1" min="0" max="100">
                    <label for="review_2">Review 2 Marks:</label>
                    <input type="number" id="review_2" name="review_2" min="0" max="100">
                    <label for="review_3">Review 3 Marks:</label>
                    <input type="number" id="review_3" name="review_3" min="0" max="100">
                    <label for="final_review">Final Review Marks:</label>
                    <input type="number" id="final_review" name="final_review" min="0" max="100">
                    <button type="submit" name="submit_marks">Submit Marks</button>
                </form>
            </div>
        </section>
    </div>
</section>


</body>
</html>