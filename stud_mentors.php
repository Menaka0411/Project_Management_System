<?php
session_start();
include 'db_connection.php'; 
include 'includes/profile_pic.php';
$roll_number = $_SESSION['roll_number'] ?? 'N/A'; 
$dashboard_data = $_SESSION['dashboard_data'] ?? null;

$messages = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <title>PMS Student Dashboard</title>
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
    .mentor-section>h2{
        margin-left: 250px;
    }
   
        .main-content{
            margin-top:100px;
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
<section class="main-content">
    
    <section id="statistics">
        <h2>Project Statistics</h2>
        <div class="content-items">
            <div class="info">
                <h2 class="info-heading">Total Projects</h2>
                <div class="info-details">
                    <h3 class="info-numbers">3</h3>
                </div>
            </div>
            <div class="info">
                <h2 class="info-heading">Completed Projects</h2>
                <div class="info-details">
                    <h3 class="info-numbers">3</h3>
                </div>
            </div>
            <div class="info">
                <h2 class="info-heading">Ongoing Projects</h2>
                <div class="info-details">
                    <h3 class="info-numbers">2</h3> 
                </div>
            </div>
            <div class="info">
                <h2 class="info-heading">Overdue Projects</h2>
                <div class="info-details">
                    <h3 class="info-numbers">1</h3> 
                </div>
            </div>
        </div>
    </section>
</section>

<section class="mentor-section">
    <h2>Mentor Name:Vaishali</h2>
    <h2>Department:CSE</h2>

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
