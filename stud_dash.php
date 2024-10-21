<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header("Location: signin.php");
    exit();
}

// Retrieve user data from session
$roll_number = $_SESSION['roll_number'] ?? 'N/A'; // Default to 'N/A' if not set

// Safely retrieve dashboard data
$dashboard_data = $_SESSION['dashboard_data'] ?? null;

// Retrieve user profile image if exists
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
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
            background-color: #efefef;
        }
        .main-content {
            margin-top: 100px;
            margin-left: 100px;
        }
        .abstract {
            height: 200px;
            width: 700px;
            margin-left: 250px;
        }
        .abstract h2 {
            text-align: justify;
            font-size: 32px;
            font-weight: 600;
        }
        .h2-tag {
            text-transform: uppercase;
            margin-left: 100px;
        }
        .circle {
    position: relative; /* Allow positioning of the camera icon relative to the profile picture */
    display: inline-block; /* Ensure the circle is sized correctly */
}

.profile-pic {
    width: 128px; /* Adjusted size */
    height: 128px; /* Adjusted size */
    border-radius: 50%; /* Makes it round */
    border: 2px solid rgba(255, 255, 255, 0.2); /* Optional border */
    display: inline-block;
    margin: 20px auto; /* Centering the profile picture */
}

.p-image {
    position: absolute; /* Absolute positioning to overlap the profile image */
    top: 80px; /* Adjust this value to position above the profile image */
    right: 10px; /* Position slightly to the right */
    color: #666666;
    cursor: pointer; /* Change cursor on hover */
}

.upload-button {
    font-size: 1.2em;
}
        .upload-button:hover {
            transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
            color: #999;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
    <div class="circle">
        <img class="profile-pic" src="<?php echo htmlspecialchars($profile_image); ?>">
        <div class="p-image">
            <i class="fa fa-camera upload-button"></i>
            <input class="file-upload" type="file" accept="image/*"/>
        </div>
    </div>

        <h2 class="profile-roll"><?php echo htmlspecialchars($roll_number); ?></h2>
        <ul>
            <li><a href="stud_dash.php"><i class="fas fa-home"></i>Home</a></li>
            <li><a href="stud_profiles.php"><i class="fas fa-user"></i>Profile</a></li>
            <li><a href="stud_projects.php"><i class="fas fa-address-card"></i>Projects</a></li>
            <li><a href="stud_mentors.php"><i class="fas fa-project-diagram"></i>Mentors</a></li>

            <li class="dropdown">
                <a href="javascript:void(0)" class="dropdown-btn"><i class="fas fa-user"></i> Submission</a>
                <div class="dropdown-container">
                    <a href="add_sub.php"><i class="fas fa-user-plus"></i> Add Submission</a>
                    <a href="list_sub.php"><i class="fas fa-list"></i> List Submission</a>
                </div>
            </li>
            <li><a href="create_teams.php"><i class="fas fa-address-book"></i>Teams</a></li>
            <li><a href="stud_editor.php"><i class="fas fa-address-book"></i>Editor</a></li>
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
    <section id="statistics">
        <h2 class="h2-tag">Project Statistics</h2>
        <div class="content-items">
            <div class="info">
                <h2 class="info-heading">Total Projects</h2>
                <div class="info-details">
                    <h3 class="info-numbers"><?php echo $dashboard_data['total_projects'] ?? '0'; ?></h3>
                </div>
            </div>
            <div class="info">
                <h2 class="info-heading">Completed Projects</h2>
                <div class="info-details">
                    <h3 class="info-numbers"><?php echo $dashboard_data['completed_projects'] ?? '0'; ?></h3>
                </div>
            </div>
            <div class="info">
                <h2 class="info-heading">Ongoing Projects</h2>
                <div class="info-details">
                    <h3 class="info-numbers"><?php echo $dashboard_data['ongoing_projects'] ?? '0'; ?></h3>
                </div>
            </div>
            <div class="info">
                <h2 class="info-heading">Overdue Projects</h2>
                <div class="info-details">
                    <h3 class="info-numbers"><?php echo $dashboard_data['overdue_projects'] ?? '0'; ?></h3>
                </div>
            </div>
        </div>
    </section>

    <section id="calendar">
        <h2>Calendar <a href="cal.html">view</a></h2>
        <div class="calendar-container">
            <div class="calendar-header">
                <button id="prevYear" onclick="changeYear(-1)">&#10094;&#10094;</button>
                <button id="prevMonth" onclick="changeMonth(-1)">&#10094;</button>
                <h3 id="currentMonthYear"></h3>
                <button id="nextMonth" onclick="changeMonth(1)">&#10095;</button>
                <button id="nextYear" onclick="changeYear(1)">&#10095;&#10095;</button>
            </div>
            <div class="calendar-days">
                <div class="day">Sun</div>
                <div class="day">Mon</div>
                <div class="day">Tue</div>
                <div class="day">Wed</div>
                <div class="day">Thu</div>
                <div class="day">Fri</div>
                <div class="day">Sat</div>
            </div>
            <div class="calendar-dates" id="calendarDates"></div>
        </div>
    </section>
</section>

<script src="assets/js/calendar.js"></script>
<script>
$(document).ready(function() {
    var readURL = function(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('.profile-pic').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $(".file-upload").on('change', function(){
        readURL(this);
    });

    $(".upload-button").on('click', function() {
       $(".file-upload").click();
    });
});
</script>

</body>
</html>
