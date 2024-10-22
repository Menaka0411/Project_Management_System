
<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "teams_management";
//$dbname = "project_management_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if roll_number is set in the query string
if (isset($_GET['roll_number'])) 
{
        $roll_number = $_GET['roll_number'];

        // Fetch student details based on roll number
        $sql = "SELECT id, name, roll_number, degree, course, batch_year, email, phno FROM student_details WHERE roll_number = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $roll_number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $student = $result->fetch_assoc();
        } else {
            $student = null; // No student found
        }
} 
else 
{
    $student = null; // No roll_number provided
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

    // Insert marks into the database
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
    $roll_number = $_POST['roll_number'];  // Get roll number from form
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
    <title>Mentors</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="mentors.css">
    <script src="https://kit.fontawesome.com/0f4e2bc10d.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <style>
        body {
            font-family: 'Josefin Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4; /* Overall background color */
        }
        .wrapper {
            display: flex;
            flex-direction: row;
        }
        .sidebar {
            background: #4b4276; /* Sidebar color */
            color: white;
            width: 200px;
            height: 100vh;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar img {
            border-radius: 50%; /* Profile image rounded */
        }
        .sidebar h2 {
            text-align: center;
            font-size: 20px; /* Profile name font size */
        }
        .sidebar ul {
            list-style-type: none; /* Remove bullet points */
            padding: 0;
        }
        .sidebar ul li {
            margin: 15px 0; /* Space between menu items */
        }
        .sidebar ul li a {
            color: white; /* Menu item color */
            text-decoration: none; /* Remove underline */
        }
        .main-content {
            margin-top: 5%;
            display: flex;
            justify-content: space-evenly;
            flex-wrap: wrap; 
            flex: 1; /* Take remaining space */
        }
        #statistics-data{
            
            padding: 20px;
            border-radius: 10px;
            margin-left: 10%;
           
        }
        #statistics-data>h1{
            font-size: 28px;
            color:#4b4276;
            text-align: center;
            padding: 10px;
            margin-top: 10px;
        }
        #statistics {
            
            margin: 10px 0; /* Margin for spacing between sections */
            
            padding-left: 20px;
            padding-right: 20px;
            
            
            width: 100%; /* Full width */
            max-width: 600px;
             /* Restricting width for a neat layout */
        }
        #statistics h1 {
            text-align: center;
            font-size: 28px;
            color: #4b4276;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .profile-container {
         background-color: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin-top: 10px;
            margin-left: 5%;
        }
        .profile-container h2 {
            font-size: 20px;

            color: #333;
            margin: 20px 0;
            text-align: center; 
        }
        .profile-container p {
            font-size: 18px;
            margin: 10px 0;
            line-height: 1.6;
            color:#555;
        }
        .profile-container p strong {
            color: #4e64bb;
        }
        .marks-entry-form {
            max-width: 600px;
            margin: 20px auto; /* Centering the form */
            padding: 20px;
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .marks-entry-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 15px;
            color: #333;
        }
        .marks-entry-form-attendance label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }
        .marks-entry-form input[type="text"],.marks-entry-form-attendance input[type="text"],
        .marks-entry-form input[type="number"],.marks-entry-form-attendance input[type="number"],
        .marks-entry-form select,.marks-entry-form-attendance select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color:#f9f9f9;
            font-size: 14px;
            color: #333;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        .marks-entry-form input[type="text"]:focus,.marks-entry-form-attendance input[type="text"]:focus,

        .marks-entry-form input[type="number"]:focus,.marks-entry-form-attendance input[type="number"]:focus,

        .marks-entry-form select:focus,.marks-entry-form-attendance select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .marks-entry-form button,.marks-entry-form-attendance {
            width: 100%;
            padding: 12px;
            background-color: #4e64bb;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .marks-entry-form-attendance button {
            width: 100%;
            padding: 12px;
            background-color: #4e64bb;
            color: #fff;
            font-size: 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .marks-entry-form button:hover,.marks-entry-form-attendance button:hover {
            background-color: #0056b3;
        }
        .marks-entry-form {
            
            margin-bottom: 20px;
            color: #444;
            font-size: 24px;
        }
        .marks-entry-form h2{
            font-size: 20px;
            color:#4b4276;
        }
        .marks_attendance_data{
            display: flex;
            justify-content: space-evenly;
            align-items: flex-start;
            gap: 20px; /* Spacing between both sections */
            margin: 20px;
            margin-left: 15%;
            flex-wrap: wrap; /* Allow wrapping for smaller screens */
        }
        .marks-entry-form-attendance{
            
            width:600px;
            margin: 20px auto; /* Centering the form */
            padding: 20px;
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .student-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin: 20px 0;
            padding-left:25%;
           
        }

        .student-info div {
            display: flex;
            justify-content: space-between;
            width: 100%;
            margin-bottom: 10px;
        }
        .student-info div strong {
            color: #4e64bb; /* Make the text stand out with a unique color */
            font-weight: bold; /* Ensure it's bold */
           
            text-transform: capitalize; /* Make the text uppercase for stronger emphasis */
            /* Add some spacing between letters for readability */
        }
        strong {
            color: #4e64bb; /* Color for strong text */
            /* Bold text */
    /* Larger font size for emphasis */
        }
        .info-pair {
            
            display: flex; /* Align label and value horizontally */
            justify-content: space-between; /* Space between labels and values */
        }
        @media screen and (max-width: 768px) {
            .student-info div {
                width: 100%; /* Full width on small screens */
            }
        }
        span {
            font-size: 15px;
            font-weight: 500;
    flex-grow: 1; /* Allow the span to take available space */
}

    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <img src="assets/img/girlprofile.png" alt="" width="100px" />
        <h2 class="profile-name"><?php echo isset($mentor_data['name']) ? htmlspecialchars($mentor_data['name']) : ''; ?></h2>
        <h2 class="profile-roll"><?php echo isset($mentor_data['department']) ? htmlspecialchars($mentor_data['department']) : ''; ?></h2>
        <ul>
            <li><a href="mentors_dash.php"><i class="fas fa-home"></i>Home</a></li>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropdown-btn"><i class="fas fa-user"></i> Students</a>
                <div class="dropdown-container">
                    <a href="stud_submission.php"><i class="fas fa-user-plus"></i> Add Submission</a>
                    <a href="list_subission.php"><i class="fas fa-list"></i> List Submission</a>
                </div>
            </li>
            <li><a href="projects.html"><i class="fas fa-address-card"></i>Projects</a></li>
            <li><a href="submission.html"><i class="fas fa-blog"></i>Submission</a></li>
            <li><a href="viewteams.php"><i class="fas fa-address-book"></i>Teams</a></li>
            <li><a href="cal.html"><i class="fas fa-calendar-alt"></i>Schedule</a></li>
        </ul>
        
    </div>


    <div class="main_header">
        <div class="header">
            <h1>PROJECT MANAGEMENT</h1>
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
<section id="statistics-data">
    <h1>Student Profile</h1>
    <div class="profile-container">
        <h2><?php echo htmlspecialchars($student['name']); ?></h2>
        <div class="student-info">
            <div class="info-pair"><strong>Roll Number:</strong> <span> <?php echo htmlspecialchars($student['roll_number']); ?> </span></div>
            <div class="info-pair"><strong>Degree:</strong>  <span><?php echo htmlspecialchars($student['degree']); ?></span></div>
            <div class="info-pair"><strong>Course:</strong>  <span><?php echo htmlspecialchars($student['course']); ?></span></div>
            <div class="info-pair"><strong>Batch Year:</strong>  <span><?php echo htmlspecialchars($student['batch_year']); ?></span></div>
            <div class="info-pair"><strong>Email:</strong>  <span><?php echo htmlspecialchars($student['email']); ?></span></div>
            <div class="info-pair"><strong>Phone Number:</strong>  <span><?php echo htmlspecialchars($student['phno']); ?></span></div>
        </div>
    </div>
    <section id="statistics">
        <h1>Student Attendance</h1>
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
</section>
<section id="statistics">
            <h1>Project Marks</h1>
                <div class="marks-entry-form">
                    
                <form  method="POST" action="">
                    <h2>Enter Student Marks</h2>
                    <input type="hidden" name="student_id" value="<?php echo isset($student['id']) ? $student['id'] : ''; ?>">
                    <label for="roll_number">Roll Number:</label>
                    <input type="text" id="roll_number" name="roll_number" value="<?php echo isset($student['roll_number']) ? $student['roll_number'] : ''; ?>" readonly>

                    <label for="semester">Semester:</label>
                    <select id="semester" name="semester">
                        <option value="1">1st Semester</option>
                        <option value="2">2nd Semester</option>
                        <option value="3">3rd Semester</option>
                        <!-- Add other semesters here -->
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

            
         </section>
    
</section>


<!-- Modal for event details -->
<!-- Add modal HTML if needed -->


</body>
</html>
