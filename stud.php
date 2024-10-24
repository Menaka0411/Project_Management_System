<?php
session_start();
include 'includes/profile_pic.php';
include 'db.php'; // Include the database connection file

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: signin.php");
    exit();
}
$roll_number = $_SESSION['roll_number'] ?? 'N/A'; 
$dashboard_data = $_SESSION['dashboard_data'] ?? null;
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image

// Function to get team notifications
function getTeamNotifications($conn, $studentId) {
    $notifications = [];
    $sql = "SELECT team_name FROM team_assignments WHERE student_id = ?"; // Change this query according to your database structure
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row['team_name'];
    }

    $stmt->close();
    return $notifications;
}

// Call the function
$studentId = $_SESSION['user_id']; // Assuming this holds the student ID
$team_notifications = getTeamNotifications($conn, $studentId);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PMS</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="mentors.css">
    <script src="https://kit.fontawesome.com/0f4e2bc10d.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <link rel="stylesheet" href="../../assets/vendor/aos/dist/aos.css">
    <script src="../../assets/vendor/aos/dist/aos.js"></script>
    <style>
        body {
            background-color: #efefef;
        }
        .main-content {
            margin-top: 100px;
            margin-left: 100px;
        }
        #notificationPopup {
            display: none;
            position: absolute;
            right: 20px;
            top: 70px;
            background: white;
            border: 1px solid #ccc;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            padding: 10px;
            border-radius: 5px;
        }
        #notificationPopup ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        #notificationPopup li {
            padding: 5px 10px;
            border-bottom: 1px solid #f0f0f0;
        }
        #notificationPopup li:last-child {
            border-bottom: none;
        }
    
    </style>
</head>
<body>

<div class="wrapper js-scroll-nav navbar navbar-light bg-light">
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
            <h1>STUDENT DASHBOARD</h1>
            <div class="header_icons">
                <div class="search">
                    <input type="text" placeholder="Search..." />
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <i class="fa-solid fa-bell" id="notificationButton"></i>
                <div id="notificationPopup">
                    <h3>Notifications</h3>
                    <ul>
                        <?php if (!empty($team_notifications)): ?>
                            <?php foreach ($team_notifications as $team): ?>
                                <li>You have been assigned to team: <?php echo htmlspecialchars($team); ?></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li>No team assignments yet.</li>
                        <?php endif; ?>
                    </ul>
                </div>
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
    <section id="notifications">
            <h2>Team Notifications</h2>
            <ul>
                <?php if (!empty($team_notifications)): ?>
                    <?php foreach ($team_notifications as $team): ?>
                        <li>You have been assigned to team: <?php echo htmlspecialchars($team); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>No team assignments yet.</li>
                <?php endif; ?>
            </ul>
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
     document.addEventListener("DOMContentLoaded", function () {
        const dropdownBtns = document.querySelectorAll('.dropdown-btn');
        
        dropdownBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                const dropdownContent = this.nextElementSibling;
                dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
            });
        });
    });
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
<script>
  $(document).on('ready', function () {
    // initialization of aos
    AOS.init({
      duration: 650,
      once: true
    });
  });
</script>
<script>
    // Pop-up notification toggle
    document.getElementById('notificationButton').addEventListener('click', function() {
        var popup = document.getElementById('notificationPopup');
        popup.style.display = popup.style.display === 'block' ? 'none' : 'block';
    });

    // Calendar setup
    $(document).ready(function() {
        $('#calendarBody').fullCalendar({
            editable: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            events: [],
            dayClick: function(date) {
                // Handle day click
            }
        });
    });
</script>
</body>
</html>
