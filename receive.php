<?php
session_start();
include 'db_connection.php';
include 'includes/profile_pic.php';
if ($_SESSION['role'] !== 'Staff') {
    $_SESSION['role'] = 'Staff'; 
}

$username = $_SESSION['username'] ?? 'Vaishali'; 
$role = $_SESSION['role'];
$mentor_data = $_SESSION['mentor_data'] ?? null;
$dashboard_data = $_SESSION['dashboard_data'] ?? null;
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image

// Prepare a query to select all submissions from the projects table
$sql = "SELECT title, team_name, status, leader, members, abstract, ppt_path, mentor_id FROM projects";
$result = $conn->query($sql);

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
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
     <style>
.table-container {
            display: flex;
            justify-content: center; /* Center the table horizontally */
            margin-top: 10%; /* Space around the table */
        }

        table {
            border-collapse: collapse;
            margin: 5px auto; /* Center the table */
            margin-left: 20%; /* Move the table slightly to the right */
            font-size: 1em;
            width: 70%;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        
        }

        table thead tr {
            background-color: #4b4276;
            color: #ffffff;
            text-align: left;
            font-weight: bold;
        }

        table th, table td {
            padding: 12px 15px;
            text-align: center;
            vertical-align: middle;
        }

        table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        table tbody tr:hover {
            background-color: #f3f3f3;
        }

        table tbody tr:last-of-type {
            border-bottom: 2px solid #4b4276;
        }

        /* Profile link styling */
        table td a {
            text-decoration: none;
            color: #4b4276;
            font-weight: bold;
        }

        table td a:hover {
            color: #4b4276;
            text-decoration: underline;
        }

        /* Additional styles for dropdown */
        .action-dropdown {
            padding: 10px 15px;  
            font-size: 1em;      
            border-radius: 5px;  
            border: 1px solid #ddd; 
            width: 120px;        
            cursor: pointer;     
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
            <h1>PROJECT MANAGEMENT</h1>
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
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>Project Title</th>
                <th>Team Name</th>
                <th>Status</th>
                <th>Project Leader</th>
                <th>Members</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['team_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['leader']); ?></td>
                        <td><?php echo htmlspecialchars($row['members']); ?></td>
                        <td>
                            <select class='action-dropdown' onchange='handleAction(this, "<?php echo htmlspecialchars($row['mentor_id']); ?>")'>
                                <option value='' selected>Action</option>
                                <option value='view'>View</option>
                                <option value='delete'>Delete</option>
                            </select>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No submissions found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    </div>
    <script>
        function handleAction(select, mentorId) {
            const action = select.value;
            if (action === 'view') {
                // Implement the view functionality (e.g., navigate to the view page)
                window.location.href = 'sub-receive.php?id=' + mentorId; // Replace with your view page URL
            } else if (action === 'delete') {
                if (confirm('Are you sure you want to delete this submission?')) {
                    // Redirect to delete page
                    window.location.href = 'delete_project.php?id=' + mentorId; // Replace with your delete page URL
                } else {
                    // Reset the dropdown if cancelled
                    select.selectedIndex = 0;
                }
            }
        }
    </script>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>
</html>
