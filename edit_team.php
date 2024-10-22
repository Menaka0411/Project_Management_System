<?php
include 'db_connection.php';
//include 'db.php'; // Include the database connection

// Get the team ID from the URL (edit_team.php?id=1)
if (isset($_GET['id'])) {
    $team_id = (int)$_GET['id'];

    // Fetch team details
    $sql_team = "SELECT * FROM teams WHERE id = $team_id";
    $team_result = $conn->query($sql_team);
    $team = $team_result->fetch_assoc();

    if ($team) {
        // Fetch team members
        $sql_members = "SELECT * FROM team_members WHERE team_id = $team_id";
        $member_result = $conn->query($sql_members);
        $team['members'] = [];
        while ($member = $member_result->fetch_assoc()) {
            $team['members'][] = $member;
        }
    } else {
        echo "Team not found.";
        exit();
    }
} else {
    echo "No team ID provided.";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="edit-style.css">
    <script src="https://kit.fontawesome.com/0f4e2bc10d.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Josefin+Sans&display=swap">
    <style>
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
            <li><a href="stud_submission.php"><i class="fas fa-blog"></i>Submission</a></li>
            <li><a href="create_teams.php"><i class="fas fa-address-book"></i>Teams</a></li>
        
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
    <h1>Edit Team: <?= htmlspecialchars($team['team_name']) ?></h1>

    <form action="update_team.php" method="POST">
        <input type="hidden" name="team_id" value="<?= $team['id'] ?>">

        <label for="team_name">Team Name:</label>
        <input type="text" id="team_name" name="team_name" value="<?= htmlspecialchars($team['team_name']) ?>" required><br>

        <label for="team_size">Team Size:</label>
        <input type="number" id="team_size" name="team_size" value="<?= $team['team_size'] ?>" required><br>

        <label for="year">Year:</label>
        <input type="number" id="year" name="year" value="<?= $team['year'] ?>" required><br>

        <label for="department">Department:</label>
        <input type="text" id="department" name="department" value="<?= htmlspecialchars($team['department']) ?>" required><br>

        <h3>Team Members:</h3>
        <?php foreach ($team['members'] as $index => $member): ?>
            <div class="member">
                <input type="hidden" name="member_id[]" value="<?= $member['id'] ?>">
                <label for="member_name_<?= $index ?>">Name:</label>
                <input type="text" id="member_name_<?= $index ?>" name="member_name[]" value="<?= htmlspecialchars($member['member_name']) ?>" required><br>

                <label for="roll_no_<?= $index ?>">Roll No:</label>
                <input type="text" id="roll_no_<?= $index ?>" name="roll_no[]" value="<?= htmlspecialchars($member['roll_no']) ?>" required><br>

                <label for="member_role_<?= $index ?>">Role:</label>
                <input type="text" id="member_role_<?= $index ?>" name="member_role[]" value="<?= htmlspecialchars($member['member_role']) ?>" required><br>

                <label for="member_email_<?= $index ?>">Email:</label>
                <input type="email" id="member_email_<?= $index ?>" name="member_email[]" value="<?= htmlspecialchars($member['member_email']) ?>" required><br>

                <label for="member_phone_<?= $index ?>">Phone:</label>
                <input type="text" id="member_phone_<?= $index ?>" name="member_phone[]" value="<?= htmlspecialchars($member['member_phone']) ?>" required><br>
            </div>
        <?php endforeach; ?>

        <div class="button-group">
    <button type="submit">Update Team</button>
    <a href="index.php">
        <button class="back">Go Back</button>
    </a>
</div>
</form>
</a>
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
