<?php
session_start();
include 'db_connection.php'; 
include 'includes/profile_pic.php';

$roll_number = $_SESSION['roll_number'] ?? 'N/A'; 
$dashboard_data = $_SESSION['dashboard_data'] ?? null;

$messages = [];

// Handle POST request to create a new project
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = $_POST;  // Get POST data directly from the form

    // Validate input fields
    if (empty($data['project_title']) || empty($data['project_description'])) {
        $messages[] = "Project title and description are required.";
    } else {
        $student_id = $_SESSION['user_id']; // Use the logged-in student's ID
        $project_title = $conn->real_escape_string($data['project_title']);
        $project_description = $conn->real_escape_string($data['project_description']);
        $submission_date = date('Y-m-d'); // Set to today's date or use an input field
        $status = 'Pending'; // Default status
        $mentor_comments = ''; // Initial value
        $files = ''; // Initial value; handle file uploads if necessary

        // Insert project into the 'student_projects' table
        $sql = "INSERT INTO student_projects (student_id, project_title, project_description, submission_date, status, mentor_comments, files) 
                VALUES ('$student_id', '$project_title', '$project_description', '$submission_date', '$status', '$mentor_comments', '$files')";

        if ($conn->query($sql) === TRUE) {
            $messages[] = "Project '$project_title' created successfully!";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $messages[] = "Failed to create project: " . $conn->error;
        }
    }
}

// Fetch projects for the logged-in student
$student_id = $_SESSION['user_id']; // Ensure this is defined
$sql = "SELECT * FROM student_projects WHERE student_id = '$student_id'";
$result = $conn->query($sql);
$projects = [];

while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

// Handle project deletion
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM student_projects WHERE id = $delete_id");
    header("Location: stud_projects.php"); // Redirect to avoid resubmission
    exit();
}

// Handle project edit (GET request to show the edit form)
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $edit_sql = "SELECT * FROM student_projects WHERE id = $edit_id";
    $edit_result = $conn->query($edit_sql);
    $edit_project = $edit_result->fetch_assoc();
}

// Handle project update (POST request)
if (isset($_POST['update_project'])) {
    $project_id = $_POST['project_id'];
    $project_title = $conn->real_escape_string($_POST['project_title']);
    $project_description = $conn->real_escape_string($_POST['project_description']);

    $update_sql = "UPDATE student_projects SET project_title = '$project_title', project_description = '$project_description' WHERE id = $project_id";

    if ($conn->query($update_sql) === TRUE) {
        $messages[] = "Project updated successfully!";
        header("Location: stud_projects.php");
        exit();
    } else {
        $messages[] = "Failed to update project: " . $conn->error;
    }
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
    <link rel="stylesheet" href="mentors.css">
    <script src="https://kit.fontawesome.com/0f4e2bc10d.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
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
}

.project-buttons a {
    padding: 5px 10px; /* Adjust padding for a better button feel */
    text-decoration: none; 
    border-radius: 5px;
    border: 2px solid #4b4276; /* Set the border color to #4b4276 by default */
    color: #4b4276; /* Text color */
    font-weight: bold; 
    transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; /* Smooth transition */
}

.edit-button {
    background-color: #FFFFFF; /* Background for Edit button */
}

.view-button {
    background-color: #FFFFFF; /* Background for View button */
}

.delete-button {
    background-color: #FFFFFF; /* Background for Delete button */
}

.project-buttons a:hover {
    opacity: 0.8; /* Slightly transparent on hover */
}

.edit-button:hover {
    background-color: #4b4276; /* Background color on hover */
    color: white; /* Text color on hover */
}

.view-button:hover {
    background-color: #4b4276; /* Background color on hover */
    color: white; /* Text color on hover */
}

.delete-button:hover {
    background-color: #4b4276; /* Background color on hover */
    color: white; /* Text color on hover */
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
            <h1>Project Dashboard</h1>
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
            <div class="header">
                <h1>Your Project Dashboard</h1>
            </div>

            <div class="form-container" id="form-container">
                <button id="create-project-btn">Create New Project</button>
                <form action="stud_projects.php" method="POST">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="project_title">Project Title:</label>
                            <input type="text" name="project_title" id="project_title" required />
                        </div>
                        <div class="form-group">
                            <label for="project_description">Project Domain:</label>
                            <input type="text" name="project_description" id="project_description" required />
                        </div>
                    </div>
                    <button type="submit">Create Project</button>
                    <button type="button" id="cancel-btn">Cancel</button>
                </form>
            </div>

            <!-- Display messages -->
            <?php if (!empty($messages)): ?>
                <div class="error-message">
                    <?php foreach ($messages as $message): ?>
                        <p><?php echo $message; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <h2>Your Created Projects</h2>
            <div id="created-projects">
                <?php foreach ($projects as $project): ?>
                    <div class="project-box">
                        <h3><?php echo $project['project_title']; ?></h3>
                        <p><strong>Domain:</strong> <?php echo $project['project_description']; ?></p>
                        <p><strong>Submitted on:</strong> <?php echo $project['submission_date']; ?></p>
                        <p><strong>Status:</strong> <?php echo $project['status']; ?></p><br>
                        <div class="project-buttons">
                            <a href="edit_projects.php?edit_id=<?php echo $project['id']; ?>" class="edit-button">Edit</a>
                            <a href="view_projects.php?view_id=<?php echo $project['id']; ?>" class="view-button">View</a>
                            <a href="stud_projects.php?delete_id=<?php echo $project['id']; ?>" class="delete-button">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            
        </div>
    </section>

    <script>
        document.getElementById('create-project-btn').onclick = function() {
            document.getElementById('form-container').classList.toggle('hidden');
        }

        document.getElementById('cancel-btn').onclick = function() {
            document.getElementById('form-container').classList.add('hidden');
        }
    </script>
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
</script>
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
