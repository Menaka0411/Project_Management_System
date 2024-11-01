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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch form data
    $name = $_POST['name'];
    $roll_number = $_POST['roll_number'];
    $univ_reg_no = $_POST['univ_reg_no'];
    $cgpa = $_POST['cgpa'];
    $degree = $_POST['degree'];
    $course = $_POST['course'];
    $batch_year = $_POST['batch_year']; 
    $email = $_POST['email'];
    $phno = $_POST['phno'];

    // Validate roll number
    $check_roll_number = $conn->prepare("SELECT * FROM student_details WHERE roll_number = ?");
    $check_roll_number->bind_param("s", $roll_number);
    $check_roll_number->execute();
    $result = $check_roll_number->get_result();

    if ($result->num_rows > 0) {
        echo "<p style='color: red;'>Error: Roll number already exists.</p>";
    } else {
        // Prepare and bind for student details
        $stmt = $conn->prepare("INSERT INTO student_details (name, roll_number, univ_reg_no, cgpa, degree, course, batch_year, email, phno) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $name, $roll_number, $univ_reg_no, $cgpa, $degree, $course, $batch_year, $email, $phno);

        // Execute the statement for student details
        if ($stmt->execute()) {
            // Set success message in the session
            $_SESSION['success_message'] = "Student added successfully.";
            
            // Redirect to the same page (add_stud.php)
            header("Location: add_stud.php");
            exit();
        } else {
            echo "<p style='color: red;'>Error: " . $stmt->error . "</p>";
        }

        // Close the statement
        $stmt->close();
    }

    // Close check statement
    $check_roll_number->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PMS Mentor Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/addstud.css">
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
            <li><a href="receive.php"><i class="fas fa-blog"></i>Submission</a></li>
            <li><a href="viewteams.php"><i class="fas fa-address-book"></i>Teams</a></li>
            <li><a href="cal.php"><i class="fas fa-calendar-alt"></i>Schedule</a></li>
        </ul>
    </div>

    <div class="main_header">
        <div class="header">
            <h1>ADD STUDENT</h1>
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
    <div class="stud_main">
        <form method="POST" action="">
            <div class="container">
                <!-- Student Details Section -->
                <div class="student-details">
                    <h3>Student Details</h3>
                    <input type="text" name="name" placeholder="Name" required value="<?php echo htmlspecialchars($name ?? ''); ?>">
                    <input type="text" name="roll_number" placeholder="Roll Number" required value="<?php echo htmlspecialchars($roll_number ?? ''); ?>">
                    <input type="text" name="univ_reg_no" placeholder="University Reg No" required value="<?php echo htmlspecialchars($univ_reg_no ?? ''); ?>">
                    <input type="text" name="cgpa" placeholder="CGPA" required value="<?php echo htmlspecialchars($cgpa ?? ''); ?>">
                    <input type="text" name="degree" placeholder="Degree" required value="<?php echo htmlspecialchars($degree ?? ''); ?>">
                    <input type="text" name="course" placeholder="Course" required value="<?php echo htmlspecialchars($course ?? ''); ?>">
                    <input type="text" name="batch_year" placeholder="Batch Year" required value="<?php echo htmlspecialchars($batch_year ?? ''); ?>"  oninput="validateBatchYear(this)">
                    <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
                    <input type="text" name="phno" placeholder="Phone Number" required value="<?php echo htmlspecialchars($phno ?? ''); ?>">
                </div>

                <!-- Submit Button -->
                <button type="submit" class="submit-btn">Submit</button>
            </div>
        </form>
    </div>

    <script src="assets/js/addstud.js"></script>
</body>
</html>