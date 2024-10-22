<?php
session_start();
include 'db_connection.php'; // Make sure you include your DB connection
include 'profile_pic.php'; // Include the profile picture logic

// Ensure the user is logged in and has the appropriate role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header("Location: signin.php");
    exit();
}

// Get the project ID from the URL
$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch project details
$project_query = $conn->query("SELECT * FROM projects WHERE id = $project_id");
$project = $project_query->fetch_assoc();

if (!$project) {
    die("Project not found.");
}

// Fetch tasks associated with the project
$total_tasks = $conn->query("SELECT COUNT(*) as count FROM projects WHERE project_id = $project_id")->fetch_assoc()['count'];
$completed_tasks = $conn->query("SELECT COUNT(*) as count FROM projects WHERE project_id = $project_id AND status = 5")->fetch_assoc()['count'];
$progress = $total_tasks > 0 ? ($completed_tasks / $total_tasks) * 100 : 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Project Progress</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://kit.fontawesome.com/0f4e2bc10d.js"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Include Summernote CSS and JS from CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-lite.min.js"></script>
</head>
<body>

<div class="container">
    <h2>Project Title: <?php echo htmlspecialchars($project['title']); ?></h2>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($project['description']); ?></p>
    
    <div class="progress-bar" style="width: <?php echo number_format($progress, 2); ?>%;">
        <div class="progress-text"><?php echo number_format($progress, 2); ?>% Complete</div>
    </div>

    <h3>Tasks Overview</h3>
    <div class="task-list">
        <?php
        $tasks_query = $conn->query("SELECT * FROM task_list WHERE project_id = $project_id");
        while ($task = $tasks_query->fetch_assoc()):
        ?>
            <div class="task">
                <h4><?php echo htmlspecialchars($task['task_name']); ?></h4>
                <p>Status: <?php echo htmlspecialchars($stat[$task['status']]); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Any additional JS for your page
    });
</script>

<style>
    .container {
        width: 80%;
        margin: 0 auto;
        background: #fff;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .progress-bar {
        background: #007BFF;
        height: 30px;
        border-radius: 5px;
        position: relative;
        margin-bottom: 20px;
    }
    .progress-text {
        color: #fff;
        text-align: center;
        line-height: 30px;
    }
    .task {
        background: #f8f9fa;
        margin: 10px 0;
        padding: 10px;
        border-radius: 5px;
    }
</style>

</body>
</html>
