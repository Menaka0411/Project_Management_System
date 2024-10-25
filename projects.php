<?php 
session_start();
include 'db_connection.php';
include 'includes/profile_pic.php';
$username = $_SESSION['username'] ?? 'Vaishali'; 
$role = $_SESSION['role'] ?? 'N/A';
$mentor_data = $_SESSION['mentor_data'] ?? null;
$dashboard_data = $_SESSION['dashboard_data'] ?? null;
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg';

$sql = "SELECT * FROM student_projects ORDER BY submission_date DESC";
$result = mysqli_query($conn, $sql);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'])) {
    $project_id = $_POST['project_id'];
    $mentor_comments = $_POST['mentor_comments'];
    $action_status = $_POST['action_status'];

    // Update query to save mentor comments and action status in the database
    $update_sql = "UPDATE student_projects SET mentor_comments = ?, status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $mentor_comments, $action_status, $project_id);
    $stmt->execute();
    $stmt->close();

    $insert_sql = "INSERT INTO mentor_actions (project_id, mentor_comments, action_status) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_sql);
    $stmt_insert->bind_param("iss", $project_id, $mentor_comments, $action_status);
    $stmt_insert->execute();
    $stmt_insert->close();
}


?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PMS Mentor Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/projects.css">
    <link rel="stylesheet" href="mentors.css">
    <script src="https://kit.fontawesome.com/0f4e2bc10d.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <style>
       .main-content {
            padding: 35px;
            margin-left: 2%;
            flex: 1; /* Take remaining space */
        }
       
        .main-content>h2{
            margin-left: 200px;
            padding: 10px 20px;
            background-color: #4e64bb;
            border-radius: 5px;
            border:2px solid #4e64bb;
        }
        .info>a{
            color: #4e64bb;
            font-weight: 400;

        }
        .project-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .project-table th, .project-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .project-table th {
            background-color: #4e64bb;
            color: white;
            text-align: center;
        }
        .project-table td a {
            color: #4e64bb;
            text-decoration: none;
        }
        .project-table td a:hover {
            text-decoration: underline;
        }
        .project-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .project-table tr:hover {
            background-color: #f1f1f1;
        }
         
        .mentor-comments {
            width: 100%;
        }

    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <li><a href="projects.php"><i class="fas fa-address-card"></i>Projects</a></li>
            <li><a href="receive.php"><i class="fas fa-blog"></i>Submission</a></li>
            <li><a href="viewteams.php"><i class="fas fa-address-book"></i>Teams</a></li>
            <li><a href="cal.php"><i class="fas fa-calendar-alt"></i>Schedule</a></li>
        </ul>
    </div>


    <div class="main_header">
        <div class="header">
            <h1>STUDENT PROJECTS</h1>
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
        <h1>Mentor Dashboard - Student Projects</h1>
        <table class="project-table">
            <thead>
                <tr>
                    <th>Project Title</th>
                    <th>Description</th>
                    <th>Team</th>
                    <th>Submission Date</th>
                    <th>Mentor Comments</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0) : ?>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <form method="POST">
                                <td><?php echo htmlspecialchars($row['project_title']); ?></td>
                                <td><?php echo htmlspecialchars($row['project_description']); ?></td>
                                <td><?php echo htmlspecialchars($row['team_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['submission_date']); ?></td>
                                
                                <!-- Mentor Comments Input -->
                                <td>
                                    <input type="text" name="mentor_comments" class="mentor-comments" placeholder="Enter comments" value="<?php echo htmlspecialchars($row['mentor_comments']); ?>">
                                </td>

                                <!-- Action Dropdown -->
                                <td>
                                    <select name="action_status">
                                        <option value="Pending" <?php echo ($row['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Approved" <?php echo ($row['status'] == 'Approved') ? 'selected' : ''; ?>>Approved</option>
                                        <option value="Disapproved" <?php echo ($row['status'] == 'Disapproved') ? 'selected' : ''; ?>>Disapproved</option>
                                    </select>
                                    <input type="hidden" name="project_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit">Update</button>
                                </td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7">No projects found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
<script src="assets/js/app.js"></script>
</body>
</html>
<?php
mysqli_close($conn); // Close the database connection
?>