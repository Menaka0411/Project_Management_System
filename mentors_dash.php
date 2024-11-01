<?php
session_start();
include 'includes/profile_pic.php';
if (!isset($_SESSION['user_id']) || $_SESSION['username'] == 'staff') {
    header("Location: signin.php");
    exit();
}

if ($_SESSION['role'] !== 'Staff') {
    $_SESSION['role'] = 'Staff'; // Reset role to 'Staff' for this session
}

$username = $_SESSION['username'] ?? 'Vaishali'; 
$role = $_SESSION['role']; // Now this will always reflect the session role

$mentor_data = $_SESSION['mentor_data'] ?? null;
$dashboard_data = $_SESSION['dashboard_data'] ?? null;
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PMS Mentor Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
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
    <div class="mentor-details">
        <h2>Mentor Details</h2>
        <?php
        // Display mentor details
        if (!empty($mentor_data)) {
            echo '<h3>Mentor ID: ' . htmlspecialchars($mentor_data['mentor_id']) . '</h3>';
            echo '<p><strong>Name:</strong> ' . htmlspecialchars($mentor_data['name']) . '</p>';
            echo '<p><strong>Role:</strong> ' . htmlspecialchars($mentor_data['role']) . '</p>';
            echo '<p><strong>Department:</strong> ' . htmlspecialchars($mentor_data['department']) . '</p>';
            // echo '<p><strong>Domain:</strong> ' . htmlspecialchars($mentor_data['domain']) . '</p>';
            echo '<p><strong>Phone Number:</strong> ' . htmlspecialchars($mentor_data['phone_number']) . '</p>';
            echo '<p><strong>Email:</strong> ' . htmlspecialchars($mentor_data['email']) . '</p>';
        } else {
            // Default user details when no mentor data is available
            echo '<h3>Mentor ID: 1</h3>';
            echo '<p><strong>Name:</strong> Vaishali</p>';
            echo '<p><strong>Role:</strong> HOD</p>';
            echo '<p><strong>Department:</strong> Computer Science Engineering</p>';
            // echo '<p><strong>Domain:</strong></p>';
            echo '<p><strong>Phone Number:</strong> +91 9632587014</p>';
            echo '<p><strong>Email:</strong> vaishali@gmail.com</p>';
        }
        
        ?>
    </div>
    
    <section id="statistics">
        <h2>Mentor Dashboard</h2>
        <div class="content-items">
            <div class="info">
                <a href="cal.php">
                    <h2 class="info-heading">Calendar</h2>
                    <div class="info-details">
                        <h3 class="info-numbers">4</h3>
                    </div>
                </a>
            </div>
            <div class="info">
                <a href="viewteams.php">
                    <h2 class="info-heading">View Teams</h2>
                    <div class="info-details">
                        <h3 class="info-numbers">3</h3>
                    </div>
                </a>
            </div>
            <div class="info">
                <a href="allocate_mentor.php">
                    <h2 class="info-heading">Mentor Allocation</h2>
                    <div class="info-details">
                        <h3 class="info-numbers">Team</h3> 
                    </div>
                </a>
            </div>
            <div class="info">
                <a href="projects.php">
                    <h2 class="info-heading">Total Projects</h2>
                    <div class="info-details">
                        <h3 class="info-numbers">3</h3> 
                    </div>
                </a>
            </div>
        </div>
    </section>
</section>
<section class="allocate-mentor">


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
    document.querySelector('.dropdown-btn').addEventListener('click', function() {
    var dropdownContent = this.nextElementSibling;
    dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
});


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
