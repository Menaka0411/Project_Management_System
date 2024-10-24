<?php
session_start();
include 'db_connection.php';
include 'includes/profile_pic.php';

 $username = $_SESSION['username'] ?? 'Vaishali'; 
 $role = $_SESSION['role'] ?? 'N/A';
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
        <div class="filter-options">
            <select id="departmentFilter" onchange="filterTeams()">
                <option value="">All Departments</option>
                <option value="CS">Computer Science</option>
                <option value="ME">Mechanical Engineering</option>
            </select>
            <input type="text" id="teamSearch" placeholder="Search teams..." oninput="filterTeams()">
        </div>

        <!-- Team cards section -->
        <section class="section-team">
            <div class="team-card" data-team-id="team12"> 
                <h3>Team Gamma</h3>
                <p><strong>Department:</strong> Computer Science</p>
                <p><strong>Year:</strong> 2024</p>
                <p><strong>Semester:</strong> 1</p>
                <p><strong>Team Members:</strong> Alice, Bob, Charlie</p>
                <div class="action-buttons">
                    <button class="approve" onclick="updateProjectStatus(this, 'approve')">Approve</button>
                    <button class="disapprove" onclick="updateProjectStatus(this, 'disapprove')">Disapprove</button>
                </div>
            </div>
            <div class="team-card" data-team-id="team123"> 
                <h3>Team Gamma</h3>
                <p><strong>Department:</strong> Computer Science</p>
                <p><strong>Year:</strong> 2024</p>
                <p><strong>Semester:</strong> 1</p>
                <p><strong>Team Members:</strong> Alice, Bob, Charlie</p>
                <div class="action-buttons" >
                    <button class="approve" onclick="updateProjectStatus(this, 'approve')">Approve</button>
                    <button class="disapprove" onclick="updateProjectStatus(this, 'disapprove')">Disapprove</button>
                </div>
            </div>
            <div class="team-card" data-team-id="team18"> 
                <h3>Team Gamma</h3>
                <p><strong>Department:</strong> Computer Science</p>
                <p><strong>Year:</strong> 2024</p>
                <p><strong>Semester:</strong> 1</p>
                <p><strong>Team Members:</strong> Alice, Bob, Charlie</p>
                <div class="action-buttons">
                    <button class="approve" onclick="updateProjectStatus(this, 'approve')">Approve</button>
                    <button class="disapprove" onclick="updateProjectStatus(this, 'disapprove')">Disapprove</button>
                </div>
            </div>
            <div class="team-card" data-team-id="team15">
                <h3>Team Gamma</h3>
                <p><strong>Department:</strong> Computer Science</p>
                <p><strong>Year:</strong> 2024</p>
                <p><strong>Semester:</strong> 1</p>
                <p><strong>Team Members:</strong> Alice, Bob, Charlie</p>
                <div class="action-buttons">
                    <button class="approve" onclick="updateProjectStatus(this, 'approve')">Approve</button>
                    <button class="disapprove" onclick="updateProjectStatus(this, 'disapprove')">Disapprove</button>
                </div>
            </div>
            <div class="team-card" data-team-id="team155">
                <h3>Team Gamma</h3>
                <p><strong>Department:</strong> Computer Science</p>
                <p><strong>Year:</strong> 2024</p>
                <p><strong>Semester:</strong> 1</p>
                <p><strong>Team Members:</strong> Alice, Bob, Charlie</p>
                <div class="action-buttons">
                    <button class="approve" onclick="updateProjectStatus(this, 'approve')">Approve</button>
                    <button class="disapprove" onclick="updateProjectStatus(this, 'disapprove')">Disapprove</button>
                </div>
            </div>

            <div class="team-card" data-team-id="team193">
                <h3>Team Alpha</h3>
                <p><strong>Department:</strong> Computer Science</p>
                <p><strong>Year:</strong> 2024</p>
                <p><strong>Semester:</strong> 1</p>
                <p><strong>Team Members:</strong> Alice, Bob, Charlie</p>
                <div class="action-buttons">
                    <button class="approve" onclick="updateProjectStatus(this, 'approve')">Approve</button>
                    <button class="disapprove" onclick="updateProjectStatus(this, 'disapprove')">Disapprove</button>
                </div>
            </div>

            <div class="team-card" data-team-id="team456"> 
                <h3>Team Beta</h3>
                <p><strong>Department:</strong> Mechanical Engineering</p>
                <p><strong>Year:</strong> 2024</p>
                <p><strong>Semester:</strong> 1</p>
                <p><strong>Team Members:</strong> David, Emma, Frank</p>
                <div class="action-buttons">
                    <button class="approve" onclick="updateProjectStatus(this, 'approve')">Approve</button>
                    <button class="disapprove" onclick="updateProjectStatus(this, 'disapprove')">Disapprove</button>
                </div>
            </div>
        </section>
    </div>

    <script>
        // Filtering logic
        function filterTeams() {
            const searchInput = document.getElementById("teamSearch").value.toLowerCase();
            const departmentFilter = document.getElementById("departmentFilter").value;
            const teamCards = document.querySelectorAll(".team-card");

            teamCards.forEach(card => {
                const teamName = card.querySelector("h3").textContent.toLowerCase();
                const department = card.querySelector("p").textContent.toLowerCase();

                const matchesSearch = teamName.includes(searchInput);
                const matchesDepartment = departmentFilter === "" || department.includes(departmentFilter.toLowerCase());

                if (matchesSearch && matchesDepartment) {
                    card.style.display = ""; // Show the card
                } else {
                    card.style.display = "none"; // Hide the card
                }
            });
        }

        
        function updateProjectStatus(buttonElement, action)
         {
                // Get the parent team-card element
                const teamCard = buttonElement.closest('.team-card');

                // Extract data attributes
                const teamId = teamCard.getAttribute('data-team-id'); // Add this attribute to your team cards
                const teamName = teamCard.querySelector("h3").textContent;
                const department = teamCard.querySelector("p").textContent.match(/Department:\s*(.*)/)[1]; // Adjusted for correct extraction
                const year = teamCard.querySelector("p").textContent.match(/Year:\s*(\d+)/)[1]; // Adjusted for correct extraction
                const semester = teamCard.querySelector("p").textContent.match(/Semester:\s*(\d+)/)[1]; // Adjusted for correct extraction
                const teamMembers = teamCard.querySelector("p").textContent.match(/Team Members:\s*(.*)/)[1]; // Adjusted for correct extraction

                // Prepare the data to send
                const postData = `team_id=${encodeURIComponent(teamId)}&` +
                                `team_name=${encodeURIComponent(teamName)}&` +
                                `department=${encodeURIComponent(department)}&` +
                                `year=${encodeURIComponent(year)}&` +
                                `semester=${encodeURIComponent(semester)}&` +
                                `team_members=${encodeURIComponent(teamMembers)}&` +
                                `action=${encodeURIComponent(action)}`;

                fetch('update_approval.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: postData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Status updated successfully!');
                    } else {
                        alert('Error updating status: ' + data.message);
                    }
                })
            .catch(error => console.error('Error:', error));
        }



    </script>
</body>

</html>
