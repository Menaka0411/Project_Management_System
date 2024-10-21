<?php
session_start();

// Check if user is logged in and has the 'Staff' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Mentor') {
    header("Location: signin.php");
    exit();
}

// Retrieve user data from session
$email = $_SESSION['email'] ?? 'N/A'; 
$role = $_SESSION['role'] ?? 'N/A'; 
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; 

// Handle profile picture upload logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $user_id = $_SESSION['user_id']; 

    // Directory where the profile images will be saved
    $target_dir = "uploads/profile_pics/";
    $imageFileType = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . "." . $imageFileType; 
    $target_file = $target_dir . $new_filename;

    // Check if the file is an actual image
    $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
    if ($check !== false) {
        // Check file size (limit to 5MB)
        if ($_FILES["profile_pic"]["size"] < 5000000) {
            // Allow certain file formats
            if (in_array($imageFileType, ["jpg", "jpeg", "png"])) {
                if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                    // File successfully uploaded, save the path in the database

                    include 'db_connection.php'; // Ensure proper connection to your database

                    // Update user profile picture in the database
                    $sql = "UPDATE staff SET profile_image = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $target_file, $user_id);

                    if ($stmt->execute()) {
                        $_SESSION['profile_image'] = $target_file; 
                        header("Location: mentors_dash.php"); 
                        exit();
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }
                } else {
                    echo "Error uploading file.";
                }
            } else {
                echo "Only JPG, JPEG, and PNG files are allowed.";
            }
        } else {
            echo "File size exceeds the limit.";
        }
    } else {
        echo "File is not an image.";
    }
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

        .circle {
            position: relative;
            display: flex; /* Flexbox to center the circle */
            justify-content: center; /* Horizontally center the circle */
            align-items: center; /* Vertically center the circle */
            margin-top: 20px; /* Adjust to move the circle higher or lower */
            cursor: pointer; /* Make the whole circle clickable */
        }

        .profile-pic {
            width: 128px;
            height: 128px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.2);
            display: inline-block;
        }

        .p-image {
            position: absolute;
            bottom: 5px; /* Move to the bottom of the circle */
            right: 28%; /* Position to the right */
            color: #666666;
        }

        .upload-button {
            font-size: 1.2em;
        }

        .upload-button:hover {
            transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
            color: #999;
        }

        .file-upload {
            display: none; /* Hide the file input */
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
        </div>
        <h2 class="profile-email"><?php echo htmlspecialchars($email); ?></h2> <!-- Display email -->
        <p class="profile-role"><?php echo htmlspecialchars($role); ?></p> <!-- Display role -->
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
    <div class="mentor-details">
        <h2>Mentor Details</h2>
        <?php
        // Display mentor details
        if (!empty($mentor_data)) {
            echo '<h3>Mentor ID: ' . htmlspecialchars($mentor_data['mentor_id']) . '</h3>';
            echo '<p><strong>Name:</strong> ' . htmlspecialchars($mentor_data['name']) . '</p>';
            echo '<p><strong>Degree:</strong> ' . htmlspecialchars($mentor_data['degree']) . '</p>';
            echo '<p><strong>Department:</strong> ' . htmlspecialchars($mentor_data['department']) . '</p>';
            echo '<p><strong>Domain:</strong> ' . htmlspecialchars($mentor_data['domain']) . '</p>';
            echo '<p><strong>Phone Number:</strong> ' . htmlspecialchars($mentor_data['phone_number']) . '</p>';
            echo '<p><strong>Email:</strong> ' . htmlspecialchars($mentor_data['email']) . '</p>';
        } else {
            echo "<p>No mentor data available.</p>";
        }
        ?>
    </div>
    
    <section id="statistics">
        <h2>Mentor Dashboard</h2>
        <div class="content-items">
            <div class="info">
                <a href="cal.html">
                    <h2 class="info-heading">Calendar</h2>
                    <div class="info-details">
                        <h3 class="info-numbers">3</h3>
                    </div>
                </a>
            </div>
            <div class="info">
                <a href="viewteams.html">
                    <h2 class="info-heading">View Teams</h2>
                    <div class="info-details">
                        <h3 class="info-numbers">3</h3>
                    </div>
                </a>
            </div>
            <div class="info">
                <a href="Year.html">
                    <h2 class="info-heading">Year</h2>
                    <div class="info-details">
                        <h3 class="info-numbers">2</h3> 
                    </div>
                </a>
            </div>
            <div class="info">
                <a href="viewprojects.html">
                    <h2 class="info-heading">ALL Projects</h2>
                    <div class="info-details">
                        <h3 class="info-numbers">1</h3> 
                    </div>
                </a>
            </div>
        </div>
    </section>
</section>

<!-- Modal for event details -->
<!-- Add modal HTML if needed -->

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
