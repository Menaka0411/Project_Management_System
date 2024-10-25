<?php
session_start();
include 'includes/profile_pic.php';


// Retrieve user data from session
$roll_number = $_SESSION['roll_number'] ?? 'N/A'; // Default to 'N/A' if not set

// Safely retrieve dashboard data
$dashboard_data = $_SESSION['dashboard_data'] ?? null;

// Retrieve user profile image if exists
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; 
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
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <style>
                  /* General Styles */
                  body {
                font-family:sans-serif;
                background-color: whitesmoke;
                margin: 0;
                padding: 0;
            }

            .container {
                max-width: 100%;
                margin-left: 210px;
                padding: 20px;
                margin-top: 100px;
            }

            /* Project Card Styles */
            .project-card {
                background-color: white;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                padding: 20px;
                margin-bottom: 20px;
            }

            .project-title {
                font-size: 16px;
                color:#717171;
                text-transform: capitalize;
                margin-bottom: 10px;
            }

            .team-name {
                font-size: 18px;
                color: #333;
                margin-bottom: 15px;
            }
            .team-name, .semester, .technology-stack {
                font-size: 18px;
                color: #333;
                margin-bottom: 10px;
            }

            .project-details h3 {
                font-size: 20px;
                color: #594f8d;
                margin-bottom: 10px;
            }

            .project-details p {
                margin-bottom: 10px;
                font-size: 16px;
                line-height: 1.5;
            }

            .project-details p strong {
                color: #333;
            }

            .project-details a {
                background-color: #594f8d;
                color: #fff;
                padding: 8px 12px;
                border-radius: 4px;
                text-decoration: none;
                font-weight: bold;
            }

            .project-details a:hover {
                background-color: #483a8b;
                text-decoration: underline;
            }

            /* Download button for PPT */
            

            .project-details a:hover {
                background-color: #483a8b;
            }

            /* Additional styles for spacing */
            .project-card {
                margin-bottom: 30px;
            }

            h2, h3 {
                margin-top: 0;
            }
            h2>em{
                color:#594f8d;
                font-size: 2rem;
                font-style: normal;
            }
        
        .abstract {
            height: 200px;
            width: 700px;
            margin-left: 250px;
        }
        .abstract h2 {
            text-align: justify;
            font-size: 32px;
            font-weight: 600;
        }
        .h2-tag {
            text-transform: uppercase;
            margin-left: 100px;
        }
        .profile-roll {
    margin-top: 20px; /* Add some space between profile picture and roll number */
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
            <h1>VIEW PROJECT</h1>
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
<?php
// Include database connection file
include('db.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if 'view_id' is set in the URL
if (isset($_GET['view_id'])) {
    $view_id = $_GET['view_id'];

    // SQL query to fetch project details using view_id from the correct table 'student_projects'
    $query = "SELECT * FROM student_projects WHERE id = ?";

    // Prepare and execute the query
    if ($stmt = mysqli_prepare($conn, $query)) {
        // Bind the view_id to the prepared statement
        mysqli_stmt_bind_param($stmt, "i", $view_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        // Check if the project exists
        if (mysqli_num_rows($result) > 0) {
            // Fetch project details
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<h1>Project Details</h1>";

                // Handle 'null' values with fallback for missing project_name and description
                $project_name = isset($row['project_name']) ? htmlspecialchars($row['project_name']) : 'N/A';
                $description = isset($row['description']) ? htmlspecialchars($row['description']) : 'N/A';
                $status = isset($row['status']) ? htmlspecialchars($row['status']) : 'N/A';

                // Output project details
                echo "<p><strong>Project Name:</strong> " . $project_name . "</p>";
                echo "<p><strong>Description:</strong> " . $description . "</p>";
                echo "<p><strong>Status:</strong> " . $status . "</p>";
            }
        } else {
            // No project found with the given view_id
            echo "<p>No project found with ID " . htmlspecialchars($view_id) . "</p>";
        }
    } else {
        // Error preparing the statement
        echo "<p>Error: Could not prepare the statement.</p>";
    }

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    // If 'view_id' is not set in the URL
    echo "<p>Error: view_id is missing from the URL.</p>";
}

// Close the database connection
mysqli_close($conn);
?>


         
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
