<?php
include 'db_connection.php';  
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
    <title>PMS</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://kit.fontawesome.com/0f4e2bc10d.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap">
    
    <style>
                body {
        font-family: Arial, sans-serif;
        background-color: whitesmoke; /* Light blue */
        margin: 0;
        padding: 0;
        }

        .container {

        max-width: 100%;
        margin-left:210px;
        background-color: #FFFFFF; 
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Header Styles */
        h1, h2, h3 {
        color: #594f8d; 
        }
        .header>h1{
            text-transform: uppercase;
            font-size: 30px;
        }

        .form-container {
            background-color: #f7f7f7; /* Light gray background */
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); /* Slight shadow */
            margin-bottom: 20px;
        }

        /* Form row styles */
        .form-row {
        display: flex; 
        justify-content: space-between; 
        margin-bottom: 1rem; 
        }

        .form-group {
        flex: 1; 
        margin-right: 10px; 
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
            color: #594f8d; /* Label color matching the theme */
        }

        .form-group:last-child {
        margin-right: 0; 
        }

        .hidden {
        display: none;
        }


        /* Label styles */
        label {
        margin-bottom: 5px;
        display: block;
        }
        h2{
            padding: 10px;
        }
        #form-container input[type="text"],
        #form-container input[type="number"],
        #form-container select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            margin-top: 5px;
            font-size: 14px;
            background-color: #fff; /* White background for form inputs */
        }
        /* Input and select styles */
        input[type="text"],
        input[type="email"],
        input[type="tel"],
        input[type="number"],
        select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
        }

        /* Button Styles */
        button {
        background-color: #594f8d;
        margin:10px;
        color: #FFFFFF; 
        border: none;
        padding: 10px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        }

        button.edit-btn {
        background-color: orange;
        }

        button.view-btn {
        background-color: green;
        }

        button.delete-btn {
        background-color: red;
        }

        /* Created Teams Section */
        #created-teams {
        display: flex;
        justify-content: space-evenly; 
       
        padding: 20px;

        }

        .team-box {
        background-color: white;
        width: 400px; /* Two cards in a row */
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        margin:15px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
        }

        .team-box h3 {
        font-size: 18px;
        margin-bottom: 5px; /* Reduce space below heading */
        }

        .team-box h4 {
        font-size: 18px;
        margin-bottom: 5px; /* Reduce space below heading */
        }

        .team-box p {
        margin: 2px 0; /* Tighten space between paragraphs */
        line-height: 1.2; /* Reduce line spacing */
        }

        .team-box ul {
        list-style-type: none;
        padding: 0;
        margin: 5px 0; /* Reduce space before and after the list */
        }

        .team-box ul li {
        margin: 3px 0; /* Tighten space between list items */
        }
        .team-members>li{
            text-transform: capitalize;
        }
        #create-team-btn {
            background-color: #5cb85c;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        #create-team-btn:hover {
            background-color: #4cae4c;
        }

        /* Styling Error Messages */
        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }   
        /* Modal styles */
        .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); 
        display: flex;
        justify-content: center;
        align-items: center;
        }
        
        .main-content{
            margin-top:100px;
        }
    
        .modal-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
        width: 80%;
        max-width: 600px;
        text-align: left;
        position: relative; 
        }

        .close-modal {
        position: absolute;
        top: 8px; 
        right: 8px; 
        font-size: 25px;
        cursor: pointer;
        color: #333;
        z-index: 1000; 
        }

        .close-modal:hover {
        color: red; 
        }
        .team-box>h3{
            font-size: 16px;
            text-transform: uppercase;
        }

        /* Hidden modal by default */
        .modal.hidden {
        display: none;
        }
        @media screen and (max-width: 1200px) {
    #created-teams {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .team-box {
        width: 48%;
    }
}

@media screen and (max-width: 992px) {
    #created-teams {
        justify-content: center;
    }

    .team-box {
        width: 100%;
        margin: 10px 0;
    }
}

@media screen and (max-width: 768px) {
    #created-teams {
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .team-box {
        width: 90%;
        margin: 10px 0;
    }
}

@media screen and (max-width: 576px) {
    .container {
        margin-left: 0;
        padding: 10px;
    }

    .team-box {
        width: 100%;
        margin: 10px 0;
    }

    .form-row {
        flex-direction: column;
        margin-bottom: 1rem;
    }

    .form-group {
        margin-right: 0;
        margin-bottom: 10px;
    }
}


    </style>
</head>
<body>

<div class="wrapper">
    <div class="sidebar">
        <img src="assets/img/girlprofile.png" alt="" width="100px "/>
        <h2 class="profile-name">Vaishali</h2>
        <h2 class="profile-roll">21CS053</h2>
        <ul>
            <li><a href="stud_dash.php"><i class="fas fa-home"></i>Home</a></li>
            <li><a href="stud_profiles.php"><i class="fas fa-user"></i>Profile</a></li>
            <li><a href="stud_projects.php"><i class="fas fa-address-card"></i>Projects</a></li>
            <li><a href="stud_mentors.php"><i class="fas fa-project-diagram"></i>Mentors</a></li>
            <li><a href="stud_submission.php"><i class="fas fa-blog"></i>Submission</a></li>
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

<script src="teams.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>




</body>
</html>
