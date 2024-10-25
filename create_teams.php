<?php
session_start();
include 'db_connection.php';  
include 'includes/profile_pic.php';
$roll_number = $_SESSION['roll_number'] ?? 'N/A'; // Default to 'N/A' if not set
$dashboard_data = $_SESSION['dashboard_data'] ?? null;
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; 

//inlcude 'db.php'; // Include database connection

$messages = [];

// Handle POST request to create a new team
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST;  // Get POST data directly from the form

    // Validate input fields
    if (empty($data['team_name']) || empty($data['team_size']) || empty($data['year']) || empty($data['department'])) {
        $messages[] = "All team fields are required.";
    } else {
        $team_name = $conn->real_escape_string($data['team_name']);
        $team_size = (int)$data['team_size'];
        $year = (int)$data['year'];
        $department = $conn->real_escape_string($data['department']);

        // Insert team into the 'teams' table
        $sql = "INSERT INTO teams (team_name, team_size, year, department) VALUES ('$team_name', $team_size, $year, '$department')";

        if ($conn->query($sql) === TRUE) {
            $team_id = $conn->insert_id;  // Get the last inserted ID

            // Insert each team member into the 'team_members' table
            for ($i = 0; $i < count($data['member_name']); $i++) {
                $member_name = $conn->real_escape_string($data['member_name'][$i]);
                $roll_no = $conn->real_escape_string($data['roll_no'][$i]);
                $member_role = $conn->real_escape_string($data['member_role'][$i]);
                $member_email = $conn->real_escape_string($data['member_email'][$i]);
                $member_phone = $conn->real_escape_string($data['member_phone'][$i]);

                // Insert member query
                $sql_member = "INSERT INTO team_members (team_id, member_name, roll_no, member_role, member_email, member_phone)
                               VALUES ($team_id, '$member_name', '$roll_no', '$member_role', '$member_email', '$member_phone')";

                if ($conn->query($sql_member) === FALSE) {
                    $messages[] = "Error inserting member: " . $conn->error;
                }
            }

            // Success message
            $messages[] = "Team '$team_name' created successfully!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $messages[] = "Failed to create team: " . $conn->error;
        }
    }
}

// Fetch all teams and members
$sql = "SELECT * FROM teams";
$result = $conn->query($sql);
$teams = [];

while ($row = $result->fetch_assoc()) {
    $team_id = $row['id'];
    $row['members'] = [];

    // Fetch members for the team
    $member_sql = "SELECT * FROM team_members WHERE team_id = $team_id";
    $member_result = $conn->query($member_sql);
    while ($member_row = $member_result->fetch_assoc()) {
        unset($member_row['id']);
        unset($member_row['team_id']);
        $row['members'][] = $member_row;
    }

    // Add team data to the teams array
    $teams[] = array(
        "id" => $row['id'],
        "team_name" => $row['team_name'],
        "team_size" => $row['team_size'],
        "year" => $row['year'],
        "department" => $row['department'],
        "members" => $row['members']
    );
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PMS Student Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="mentors.css">
    <link rel="stylesheet" href="assets/css/teams.css">
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
            <h1>Team Creation</h1>
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
<div class="container">
        <button id="create-team-btn" class="btn primary">Create Team</button>

        <!-- Hidden form container -->
        <div id="form-container" class="hidden form-container">
            <form id="create-team-form" method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="team-name">Team Name</label>
                        <input type="text" id="team-name" name="team_name" required>
                    </div>
                    <div class="form-group">
                        <label for="team-size">Team Size</label>
                        <input type="number" id="team-size" name="team_size" required>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="year">Year</label>
                        <select id="year" name="year" required>
                            <option value="0" disabled selected>Select</option>
                            <option value="1">1st</option>
                            <option value="2">2nd</option>
                            <option value="3">3rd</option>
                            <option value="4">4th</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="department">Department</label>
                        <select id="department" name="department" required>
                            <option value="0" disabled selected>Select</option>
                            <option value="CSE">CSE</option>
                            <option value="ECE">ECE</option>
                            <option value="EEE">EEE</option>
                            <option value="MECH">MECH</option>
                            <option value="IT">IT</option>
                            <option value="AIDS">AIDS</option>
                        </select>
                    </div>
                </div>
                
                <h3>Team Members</h3>
                <div id="team-members-section">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="member-name-0">Member Name</label>
                            <input type="text" id="member-name-0" name="member_name[]" required>
                        </div>
                        <div class="form-group">
                            <label for="roll-no-0">Roll No</label>
                            <input type="text" id="roll-no-0" name="roll_no[]" required>
                        </div>
                        <div class="form-group">
                            <label for="member-role-0">Role</label>
                            <select id="member-role-0" name="member_role[]" required>
                                <option value="0" disabled selected>Select</option>
                                <option value="Leader">Leader</option>
                                <option value="Member">Member</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="member-email-0">Email</label>
                            <input type="email" id="member-email-0" name="member_email[]" required>
                        </div>
                        <div class="form-group">
                            <label for="member-phone-0">Phone No</label>
                            <input type="tel" id="member-phone-0" name="member_phone[]" required>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-member-btn" class="btn secondary">Add Member</button>
                <button type="submit">Save Team</button>
            </form>
        </div>
        <h2>Created Teams</h2>
        <div id="created-teams">  
            <?php foreach ($teams as $team): ?>  
                <div class="team-box">  
                    <h3><?php echo htmlspecialchars($team['team_name']); ?> (ID: <?php echo htmlspecialchars($team['id']); ?>)</h3>  
                    <p>Department: <?php echo htmlspecialchars($team['department']); ?>, Year: <?php echo htmlspecialchars($team['year']); ?>, Size: <?php echo htmlspecialchars($team['team_size']); ?></p>  
                    <h4>Members:</h4>  
                    <ul class="team-members">  
                        <?php foreach ($team['members'] as $member): ?>  
                            <li><?php echo htmlspecialchars($member['member_name']); ?> (<?php echo htmlspecialchars($member['member_role']); ?>)</li>  
                        <?php endforeach; ?>  
                    </ul>  
                    <div class="team-actions">  
                        <button class="edit-btn" onclick="editTeam(<?php echo $team['id']; ?>)">Edit</button>  
                        <button class="view-btn" onclick="viewTeam(<?php echo $team['id']; ?>)">View</button>  
                        <button class="delete-btn" onclick="deleteTeam(<?php echo $team['id']; ?>)">Delete</button>  
                    </div>  
                </div>  
            <?php endforeach; ?>  
        </div>
    </div>

    
</section>
<!-- Display success messages -->
<?php if (!empty($messages)): ?>
            <div class="messages">
                <?php foreach ($messages as $message): ?>
                    <p><?php echo $message; ?></p>
                <?php endforeach; ?>
            </div>
<?php endif; ?>
<section class="project-section">
    

</section>
<!-- Modal for viewing team details -->  
<div id="team-modal" class="modal hidden">  
    <div class="modal-content">  
        <span class="close-modal" onclick="closeModal()">&times;</span>  
        <h2>Team Details</h2>  
        <div id="team-details"></div>  
    </div>  
</div>  

<script src="assets/js/teams.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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



</body>
</html>
