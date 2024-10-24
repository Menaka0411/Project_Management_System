<?php
session_start();
include 'db_connection.php';
include 'includes/profile_pic.php';
$username = $_SESSION['username'] ?? 'Vaishali'; 
$role = $_SESSION['role'] ?? 'N/A';
$mentor_data = $_SESSION['mentor_data'] ?? null;
$dashboard_data = $_SESSION['dashboard_data'] ?? null;
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $team_id = $_POST['team_id'];
    $mentor_id = $_POST['mentor_id'];

    // Update the team to assign the mentor
    $query = "UPDATE teams SET mentor_id = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $mentor_id, $team_id);

    if ($stmt->execute()) {
        echo "Mentor has been successfully allocated to the team!";
    } else {
        echo "Error allocating mentor: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PMS Mentor Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/allocate-mentor.css">
    <link rel="stylesheet" href="mentors.css">
    <script src="https://kit.fontawesome.com/0f4e2bc10d.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
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
        <h2 class="profile-email"><?php echo htmlspecialchars($username); ?></h2>
        <p class="profile-role" style="text-align: center;"><?php echo htmlspecialchars($role); ?></p> 
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
            <li><a href="submission.html"><i class="fas fa-blog"></i>Submission</a></li>
            <li><a href="viewteams.php"><i class="fas fa-address-book"></i>Teams</a></li>
            <li><a href="cal.php"><i class="fas fa-calendar-alt"></i>Schedule</a></li>
        </ul>
    </div>


    <div class="main_header">
        <div class="header">
            <h1>ALLOCATE MENTORS</h1>
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

<section id="allocate">
    <h2>Allocate Mentors to Teams</h2>
    <div class="container mx-auto mt-10">
        
        <table>
            <thead>
                <tr>
                    <th>Team</th>
                    <th>Mentor</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
         
                    <?php
                        // Fetch teams from the database
                        $teams_query = "SELECT id, team_name FROM teams";
                        $teams_result = mysqli_query($conn, $teams_query);

                        while ($team = mysqli_fetch_assoc($teams_result)) {
                            $mentors_query = "SELECT id, email FROM staff WHERE role = 'Mentor'";
                            $mentors_result = mysqli_query($conn, $mentors_query);
                            echo "<tr>";
                            echo "<td>{$team['team_name']}</td>";  // Team name
                            echo "<td>";
                            echo "<form method='POST' action='mentors_dash.php' class='inline'>";  // Form for each team
                            echo "<select name='mentor_id' class='p-2 border border-gray-300 rounded-lg'>";
                            while ($mentor = mysqli_fetch_assoc($mentors_result)) {
                                echo "<option value='{$mentor['id']}'>{$mentor['email']}</option>";
                            }
                            echo "</select>";
                            echo "<input type='hidden' name='team_id' value='{$team['id']}'>";
                            echo "</td>";  // Closing the mentor select dropdown's <td>
                            echo "<td>";  // New column for action button
                            echo "<button type='submit'>Allocate</button>";  // Button in the action column
                            echo "</form>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    ?>
                

            </tbody>
        </table>
        <?php if (isset($allocation_message)): ?>
            <div id="allocation-result"><?php echo $allocation_message; ?></div>
        <?php endif; ?>
    </div>
</section>


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
    document.querySelector('.dropdown-btn').addEventListener('click', function() {
    var dropdownContent = this.nextElementSibling;
    dropdownContent.style.display = dropdownContent.style.display === 'block' ? 'none' : 'block';
});


    
</script>

</body>
</html>