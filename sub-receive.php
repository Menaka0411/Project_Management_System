<?php
session_start();
include 'db_connection.php';
include 'includes/profile_pic.php';
$username = $_SESSION['username'] ?? 'Vaishali'; 
$role = $_SESSION['role'] ?? 'N/A';
$mentor_data = $_SESSION['mentor_data'] ?? null;
$dashboard_data = $_SESSION['dashboard_data'] ?? null;
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image

$sql = "SELECT title, team_name, status, leader, members, abstract, ppt_path, mentor_id FROM projects";
$result = $conn->query($sql);

// Handle file viewing logic
if (isset($_GET['file'])) {
    $file = $_GET['file'];

    // Validate and sanitize the file path to prevent security issues
    $filePath = './uploads/' . basename($file);

    // Check if the file exists
    if (file_exists($filePath)) {
        // Set headers to display the file in the browser
        header('Content-Type: application/vnd.ms-powerpoint'); // For .ppt files
        header('Content-Disposition: inline; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($filePath));
        
        // Read the file and send it to the browser
        readfile($filePath);
        exit;
    } else {
        echo 'File not found.';
    }
} else {
    echo 'No file specified.';
}
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
      /* Table styling within a specific container (e.g., class 'table-container') */
.table-container table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.table-container th, .table-container td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
}

.table-container th {
    background-color: #f2f2f2;
}

.table-container tr:hover {
    background-color: #f1f1f1;
}

/* Style for the abstract to prevent excessive wrapping */
.table-container td {
    white-space: normal; /* Allow text wrapping */
    word-wrap: break-word; /* Break long words if necessary */
}

/* Dropdown styling */
.table-container .action-dropdown {
    padding: 10px 15px;  /* Increase the padding for a bigger appearance */
    font-size: 1em;      /* Adjust font size as needed */
    border-radius: 5px;  /* Maintain rounded corners */
    border: 1px solid #ddd; /* Consistent border */
    width: 120px;        /* Set width as needed */
    cursor: pointer;     /* Show pointer cursor on hover */
}

        /* Dropdown styling */
        .action-dropdown {
            padding: 10px 15px;  /* Increase the padding for a bigger appearance */
            font-size: 1em;      /* Adjust font size as needed */
            border-radius: 5px;  /* Maintain rounded corners */
            border: 1px solid #ddd; /* Consistent border */
            width: 120px;        /* Set width as needed */
            cursor: pointer;     /* Show pointer cursor on hover */
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
            <li><a href="project_list.php"><i class="fas fa-address-card"></i>Projects</a></li>
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
                <th>Description</th>
                <th>PPT Link</th>
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
                            <?php
                            // Replace newline characters with a space for a continuous paragraph
                            $abstract = $row['abstract'] ? str_replace(array("\r", "\n"), ' ', $row['abstract']) : 'N/A';
                            echo nl2br(htmlspecialchars($abstract)); // Use nl2br for any remaining HTML line breaks
                            ?>
                        </td>
                        <td>
                            <?php if ($row['ppt_path']): ?>
                                <a href="view_file.php?file=<?php echo urlencode(basename($row['ppt_path'])); ?>" target="_blank">View File</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
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
                window.location.href = 'view_project.php?id=' + mentorId; // Replace with your view page URL
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
