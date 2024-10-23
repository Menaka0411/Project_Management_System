<?php
session_start();
include 'db_connection.php'; 
include 'includes/profile_pic.php';
$roll_number = $_SESSION['roll_number'] ?? 'N/A'; 
$dashboard_data = $_SESSION['dashboard_data'] ?? null;

// Handle delete action
if (isset($_GET['delete_id'])) {
    $project_id = intval($_GET['delete_id']);
    
    // Delete the project
    $sql = "DELETE FROM projects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $project_id);

    if ($stmt->execute()) {
        echo "<script>alert('Project deleted successfully.');</script>";
    } else {
        echo "<script>alert('Error deleting project: " . $conn->error . "');</script>";
    }

    $stmt->close();
    echo "<script>window.location.href = 'stud_list.php';</script>";
    exit;
}

// Query to fetch project submissions
$sql = "SELECT id, title, team_name, mentor, status FROM projects";
$result = $conn->query($sql);
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
    <!-- Include Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

    <style>
/* Table styles */
table {
    width: 80%; /* Set a width for the table */
    border-collapse: collapse; /* Collapse borders */
    margin: 150px auto; 
    margin-right: 80px;
    font-family: 'Josefin Sans', sans-serif; /* Consistent font */
    border: none; /* Ensure no border on the table itself */
}

th, td {
    border: 1px solid #ddd; /* Lighter border for a subtle effect */
    padding: 12px; /* Increased padding for better spacing */
    text-align: left;
}

/* Header styles */
th {
    background-color: #594f8d; /* Purple background for header */
    color: white; /* White text for contrast */
    font-weight: bold; /* Bold text for headers */
}

/* Row styles */
tr:nth-child(even) {
    background-color: #f2f2f2; /* Light grey for even rows */
}

tr:hover {
    background-color: #ddd; /* Darker grey on hover for rows */
}

/* Action button styles */
.btn-default {
    background-color: #f8f9fa;
    border-color: #17a2b8 !important;
    color: #444;
}

/* Hover effect for the Action button */
.btn-default:hover {
    background-color: #e9ecef;
    color: #2b2b2b;
}

/* Remove shadow when the button is focused */
.btn:focus, .btn:active {
    box-shadow: none;
    outline: none;
}

/* Additional flat button styles */
.btn-flat {
    border-radius: 0;
    box-shadow: none;
}

/* Default border and color on hover */
.btn:hover {
    color: #212529;
    text-decoration: none;
    border-color: #17a2b8 !important;
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
            <h1>LIST SUBMISSIONS</h1>
            <div class="header_icons">
                <div class="search">
                    <input type="text" placeholder="Search..." />
                    <i class="fa-solid fa-magnifying-glass"></i>
                </div>
                <i class="fa-solid fa-bell"></i>
            </div>
        </div>
        <br>
    </div>
</div>
<table>
    <thead>
        <tr>
            <th>S.No</th>
            <th>Title</th>
            <th>Team Name</th>
            <th>Mentor</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
if ($result->num_rows > 0) {
    $serial = 1;  // Initialize serial number
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $serial . "</td>";
        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
        echo "<td>" . htmlspecialchars($row['team_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['mentor']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td class='text-center'>
        <div class='btn-group'>
            <button type='button' class='btn btn-default btn-sm btn-flat border-info wave-effect text-info dropdown-toggle' data-toggle='dropdown' aria-expanded='false'>
                Action
            </button>
            <div class='dropdown-menu'>
                <a class='dropdown-item view_task' href='progress.php?id=" . $row['id'] . "'>View</a>
                <div class='dropdown-divider'></div>
                <a class='dropdown-item delete_task' href='stud_list.php?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this project?\")'>Delete</a>
            </div>
        </div>
    </td>";
        echo "</tr>";
        $serial++;  // Increment serial number
    }
} else {
    echo "<tr><td colspan='6'>No submissions found</td></tr>";
}

        ?>
    </tbody>
</table>

<?php $conn->close(); ?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const dropdownBtns = document.querySelectorAll('.dropdown-btn');

    dropdownBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            this.classList.toggle("active");
            const dropdownContent = this.nextElementSibling;
            dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
        });
    });
});

    </script>
</body>
</html>
