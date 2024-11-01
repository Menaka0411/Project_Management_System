<?php
session_start();
include 'includes/profile_pic.php';
$roll_number = $_SESSION['roll_number'] ?? 'N/A'; 
$student_id = $_SESSION['user_id']; 
$dashboard_data = $_SESSION['dashboard_data'] ?? null;
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PMS Student Dashboard</title>
    <link rel="stylesheet" href="mentors.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
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
        
        .main-content{
            margin-top:60px;
        }
    
    </style>
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
            <h1>SCHEDULE EVENTS</h1>
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
    <section id="calendar">
        
       <!-- <a href="events.html">add events</a> -->
    </section>
</section>

<!-- Modal for event details -->
<div id="eventModal">
    <div id="modalContent">
        <h3 id="modalTitle"></h3>
        <p id="modalDescription"></p>
        <button id="closeModal">Close</button>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize the calendar
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            editable: true, // Enable drag-and-drop
            events: [
                {
                    title: 'Project Submission Deadline',
                    start: '2024-10-10',
                    backgroundColor: '#ff5733' // Custom color for deadlines
                },
                {
                    title: 'Team Meeting',
                    start: '2024-10-12T10:00:00',
                    end: '2024-10-12T11:00:00', // Optional end date
                    backgroundColor: '#007bff' // Another event color
                },
                {
                    title: 'Client Review',
                    start: '2024-10-15',
                    allDay: true // For all-day events
                },
                {
                    title: 'Weekly Team Sync',
                    start: '2024-10-05T10:00:00',
                    rrule: {
                        freq: 'weekly',
                        interval: 1,
                        byweekday: ['mo', 'we', 'fr'] // Repeat every Monday, Wednesday, and Friday
                    },
                    backgroundColor: '#28a745' // Custom color for recurring events
                }
            ],
            dayRender: function(date, cell) {
                if (date.isSame(moment(), 'day')) {
                    cell.css("background-color", "#ffcc00"); // Highlight today's date
                }
            },
            eventClick: function(event) {
                $('#modalTitle').text(event.title);
                $('#modalDescription').text('Event Date: ' + event.start.format('MMMM Do YYYY'));
                $('#eventModal').show(); // Show the modal
            }
        });

        // Close modal functionality
        $('#closeModal').click(function() {
            $('#eventModal').hide(); // Hide the modal
        });
    });
</script>

</body>
</html>
