<?php
session_start();
include 'db_connection.php';
include 'includes/profile_pic.php';
$username = $_SESSION['username'] ?? 'Vaishali'; 
$role = $_SESSION['role'] ?? 'N/A';
$mentor_data = $_SESSION['mentor_data'] ?? null;
$dashboard_data = $_SESSION['dashboard_data'] ?? null;
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_id = $_POST['team_id'];
    $mentor_id = $_POST['mentor_id'];

    // Update the team to assign the mentor
    $query = "UPDATE teams SET mentor_id = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $mentor_id, $team_id);

    if ($stmt->execute()) {
        echo "Mentor has been successfully allocated to the team!";
    } else {
        echo "Error allocating mentor: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PMS Mentor Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/allocate-mentor.css">
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
        <h2 class="profile-email"><?php echo htmlspecialchars($username); ?></h2>
        <p class="profile-role" style="text-align: center;"><?php echo htmlspecialchars($role); ?></p> 
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
            <li><a href="cal.php"><i class="fas fa-calendar-alt"></i>Schedule</a></li>
        </ul>
    </div>


    <div class="main_header">
        <div class="header">
            <h1>ALLOCATE MENTORS</h1>
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

<section id="allocate">
    <h2>Allocate Mentors to Teams</h2>
    <div class="container mx-auto mt-10">
        
        <table>
            <thead>
                <tr>
                    <th>Team</th>
                    <th>Mentor</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
         
                    <?php
                        // Fetch teams from the database
                        $teams_query = "SELECT id, team_name FROM teams";
                        $teams_result = mysqli_query($conn, $teams_query);

                        while ($team = mysqli_fetch_assoc($teams_result)) {
                            $mentors_query = "SELECT id, email FROM staff WHERE role = 'Mentor'";
                            $mentors_result = mysqli_query($conn, $mentors_query);
                            echo "<tr>";
                            echo "<td>{$team['team_name']}</td>";  // Team name
                            echo "<td>";
                            echo "<form method='POST' action='mentors_dash.php' class='inline'>";  // Form for each team
                            echo "<select name='mentor_id' class='p-2 border border-gray-300 rounded-lg'>";
                            while ($mentor = mysqli_fetch_assoc($mentors_result)) {
                                echo "<option value='{$mentor['id']}'>{$mentor['email']}</option>";
                            }
                            echo "</select>";
                            echo "<input type='hidden' name='team_id' value='{$team['id']}'>";
                            echo "</td>";  // Closing the mentor select dropdown's <td>
                            echo "<td>";  // New column for action button
                            echo "<button type='submit'>Allocate</button>";  // Button in the action column
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    ?>
                

            </tbody>
        </table>
        <?php if (isset($allocation_message)): ?>
            <div id="allocation-result"><?php echo $allocation_message; ?></div>
        <?php endif; ?>
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
    document.querySelector('.dropdown-btn').addEventListener('click', function() {
    var dropdownContent = this.nextElementSibling;
    dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
});


    
</script>

</body>
</html>
<?php
session_start();
include 'includes/profile_pic.php';


// Retrieve user data from session
$username = $_SESSION['username'] ?? 'Vaishali'; // Default to 'N/A' if not set
$role = $_SESSION['role'] ?? 'N/A'; // Default to 'N/A' if not set
$mentor_data = $_SESSION['mentor_data'] ?? null;
// Safely retrieve dashboard data
$dashboard_data = $_SESSION['dashboard_data'] ?? null;

// Retrieve user profile image if exists
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image
?>
<?php
include 'db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_id = $_POST['team_id'];
    $mentor_id = $_POST['mentor_id'];

    // Update the team to assign the mentor
    $query = "UPDATE teams SET mentor_id = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $mentor_id, $team_id);

    if ($stmt->execute()) {
        echo "Mentor has been successfully allocated to the team!";
    } else {
        echo "Error allocating mentor: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
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
        form div {
    margin-bottom: 15px;
}

form label {
    margin-right: 10px;
}

form select {
    padding: 5px;
}

button {
    padding: 8px 12px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
}

button:hover {
    background-color: #0056b3;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

table th, table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
    border-left: 1px solid black;  /* Left border between columns */
    border-right: 1px solid black;
}

table th {
    background-color: #343a40; /* Dark background for header */
    color: white; /* White text for header */
    font-weight: bold; /* Bold font */
}

table tr:hover {
    background-color: #f1f1f1; /* Light gray background on hover */
}

table td {
    background-color: #ffffff; /* White background for table data */
}

table td form {
    display: flex;
    align-items: center;
    justify-content: space-between;
}
#allocate{
    margin-left: 20%;
    margin-top: 10%;
}
button {
    padding: 8px 12px;
    background-color: #007bff; /* Blue background */
    color: white; /* White text */
    border: none; /* No border */
    cursor: pointer; /* Pointer cursor on hover */
    border-radius: 5px; /* Rounded corners */
    transition: background-color 0.3s ease; /* Smooth background change */
}

button:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

#allocation-result {
    margin-top: 10px;
    color: green; /* Green text for success messages */
    font-weight: bold; /* Bold font for messages */
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

<section id="allocate">
    <h2>Allocate Mentors to Teams</h2>
    <div class="container mx-auto mt-10">
        
        <table>
            <thead>
                <tr>
                    <th>Team</th>
                    <th>Mentor</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
         
                    <?php
                        // Fetch teams from the database
                        $teams_query = "SELECT id, team_name FROM teams";
                        $teams_result = mysqli_query($conn, $teams_query);

                        while ($team = mysqli_fetch_assoc($teams_result)) {
                            $mentors_query = "SELECT id, email FROM staff WHERE role = 'Mentor'";
                            $mentors_result = mysqli_query($conn, $mentors_query);
                            echo "<tr>";
                            echo "<td>{$team['team_name']}</td>";  // Team name
                            echo "<td>";
                            echo "<form method='POST' action='mentors_dash.php' class='inline'>";  // Form for each team
                            echo "<select name='mentor_id' class='p-2 border border-gray-300 rounded-lg'>";
                            while ($mentor = mysqli_fetch_assoc($mentors_result)) {
                                echo "<option value='{$mentor['id']}'>{$mentor['email']}</option>";
                            }
                            echo "</select>";
                            echo "<input type='hidden' name='team_id' value='{$team['id']}'>";
                            echo "</td>";  // Closing the mentor select dropdown's <td>
                            echo "<td>";  // New column for action button
                            echo "<button type='submit'>Allocate</button>";  // Button in the action column
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    ?>
                

            </tbody>
        </table>
        <?php if (isset($allocation_message)): ?>
            <div id="allocation-result"><?php echo $allocation_message; ?></div>
        <?php endif; ?>
    </div>
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


    
</script>

</body>
</html>
