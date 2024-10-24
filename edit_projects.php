<?php
session_start();


// Retrieve user data from session
$roll_number = $_SESSION['roll_number'] ?? 'N/A'; // Default to 'N/A' if not set

// Safely retrieve dashboard data
$dashboard_data = $_SESSION['dashboard_data'] ?? null;

// Retrieve user profile image if exists
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image

// Handle profile picture upload logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $user_id = $_SESSION['user_id']; // Assuming you have the user's ID in the session

    // Directory where the profile images will be saved
    $target_dir = "uploads/profile_pics/";
    $imageFileType = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . "." . $imageFileType; // Create a unique filename
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
                    $sql = "UPDATE students SET profile_image = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $target_file, $user_id);

                    if ($stmt->execute()) {
                        $_SESSION['profile_image'] = $target_file; // Update session with new image path
                        header("Location: stud_dashboard.php"); // Redirect to dashboard after upload
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
<?php 
include 'db_connection.php';  // Include your database connection

$messages = [];
$project_to_edit = null;

// Check if an ID is passed to edit a project
if (isset($_GET['id'])) {
    $project_id = $_GET['id'];

    // Fetch the project details for editing
    $sql = "SELECT * FROM student_projects WHERE id = $project_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $project_to_edit = $result->fetch_assoc(); // Store project details in $project_to_edit
    } else {
        $messages[] = "Project not found.";
    }
}

// Handle the update request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_project'])) {
    var_dump($_POST);
    $project_title = $conn->real_escape_string($_POST['project_title']);
    $project_description = $conn->real_escape_string($_POST['project_description']);
    $team_name = $conn->real_escape_string($_POST['team_name']);
    $team_members = $conn->real_escape_string($_POST['team_members']);
    $technology_stack = $conn->real_escape_string($_POST['technology_stack']);

    // Update the project in the database
    $update_sql = "UPDATE student_projects 
               SET project_title = '$project_title', 
                   project_description = '$project_description', 
                   team_name = '$team_name', 
                   team_members = '$team_members', 
                   technology_stack = '$technology_stack' 
               WHERE id = $project_id";


    if ($conn->query($update_sql) === TRUE) {
        $messages[] = "Project updated successfully!";
        header("Location: edit_projects.php"); // Redirect to the projects page after update
        exit();
    } else {
        $messages[] = "Error updating project: " . $conn->error;
    }
}

// Fetch all projects to display in the table
$projects_sql = "SELECT * FROM student_projects";
$projects_result = $conn->query($projects_sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/mentors.css">
    <script src="https://kit.fontawesome.com/0f4e2bc10d.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: whitesmoke; /* Light blue */
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 100%;
            margin-left: 210px;
            background-color: #FFFFFF; 
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Header Styles */
        h1, h2, h3 {
            color: #594f8d; 
        }
        
        .header > h1 {
            text-transform: uppercase;
            font-size: 30px;
        }

        .main-content {
            margin-top: 100px;   
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
            margin: 10px;
            color: #FFFFFF; 
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        /* Created Projects Section */
        #created-projects {
            display: flex;
            justify-content: space-evenly; 
            padding: 20px;
        }

        .project-box {
            background-color: white;
            width: 400px; /* Two cards in a row */
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            margin: 15px;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
        }

        .project-box h3 {
            font-size: 18px;
            margin-bottom: 5px; /* Reduce space below heading */
        }

        .project-box p {
            margin: 2px 0; /* Tighten space between paragraphs */
            line-height: 1.2; /* Reduce line spacing */
        }

        /* Styling Error Messages */
        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
        }   

        .project-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px; /* Space above buttons */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th {
    background-color: #f2f2f2; /* Light background for headers */
}
        th, td {
            padding: 10px;
            text-align: left;
        }
        h2{
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .form-container input[type="text"], .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-container button {
            background-color: #594f8d;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .profile-roll {
    margin-top: 20px; /* Add some space between profile picture and roll number */
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
                    <a href="list_subission.php"><i class="fas fa-list"></i> List Submission</a>
                </div>
            </li>
            <li><a href="create_teams.php"><i class="fas fa-address-book"></i>Teams</a></li>
            <li><a href="stud_editor.php"><i class="fas fa-address-book"></i>Editor</a></li>
        </ul>
    </div>
    <div class="main_header">
        <div class="header">
            <h1>Edit Project</h1>
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
    <h1>Edit Projects</h1>

    <!-- Display messages -->
    <?php if (!empty($messages)): ?>
        <div class="messages">
            <?php foreach ($messages as $message): ?>
                <p><?php echo $message; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Display all projects in a table -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Project Title</th>
                <th>Project Description</th>
                <th>Team Name</th>
                <th>Team Members</th>
                <th>Technology Stack</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($projects_result->num_rows > 0): ?>
                <?php while($project = $projects_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $project['id']; ?></td>
                        <td><?php echo $project['project_title']; ?></td>
                        <td><?php echo $project['project_description']; ?></td>
                        <td><?php echo $project['team_name']; ?></td>
                        <td><?php echo $project['team_members']; ?></td>
                        <td><?php echo $project['technology_stack']; ?></td>
                        <td>
                            <a href="edit_projects.php?id=<?php echo $project['id']; ?>">Edit</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No projects found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Edit form (only visible when editing a project) -->
    <?php if ($project_to_edit): ?>
        <h2>Edit Project ID: <?php echo $project_to_edit['id']; ?></h2>
        <form action="edit_projects.php?id=<?php echo $project_to_edit['id']; ?>" method="POST">
            <div class="form-container">
                <label for="project_title">Project Title:</label>
                <input type="text" name="project_title" id="project_title" value="<?php echo $project_to_edit['project_title']; ?>" required />

                <label for="project_description">Project Description:</label>
                <input type="text" name="project_description" id="project_description" value="<?php echo $project_to_edit['project_description']; ?>" required />

                <label for="team_name">Team Name:</label>
                <input type="text" name="team_name" id="team_name" value="<?php echo $project_to_edit['team_name']; ?>" required />

                <label for="team_members">Team Members:</label>
                <textarea name="team_members" id="team_members" rows="4" required><?php echo $project_to_edit['team_members']; ?></textarea>

                <label for="technology_stack">Technology Stack:</label>
                <input type="text" name="technology_stack" id="technology_stack" value="<?php echo $project_to_edit['technology_stack']; ?>" required />

                <button type="submit" name="update_project">Update Project</button>
            </div>
        </form>
    <?php endif; ?>
</div>
    </section>
    <script>
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
