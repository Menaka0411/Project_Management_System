<?php
session_start();
include 'db_connection.php'; // Ensure your database connection is established
include 'includes/profile_pic.php';

// Ensure that the role is set to 'Staff'
if ($_SESSION['role'] !== 'Staff') {
    $_SESSION['role'] = 'Staff'; 
}

$username = $_SESSION['username'] ?? 'Vaishali'; 
$role = $_SESSION['role'];
$mentor_data = $_SESSION['mentor_data'] ?? null;
$dashboard_data = $_SESSION['dashboard_data'] ?? null;
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image

// Fetch teams and members from database
$teams = [];
$sql = "SELECT t.id AS team_id, t.team_name, t.department, t.year, GROUP_CONCAT(tm.member_name SEPARATOR ', ') AS members
        FROM teams t
        LEFT JOIN team_members tm ON t.id = tm.team_id
        GROUP BY t.id";


$result = $conn->query($sql); // Execute the query

if ($result->num_rows > 0) {
    // Fetch all teams
    while ($row = $result->fetch_assoc()) {
        $teams[] = $row; // Store the results in an array
    }
} else {
    echo "No teams found";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PMS Mentor Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/viewteams.css">
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
            <li><a href="projects.php"><i class="fas fa-address-card"></i>Projects</a></li>
            <li><a href="receive.php"><i class="fas fa-blog"></i>Submission</a></li>
            <li><a href="viewteams.php"><i class="fas fa-address-book"></i>Teams</a></li>
            <li><a href="cal.php"><i class="fas fa-calendar-alt"></i>Schedule</a></li>
        </ul>
    </div>
    <div class="main_header">
        <div class="header">
            <h1>Teams Under Your Mentorship</h1>
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
    <!-- Main content section -->
    <div class="main-content">

        <!-- Filter and search section -->
       <!-- Filter and search section -->
<div class="filter-options">
    <input type="text" id="teamSearch" placeholder="Search teams..." oninput="filterTeams()">
</div>


        <!-- Team cards section -->
        <section class="section-team">
            <?php foreach ($teams as $team): ?>
            <div class="team-card" data-team-id="<?php echo htmlspecialchars($team['team_id']); ?>"> 
                <h3><?php echo htmlspecialchars($team['team_name']); ?></h3>
                <p><strong>Department:</strong> <?php echo htmlspecialchars($team['department']); ?></p>
                <p><strong>Year:</strong> <?php echo htmlspecialchars($team['year']); ?></p>
                <p><strong>Team Members:</strong> <br><br><?php echo htmlspecialchars($team['members']); ?></p>
                <!-- <div class="action-buttons">
                    <button class="approve" onclick="updateProjectStatus(this, 'approve')">Approve</button>
                    <button class="disapprove" onclick="updateProjectStatus(this, 'disapprove')">Disapprove</button>
                </div> -->
            </div>
            <?php endforeach; ?>
        </section>
    </div>

    <script>
    // Filtering logic
    function filterTeams() {
        const searchInput = document.getElementById("teamSearch").value.toLowerCase();
        const teamCards = document.querySelectorAll(".team-card");

        teamCards.forEach(card => {
            const teamName = card.querySelector("h3").textContent.toLowerCase();

            // Check if the team name matches the search input
            const matchesSearch = teamName.includes(searchInput);

            if (matchesSearch) {
                card.style.display = ""; // Show the card
            } else {
                card.style.display = "none"; // Hide the card
            }
        });
    }

        function updateProjectStatus(buttonElement, action) {
            // Get the parent team-card element
            const teamCard = buttonElement.closest('.team-card');

            // Extract data attributes
            const teamId = teamCard.getAttribute('data-team-id');
            const teamName = teamCard.querySelector("h3").textContent;
            const department = teamCard.querySelector("p").textContent.match(/Department:\s*(.*)/)[1]; 
            const year = teamCard.querySelector("p").textContent.match(/Year:\s*(\d+)/)[1]; 
            const semester = teamCard.querySelector("p").textContent.match(/Semester:\s*(\d+)/)[1]; 
            const teamMembers = teamCard.querySelector("p").textContent.match(/Team Members:\s*(.*)/)[1];

            // Perform AJAX call to update project status in the database
            // Example AJAX call (implement this based on your backend API)
            // $.post('your_api_endpoint', { teamId, action, ... }, function(response) {
            //     // Handle response
            // });
            console.log(`Team ID: ${teamId}, Action: ${action}`); // Log for testing
        }
    </script>
</body>
</html>
