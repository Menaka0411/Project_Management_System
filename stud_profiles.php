<?php
session_start();
include 'db_connection.php'; 
include 'includes/profile_pic.php';

// Validate login and get roll number from session
$roll_number = $_SESSION['roll_number'] ?? null;
$dashboard_data = $_SESSION['dashboard_data'] ?? null;
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image

if (!$roll_number) {
    echo "You need to log in first.";
    exit();
}

// Fetch student details
$sql_details = "SELECT * FROM student_details WHERE roll_number = ?";
$stmt = $conn->prepare($sql_details);
$stmt->bind_param("s", $roll_number);
$stmt->execute();
$student_details = $stmt->get_result()->fetch_assoc();

if (!$student_details) {
    echo "Student details not found.";
    exit();
}

// Fetch project marks
$sql_marks = "SELECT * FROM student_project_marks WHERE roll_number = ?";
$stmt_marks = $conn->prepare($sql_marks);
$stmt_marks->bind_param("s", $roll_number);
$stmt_marks->execute();
$project_marks = $stmt_marks->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch attendance data
$sql_attendance = "SELECT * FROM attendance WHERE roll_number = ? ORDER BY attendance_date DESC";
$stmt_attendance = $conn->prepare($sql_attendance);
$stmt_attendance->bind_param("s", $roll_number);
$stmt_attendance->execute();
$attendance_data = $stmt_attendance->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$stmt_marks->close();
$stmt_attendance->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <title>PMS</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/stud_profile.css"> 
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
            <h2 class="profile-roll"><?php echo htmlspecialchars($roll_number); ?></h2>
        <ul>
            <li><a href="stud_dash.php"><i class="fas fa-home"></i>Home</a></li>
            <li><a href="stud_profiles.php"><i class="fas fa-user"></i>Profile</a></li>
            <li><a href="stud_projects.php"><i class="fas fa-address-card"></i>Projects</a></li>
            <li><a href="stud_mentors.php"><i class="fas fa-project-diagram"></i>Mentors</a></li>

            <li class="dropdown">
                <a href="javascript:void(0)" class="dropdown-btn"><i class="fas fa-user"></i> Submission</a>
                <div class="dropdown-container">
                    <a href="stud_submission.php"><i class="fas fa-user-plus"></i> Add Submission</a>
                    <a href="stud_list.php"><i class="fas fa-list"></i> List Submission</a>
                </div>
            </li>
            <li><a href="create_teams.php"><i class="fas fa-address-book"></i>Teams</a></li>
        </ul>
    </div>

    <div class="main_header">
        <div class="header">
            <h1>Profile</h1>
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
<div class="container">
    <div class="profile-layout">
        <div class="student-details">
            <h2>Student Details</h2>
            <p>Name: <?= htmlspecialchars($student_details['name']) ?></p>
            <p>Roll Number: <?= htmlspecialchars($student_details['roll_number']) ?></p>
            <p>University Registration No: <?= htmlspecialchars($student_details['univ_reg_no']) ?></p>
            <p>CGPA: <?= htmlspecialchars($student_details['cgpa']) ?></p>
            <p>Degree: <?= htmlspecialchars($student_details['degree']) ?></p>
            <p>Course: <?= htmlspecialchars($student_details['course']) ?></p>
            <p>Batch Year: <?= htmlspecialchars($student_details['batch_year']) ?></p>
            <p>Email: <?= htmlspecialchars($student_details['email']) ?></p>
            <p>Phone No: <?= htmlspecialchars($student_details['phno']) ?></p>
        </div>

        <div class="marks-layout">
            <div class="academic-marks">
                <h2>Academic Marks</h2>
                <?php if (!empty($project_marks)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Semester</th>
                                <th>Year</th>
                                <th>Review 0</th>
                                <th>Review 1</th>
                                <th>Review 2</th>
                                <th>Review 3</th>
                                <th>Final Review</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($project_marks as $mark): ?>
                                <tr>
                                    <td><?= htmlspecialchars($mark['semester']) ?></td>
                                    <td><?= htmlspecialchars($mark['year']) ?></td>
                                    <td><?= htmlspecialchars($mark['review_0']) ?></td>
                                    <td><?= htmlspecialchars($mark['review_1']) ?></td>
                                    <td><?= htmlspecialchars($mark['review_2']) ?></td>
                                    <td><?= htmlspecialchars($mark['review_3']) ?></td>
                                    <td><?= htmlspecialchars($mark['final_review']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No academic marks available.</p>
                <?php endif; ?>
            </div>

            <div class="attendance-marks">
                <h2>Attendance Marks</h2>
                <?php if (!empty($attendance_data)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Roll Number</th>
                                <th>Week Number</th>
                                <th>Review Number</th>
                                <th>Status</th>
                                <th>Attendance Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendance_data as $attendance): ?>
                                <tr>
                                    <td><?= htmlspecialchars($attendance['id']) ?></td>
                                    <td><?= htmlspecialchars($attendance['roll_number']) ?></td>
                                    <td><?= htmlspecialchars($attendance['week_number']) ?></td>
                                    <td><?= htmlspecialchars($attendance['review_number']) ?></td>
                                    <td><?= htmlspecialchars($attendance['status']) ?></td>
                                    <td><?= htmlspecialchars($attendance['attendance_date']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No attendance data available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

</body>
</html>
