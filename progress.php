<?php
session_start();
include 'db_connection.php'; 
include 'includes/profile_pic.php';
$roll_number = $_SESSION['roll_number'] ?? 'N/A'; 
$dashboard_data = $_SESSION['dashboard_data'] ?? null;
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image

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
        
        $stmt = $conn->prepare("SELECT id FROM staff WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $staffResult = $stmt->get_result();

        if ($staffResult->num_rows == 0) {
            echo "<script>alert('Invalid email address.');</script>";
        } else {
            $staffRow = $staffResult->fetch_assoc();
            $staff_id = $staffRow['id'];

            $checkProject = $conn->prepare("SELECT id FROM projects WHERE id = ?");
            $checkProject->bind_param("i", $project_id);
            $checkProject->execute();
            $projectResult = $checkProject->get_result();

            if ($projectResult->num_rows == 0) {
                die('Invalid project ID.');
            }

            $insertQuery = $conn->prepare("INSERT INTO remarks (project_id, staff_email, remark) VALUES (?, ?, ?)");
            $insertQuery->bind_param("iss", $project_id, $email, $remark);

            if ($insertQuery->execute()) {
                echo "<script>alert('Remark added successfully');</script>";
            } else {
                echo "<script>alert('Error: " . $insertQuery->error . "');</script>";
            }

            $insertQuery->close();
            $checkProject->close();
        }
        $stmt->close();
    }
}

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
    <link rel="stylesheet" href="../../assets/vendor/aos/dist/aos.css">
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
        padding-left: 20px; 
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
        text-align: right; 
    }

    .team-member {
        display: flex;
        align-items: center;
        margin-bottom: 10px; 
    }

    .team-member img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
    }

    .remarks-section {
    margin-top: 20px; 
    padding: 15px; 
    background-color: #f1f1f1; 
    border: 1px solid #ddd; 
    border-radius: 5px; 
}

.remarks-form {
    margin-top: 10px; 
    display: flex; 
    flex-direction: column; 
    background-color: #fff; 
    padding: 15px; 
    border: 1px solid #ddd; 
    border-radius: 5px; 
}

.remarks-form input[type="text"],
.remarks-form textarea {
    width: 100%; 
    margin: 5px 0; 
    padding: 10px; 
    border: 1px solid #ccc; 
    border-radius: 5px; 
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
}
    .team-members-container {
        display: flex;
        flex-wrap: wrap;
        margin-top: 10px;
        width: calc(100% - 30px); 
    }

    .team-member-icon {
        display: flex;
        align-items: center;
        margin-right: 15px;
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

    .project-details h2 {
        font-size: 1.5em; 
        margin-bottom: 10px;
    }

    .project-details p {
        font-size: 1.1em; 
        margin: 5px 0; 
    }
    </style>
</head>
<body>
<div class="wrapper js-scroll-nav navbar navbar-light bg-light">
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
                        <p><strong>Uploads:</strong> 
    <?php 
    if (!empty($row['ppt_path'])): 
        $file_path = htmlspecialchars($row['ppt_path']);
        $file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        
        // Set MIME type based on the file extension
        $mime_types = [
            'pdf' => 'application/pdf',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm'
        ];

        $mime_type = $mime_types[$file_extension] ?? 'application/octet-stream'; // Default if unknown

        // Generate the HTML for viewable file
        echo "<a href=\"$file_path\" target=\"_blank\">View Project File</a>";
    else: 
        echo "No file uploaded.";
    endif;
    ?>
</p>

                    </div>
                    <div class="right-top">
                        <p><strong>Abstract:</strong> <?php echo nl2br(htmlspecialchars($row['abstract'])); ?></p>

                    </div>
                </div>

                <div class="bottom-section">
                    <div class="left-bottom">
                        <div class="team-members-container">
                            <?php
                            $members = explode(',', $row['members']); 
                            $team_leader = isset($row['leader']) ? $row['leader'] : 'Unknown Leader'; 
                            
                            foreach ($members as $member) {
                                echo '<div class="team-member-icon">';
                                echo '<img src="' . htmlspecialchars($profile_image) . '" alt="Member Image">'; 
                                echo '<span>' . htmlspecialchars(trim($member)) . '</span>';
                                echo '</div>';
                            }
                            
                            echo '<div class="team-member-icon">';
                            echo '<img src="' . htmlspecialchars($profile_image) . '" alt="Leader Image">'; 
                            echo '<span>' . htmlspecialchars(trim($team_leader)) . ' (Leader)</span>'; 
                            echo '</div>';
                            ?>
                        </div>
                    </div>
                </div>
            </div>
<!-- Remarks Section -->
<div class="remarks-section">
    <button class="remarks-button" onclick="toggleRemarks(<?php echo $row['id']; ?>)">Add/View Message</button>
    
    <div id="remarks-form-<?php echo $row['id']; ?>" class="remarks-form" style="display:none;">
        <form method="POST" action="">
            <input type="hidden" name="project_id" value="<?php echo $row['id']; ?>">
            <input type="text" name="email" placeholder="Enter staff email" required>
            <textarea name="remark" class="textarea-style" placeholder="Add your remark" required></textarea>
            <button type="submit" class="remarks-button">Submit Remark</button>
        </form>
    </div>

    <div id="remarks-<?php echo $row['id']; ?>" class="remarks-history" style="display:none;">
        <?php
        // Fetch and display remarks from the database
        $project_id = $row['id'];
        $remarkQuery = $conn->prepare("SELECT staff_email, remark FROM remarks WHERE project_id = ?");
        $remarkQuery->bind_param("i", $project_id);
        $remarkQuery->execute();
        $remarksResult = $remarkQuery->get_result();

        if ($remarksResult->num_rows > 0) {
            while ($remarkRow = $remarksResult->fetch_assoc()) {
                echo "<p><strong>" . htmlspecialchars($remarkRow['staff_email']) . ":</strong> " . htmlspecialchars($remarkRow['remark']) . "</p>";
            }
        } else {
            echo "<p>No remarks yet.</p>";
        }
        $remarkQuery->close();
        ?>
    </div>
</div>

<?php endwhile; ?>
<?php else: ?>
    <p>No projects found.</p>
<?php endif; ?>

</div> 

<script>
    
function toggleRemarks(projectId) {
    const remarksForm = document.getElementById(`remarks-form-${projectId}`);
    const remarksHistory = document.getElementById(`remarks-${projectId}`);
    
    // Toggle visibility of the remarks form and history
    if (remarksForm.style.display === "none") {
        remarksForm.style.display = "block";
        remarksHistory.style.display = "none"; // Hide remarks history when form is visible
    } else {
        remarksForm.style.display = "none";
        remarksHistory.style.display = "block"; // Show remarks history when form is hidden
    }
}



function toggleRemarks(projectId) {
    const remarksForm = document.getElementById(`remarks-form-${projectId}`);
    const remarksHistory = document.getElementById(`remarks-${projectId}`);

    if (remarksForm.style.display === "none") {
        remarksForm.style.display = "block";
        remarksHistory.style.display = "block";
    } else {
        remarksForm.style.display = "none";
        remarksHistory.style.display = "none";
    }
}

// Add this script for the buttons
document.querySelectorAll('.view-remarks-button').forEach(button => {
    button.addEventListener('click', function() {
        const remarksHistory = this.closest('.project-details').querySelector('.remarks-history');
        remarksHistory.style.display = remarksHistory.style.display === 'block' ? 'none' : 'block';
    });
});
</script>


</body>
</html>