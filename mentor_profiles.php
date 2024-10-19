

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
        .main-content{
            margin-top:100px;
        }
        body {
            font-family: 'Josefin Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4; /* Overall background color */
        }
        .wrapper {
            display: flex;
        }
        .sidebar {
            background: #4b4276; /* Sidebar color */
            color: white;
            width: 200px;
            padding: 20px;
        }
        .sidebar img {
            border-radius: 50%; /* Profile image rounded */
        }
        .sidebar h2 {
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
        .main_header {
            flex: 1; /* Take remaining space */
            padding: 20px;
            background: white; /* Header background */
        }
        .main-content {
            padding: 20px;
            flex: 1; /* Take remaining space */
        }
        #statistics{
            margin-left: 70px;
        }
        #statistics > h2{
            margin-left: 150px;
        }
        .main-content>h2{
            margin-left: 200px;
            padding: 10px 20px;
            background-color: #4e64bb;
            border-radius: 5px;
            border:2px solid #4e64bb;
        }
        .info>a{
            color: #4e64bb;
            font-weight: 400;

        }
        #calendar {
            max-width: 800px;  /* Adjust width as necessary */
            margin: 20px auto;  /* Center align */
            padding: 20px;  /* Padding around the calendar */
            border: 1px solid #ccc;  /* Light border for calendar */
            border-radius: 10px;  /* Rounded corners */
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);  /* Subtle shadow */
            background-color: #f9f9f9;  /* Light background color */
        }
        .fc-today {
            background-color: #ffcc00 !important; /* Highlight today */
        }
        .main-content{
            margin-top:100px;
        }
        .fc-event {
            border-radius: 5px; /* Rounded corners for events */
            color: white; /* Text color for events */
            border: none; /* Remove borders */
        }
        .fc-header-toolbar {
            background-color: #f1f1f1; /* Header background */
            padding: 10px 0; /* Header padding */
            border-bottom: 1px solid #ccc; /* Bottom border */
        }
        /* Modal Styles */
        #eventModal {
            display: none; /* Hidden by default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 20px;
        }
        #modalContent {
            background: #333;
            border-radius: 5px;
            padding: 20px;
            max-width: 300px;
            margin: auto;
            position: relative;
            top: 50%;
            transform: translateY(-50%);
        }
        .mentor-details {
            background-color: #ffffff;
            border: 1px solid #ccc;
            margin-left: 200px;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .mentor-info p {
            font-size: 16px;
            line-height: 1.6;
            margin: 10px 0;
        }
        .mentor-details h2 {
            margin-bottom: 10px;
            color: #4e64bb;
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
            <li><a href="mentor_profiles.php"><i class="fas fa-user"></i>Profile</a></li>
            <li><a href="projects.php"><i class="fas fa-address-card"></i>Projects</a></li>
            <li><a href="sub.php"><i class="fas fa-blog"></i>Submission</a></li>
            <li><a href="viewteams.php"><i class="fas fa-address-book"></i>Teams</a></li>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropdown-btn"><i class="fas fa-user"></i> Students</a>
                <div class="dropdown-container">
                    <a href="add_stud.php"><i class="fas fa-user-plus"></i> Add Students</a>
                    <a href="list_stud.php"><i class="fas fa-list"></i> List Students</a>
                </div>
            </li>
            <li><a href="projects.html"><i class="fas fa-address-card"></i>Projects</a></li>
            <li><a href="submission.html"><i class="fas fa-blog"></i>Submission</a></li>
            <li><a href="teams.html"><i class="fas fa-address-book"></i>Teams</a></li>
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
<section class="content_section_stud">
    
    
    
    <div class="additional-details">
        <form action="#" method="post">
            <div class="form-group">
                <label for="position">Position</label>
                <input type="text" id="position" name="position" value="Student" required>
            </div>
    
            <div class="form-group">
                <label for="join-date">Joining Date</label>
                <input type="date" id="join-date" name="join-date" value="2021-12-12" required>
            </div>
    
            <div class="form-group">
                <label for="cgpa">CGPA</label>
                <input type="number" step="0.01" id="cgpa" name="cgpa" value="9.00" required>
            </div>
    
            <div class="form-group">
                <label for="degree">Degree</label>
                <input type="text" id="degree" name="degree" value="Bachelor of Technology" required>
            </div>
    
            <div class="form-group">
                <label for="course">Course</label>
                <input type="text" id="course" name="course" value="Computer Science Engineering" required>
            </div>
    
            <div class="form-group">
                <label for="year">Year</label>
                <input type="text" id="year" name="year" value="4th Year" required>
            </div>
    
            <div class="form-group">
                <label for="email">Email Id</label>
                <input type="email" id="email" name="email" value="vaishali@gmail.com" required>
            </div>
    
            <div class="form-group">
                <label for="dob">Date of Birth</label>
                <input type="date" id="dob" name="dob" value="2003-12-12" required>
            </div>
    
            <div class="form-group">
                <label for="phno">Phone Number</label>
                <input type="tel" id="phno" name="phno" value="234567890" required>
            </div>
    
            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="asdfghjkl" required>
            </div>
    
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                
            </div>
    
            <div class="form-group">
                <button type="button">Edit</button>
                <button type="submit">Submit</button>
            </div>
        </form>
    </div>
</section>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dropdownBtns = document.querySelectorAll('.dropdown-btn');
        
        dropdownBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const dropdownContent = this.nextElementSibling;
                dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
            });
        });
    });
</script>

</body>
</html>
