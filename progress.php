<?php
session_start();
include 'db_connection.php';
include 'includes/profile_pic.php';
$roll_number = $_SESSION['roll_number'] ?? 'N/A'; 
$dashboard_data = $_SESSION['dashboard_data'] ?? null;

$student_team = $_SESSION['team'];
$stmt = $conn->prepare("SELECT r.remark, s.email AS staff_email FROM remarks r JOIN staff s ON r.staff_id = s.id JOIN projects p ON r.project_id = p.id WHERE p.team = ?");
$stmt->bind_param("s", $student_team);
$stmt->execute();
$remarksResult = $stmt->get_result();

if ($remarksResult->num_rows > 0) {
    echo "<h3>Remarks from Staff:</h3>";
    while ($row = $remarksResult->fetch_assoc()) {
        echo "<div>";
        echo "<p><b>Staff:</b> " . htmlspecialchars($row['staff_email']) . "</p>";
        echo "<p><b>Remark:</b> " . nl2br(htmlspecialchars($row['remark'])) . "</p>";
        echo "<form action='stud_reply.php' method='POST'>
                <input type='hidden' name='staff_email' value='" . htmlspecialchars($row['staff_email']) . "'>
                <textarea name='student_message' placeholder='Reply to this remark' required></textarea>
                <input type='submit' value='Send'>
              </form>";
        echo "</div><br>";
    }
} else {
    echo "<p>No remarks found for your team.</p>";
}

// Fetch messages
$query = "SELECT m.message, s.email AS staff_email FROM messages m JOIN staff s ON m.staff_id = s.id WHERE m.team_name = '$student_team' ORDER BY m.id ASC";
$messageResult = $conn->query($query);

if ($messageResult->num_rows > 0) {
    echo "<h3>Conversation History:</h3>";
    while ($row = $messageResult->fetch_assoc()) {
        echo "<div>";
        echo "<p><b>Staff:</b> " . htmlspecialchars($row['staff_email']) . "</p>";
        echo "<p><b>Message:</b> " . nl2br(htmlspecialchars($row['message'])) . "</p>";
        echo "</div><br>";
    }
} else {
    echo "<p>No conversation history found for your team.</p>";
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
    <script src="https://kit.fontawesome.com/0f4e2bc10d.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <style>
        .flex-container {
            margin-top: 7%;
            margin-left: 15%;
            margin-right: 30px;
            display: flex;
            flex-direction: column;
        }

        .project-details {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .top-section {
            display: flex;
            justify-content: space-between;
        }

        .left-top {
            width: 50%;
        }

        .right-top {
            width: 50%;
            padding-left: 20px; /* Add some space between columns */
        }

        .bottom-section {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .left-bottom {
            width: 50%;
        }

        .right-bottom {
            width: 50%;
            text-align: right; /* Align remarks button to the right */
        }

        .team-member {
            display: flex;
            align-items: center;
            margin-bottom: 10px; /* Space between team members */
        }

        .team-member img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .remarks-button {
            padding: 10px 15px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .remarks-button:hover {
            background-color: #2980b9;
        }

        .remarks-history {
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
            display: none; /* Initially hidden */
        }

        .remarks-input {
            display: none; /* Initially hidden */
            margin-top: 10px;
        }

        .textarea-style {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }


        .team-members-container {
            display: flex;
            flex-wrap: wrap;
            margin-top: 10px;
            width: calc(100% - 30px); /* Same width as project-details */
        }

        .team-member-icon {
            display: flex;
            align-items: center;
            margin-right: 15px; /* Space between icons */
        }

        .team-member-icon img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .leader {
            font-weight: bold;
            color: #2c3e50;
        }

        /* New styles for font size and spacing */
        .project-details h2 {
            font-size: 1.5em; /* Increase the size of the title */
            margin-bottom: 10px; /* Space below the title */
        }

        .project-details p {
            font-size: 1.1em; /* Slightly larger font for paragraphs */
            margin: 5px 0; /* Space above and below each paragraph */
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
            <h1>PROGRESS</h1>
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

<div class="flex-container">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="project-details">
                <div class="top-section">
                    <div class="left-top">
                        <h2><?php echo htmlspecialchars($row['title']); ?></h2>
                        <p><strong>Team Name:</strong> <?php echo htmlspecialchars($row['team_name']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                        <p><strong>Shared Date:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
                    </div>
                    <div class="right-top">
                        <p><strong>Abstract:</strong> <?php echo nl2br(htmlspecialchars($row['abstract'])); ?></p>
                    </div>
                </div>

                <div class="bottom-section">
                    <div class="left-bottom">
                        <div class="team-members-container">
                            <?php
                            // Assuming you have a function to get team members by team name
                            $members = explode(',', $row['members']); // Assuming members are stored as a comma-separated string
                            $team_leader = $row['team_leader']; // Assuming you have the team leader's name from the database
                            
                            // Counter to track the display of the leader
                            $isLeaderDisplayed = false;

                            foreach ($members as $member):
                                // Fetch user image from your database if necessary
                                $memberImage = 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image for members
                                ?>
                                <div class="team-member">
                                    <div class="team-member-icon">
                                        <img src="<?php echo htmlspecialchars($memberImage); ?>" alt="Team Member">
                                        <span><?php echo htmlspecialchars(trim($member)); ?></span>
                                    </div>
                                </div>
                                <?php if (trim($member) === trim($team_leader)) $isLeaderDisplayed = true; ?>
                            <?php endforeach; ?>
                            <?php if (!$isLeaderDisplayed): ?>
                                <div class="team-member">
                                    <span class="leader"><?php echo htmlspecialchars($team_leader); ?></span> (Leader)
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button class="remarks-button" onclick="toggleRemarkForm('<?php echo $row['mentor_id']; ?>')">Share Remarks</button>

                    <form id="remark-form-<?php echo $row['mentor_id']; ?>" method="POST" class="remarks-input">
                        <input type="hidden" name="project_id" value="<?php echo $row['mentor_id']; ?>">
                        <textarea name="remark" class="textarea-style" rows="3" placeholder="Enter your remark here..." required></textarea><br>
                        <input type="submit" name="share_remark" value="Submit" class="remarks-button">
                    </form>

                    <div class="remarks-history" id="remarks-history-<?php echo $row['mentor_id']; ?>">
                        <h4>Remarks History:</h4>
                        <?php
                        $stmt->bind_param("i", $row['mentor_id']);
                        $stmt->execute();
                        $historyResult = $stmt->get_result();
                        while ($historyRow = $historyResult->fetch_assoc()): ?>
                            <p><strong><?php echo htmlspecialchars($historyRow['staff_email']); ?>:</strong> <?php echo nl2br(htmlspecialchars($historyRow['remark'])); ?></p>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No projects found for your team.</p>
    <?php endif; ?>
</div>

<script>
    function toggleRemarkForm(mentorId) {
        const form = document.getElementById('remark-form-' + mentorId);
        const history = document.getElementById('remarks-history-' + mentorId);
        if (form.style.display === "none" || form.style.display === "") {
            form.style.display = "block";
            history.style.display = "block";
        } else {
            form.style.display = "none";
            history.style.display = "none";
        }
    }
</script>
</body>
</html>