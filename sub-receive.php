<?php
session_start();
include 'db_connection.php';
include 'includes/profile_pic.php';

$username = $_SESSION['username'] ?? 'Vaishali'; 
$role = $_SESSION['role'] ?? 'N/A';
$profile_image = $_SESSION['profile_image'] ?? 'https://example.com/default.jpg';

$sql = "SELECT title, team_name, status, leader AS team_leader, members, created_at, abstract, ppt_path, mentor_id FROM projects";
$result = $conn->query($sql);
$result = $conn->query("SELECT * FROM projects");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? null;
    $project_id = $_POST['project_id'] ?? null;
    $remark = $_POST['remark'] ?? '';

    if (!$email || !$project_id) {
        echo "<script>alert('Email and Project ID are required.');</script>";
    } else {
        // Prepare statement to get staff_id from email
        $stmt = $conn->prepare("SELECT id FROM staff WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $staffResult = $stmt->get_result();

        if ($staffResult->num_rows == 0) {
            echo "<script>alert('Invalid email address.');</script>";
        }
    }

    $staffRow = $staffResult->fetch_assoc();
    $staff_id = $staffRow['id'];

    // Check if the project ID exists in the projects table
    $checkProject = $conn->prepare("SELECT id FROM projects WHERE id = ?");
    $checkProject->bind_param("i", $project_id);
    $checkProject->execute();
    $projectResult = $checkProject->get_result();

    if ($projectResult->num_rows == 0) {
        die('Invalid project ID.');
    }

    // Insert into remarks table using staff_id
    $insertQuery = $conn->prepare("INSERT INTO remarks (project_id, staff_email, content) VALUES (?, ?, ?)");
    $insertQuery->bind_param("iss", $project_id, $email, $remark);

    if ($insertQuery->execute()) {
        echo "Remark added successfully";
    } else {
        echo "Error: " . $insertQuery->error;
    }

    $insertQuery->close();
    $stmt->close();
    $checkProject->close();
}

// Do not close the connection yet, as we still need it to fetch remarks
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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap">
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
        width: calc(100% - 30px); /* Same width as project-details */
        margin-left: auto; /* Centering */
        margin-right: auto; /* Centering */
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
            <li><a href="receive.php"><i class="fas fa-blog"></i>Submission</a></li>
            <li><a href="viewteams.php"><i class="fas fa-address-book"></i>Teams</a></li>
            <li><a href="cal.php"><i class="fas fa-calendar-alt"></i>Schedule</a></li>
        </ul>
    </div>

    <div class="main_header">
        <div class="header">
            <h1>PROJECT PROGRESS</h1>
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
            
            // Counter to track the display of the leader vs. members
            $isLeaderDisplayed = false;
            
            // Loop through the members and display them
            foreach ($members as $member) {
                echo '<div class="team-member-icon">';
                echo '<img src="' . htmlspecialchars($profile_image) . '" alt="Member Image">'; // Placeholder for member's image
                echo '<span>' . htmlspecialchars(trim($member)) . '</span>';
                echo '</div>';
            }
            
            // Display the team leader
            echo '<div class="team-member-icon">';
            echo '<img src="' . htmlspecialchars($profile_image) . '" alt="Leader Image">'; // Placeholder for leader's image
            echo '<span>' . htmlspecialchars(trim($team_leader)) . ' (Leader)</span>'; // Display leader with indication
            echo '</div>';
            ?>
        </div>
    </div>
                <div class="bottom-section">
                    <div class="left-bottom">
                        <form method="POST" action="">
                            <input type="hidden" name="project_id" value="<?php echo $row['id']; ?>">
                            <input type="text" name="email" placeholder="Enter staff email" required>
                            <textarea name="remark" class="textarea-style" placeholder="Add your remark" required></textarea>
                            <button type="submit" class="remarks-button">Add Remark</button>
                        </form>
                    </div>
                    <div class="right-bottom">
                        <button class="remarks-button" onclick="toggleRemarks(<?php echo $row['id']; ?>)">View Remarks</button>
                        <div id="remarks-<?php echo $row['id']; ?>" class="remarks-history" style="display:none;">
                            <?php
                            // Fetch remarks related to the current project
                            $remarksQuery = $conn->prepare("SELECT content FROM remarks WHERE project_id = ?");
                            $remarksQuery->bind_param("i", $row['id']);
                            $remarksQuery->execute();
                            $remarksResult = $remarksQuery->get_result();
                            
                            if ($remarksResult->num_rows > 0) {
                                while ($remarkRow = $remarksResult->fetch_assoc()) {
                                    echo "<p>" . htmlspecialchars($remarkRow['content']) . "</p>";
                                }
                            } else {
                                echo "<p>No remarks yet.</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No projects found.</p>
    <?php endif; ?>
</div>

<script>
    function toggleRemarks(id) {
        const remarksDiv = document.getElementById('remarks-' + id);
        remarksDiv.style.display = (remarksDiv.style.display === "none" || remarksDiv.style.display === "") ? "block" : "none";
    }
</script>

</body>
</html>

<?php
// Close the database connection after all operations are completed
$conn->close();
?>