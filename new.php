<?php
include 'db_connection.php';  // Include database connection


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $team_name = $_POST['team_name'];
    $members = $_POST['members'];
    $tech_stack = $_POST['tech_stack'];
    $student_id = $_SESSION['student_id']; // Assuming you are storing student ID in session
    
    // Prepare SQL statement to insert the project data into the database
    $query = "INSERT INTO projects (student_id, title, description, team_name, members, tech_stack) 
              VALUES ('$student_id', '$title', '$description', '$team_name', '$members', '$tech_stack')";
    
    if (mysqli_query($conn, $query)) {
        $success_message = "Project created successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}


// Fetch all projects
$sql = "SELECT * FROM student_projects";
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
    <title>Student project Dashboard</title>
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
        .main-content{
            margin-top:100px;
            
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
            display: grid;
            grid-template-columns: 1fr 2fr; /* Label takes 1 part, input takes 2 parts */
            grid-gap: 15px;
            align-items: center;
            margin-bottom: 15px;
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
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #007bff;
            outline: none;
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

            <li class="dropdown">
                <a href="javascript:void(0)" class="dropdown-btn"><i class="fas fa-user"></i> Submission</a>
                <div class="dropdown-container">
                    <a href="add_sub.php"><i class="fas fa-user-plus"></i> Add Submission</a>
                    <a href="list_sub.php"><i class="fas fa-list"></i> List Submission</a>
                </div>
            </li>
            <li><a href="create_teams.php"><i class="fas fa-address-book"></i>Teams</a></li>
            <li><a href="stud_editor.php"><i class="fas fa-address-book"></i>Editor</a></li>
        </ul>
    </div>

    <div class="main_header">
        <div class="header">
            <h1>Project Creation</h1>
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
                            <label for="project_description">Project Description:</label>
                            <input type="text" name="project_description" id="project_description" required />
                        </div>
                        <div class="form-group">
                            <label for="team_name">Team Name:</label>
                            <input type="text" name="team_name" id="team_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="members">Team Members (Comma Separated):</label>
                            <input type="text" name="members" id="members" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="tech_stack">Technology Stack:</label>
                            <input type="text" name="tech_stack" id="tech_stack" required>
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
                        <p><strong>Description:</strong> <?php echo $project['project_description']; ?></p>
                        <p><strong>Submitted on:</strong> <?php echo $project['submission_date']; ?></p>
                        <p><strong>Status:</strong> <?php echo $project['status']; ?></p>
                        <div class="project-buttons">
                            <a href="edit_projects.php?edit_id=<?php echo $project['id']; ?>" class="edit-button">Edit</a>
                            <a href="view_projects.php?view_id=<?php echo $project['id']; ?>" class="view-button">View</a>
                            <a href="stud_projects.php?delete_id=<?php echo $project['id']; ?>" class="delete-button">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Edit Project Form -->
            <?php if (isset($edit_project)): ?>
                <div class="form-container">
                    <h2>Edit Project</h2>
                    <form action="stud_projects.php" method="POST">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="project_title">Project Title:</label>
                                <input type="text" name="project_title" id="project_title" required />
                            </div>
                            <div class="form-group">
                                <label for="project_description">Project Description:</label>
                                <input type="text" name="project_description" id="project_description" required />
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="team_members">Team Members:</label>
                                <input type="text" name="team_members" id="team_members" placeholder="Enter team members, separated by commas" />
                            </div>
                            <div class="form-group">
                                <label for="tech_stack">Technology Stack:</label>
                                <input type="text" name="tech_stack" id="tech_stack" placeholder="e.g., PHP, JavaScript, MySQL" />
                            </div>
                        </div>
                        <button type="submit">Create Project</button>
                        <button type="button" id="cancel-btn">Cancel</button>
                    </form>

                </div>
            <?php endif; ?>
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
</body>
</html>
