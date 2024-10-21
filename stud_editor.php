
<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Student') {
    header("Location: signin.php");
    exit();
}

// Retrieve user data from session
$roll_number = $_SESSION['roll_number'] ?? 'N/A'; // Default to 'N/A' if not set

// Safely retrieve dashboard data
$dashboard_data = $_SESSION['dashboard_data'] ?? null;

// Retrieve user profile image if exists
$profile_image = $_SESSION['profile_image'] ?? 'https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg'; // Default image

// Handle profile picture upload logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $user_id = $_SESSION['user_id']; // Assuming you have the user's ID in the session

    // Directory where the profile images will be saved
    $target_dir = "uploads/profile_pics/";
    $imageFileType = strtolower(pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION));
    $new_filename = uniqid() . "." . $imageFileType; // Create a unique filename
    $target_file = $target_dir . $new_filename;

    // Check if the file is an actual image
    $check = getimagesize($_FILES["profile_pic"]["tmp_name"]);
    if ($check !== false) {
        // Check file size (limit to 5MB)
        if ($_FILES["profile_pic"]["size"] < 5000000) {
            // Allow certain file formats
            if (in_array($imageFileType, ["jpg", "jpeg", "png"])) {
                if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                    // File successfully uploaded, save the path in the database

                    include 'db_connection.php'; // Ensure proper connection to your database

                    // Update user profile picture in the database
                    $sql = "UPDATE students SET profile_image = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("si", $target_file, $user_id);

                    if ($stmt->execute()) {
                        $_SESSION['profile_image'] = $target_file; // Update session with new image path
                        header("Location: stud_dashboard.php"); // Redirect to dashboard after upload
                        exit();
                    } else {
                        echo "Error updating record: " . $conn->error;
                    }
                } else {
                    echo "Error uploading file.";
                }
            } else {
                echo "Only JPG, JPEG, and PNG files are allowed.";
            }
        } else {
            echo "File size exceeds the limit.";
        }
    } else {
        echo "File is not an image.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <title>Student Dashboard</title>
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

        .main-content{
            margin-top:100px;
            margin-left: 100px;
        }
        .abstract{
            margin-top: 100px; /* Top margin */
            margin-left: 250px; /* Left margin */
            width: calc(100% - 300px); /* Full width minus sidebar width */
            max-width: 1000px; /* Max width for larger screens */
            margin-bottom: 30px; /* Space at the bottom */
            padding: 20px; /* Padding inside the abstract section */
            background-color: white; /* Background for contrast */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Shadow effect */
            border-radius: 8px; /* Rounded corners */
            position: relative; /* Ensure it can contain floated elements */
            z-index: 1; /* Bring this element to the front */
        }
        .abstract h2{
            text-align: center;
            font-size: 32px;
            font-weight: 600;
        }
        .download-btn {
            background-color:#4b4276; 
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-size: 16px;
            margin: 10px;
            cursor: pointer;
            margin-left: 20px;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }
        .button-container{

            display: flex;
            justify-content: center;
            margin-top: 30px;
        }
        .download-btn:hover {
            background-color: #0056b3; 
            transform: translateY(-2px);
        }
        .download-btn:active {
            transform: translateY(1px);
        }
        .form-group-stud {
            position: relative; /* Ensure proper positioning */
            z-index: 2; /* Bring this element to the front */
            width: 100%; /* Full width for the form group */
            margin-top: 20px; /* Margin above textarea */
        }
        textarea {
            width: 100%; /* Full width of the parent */
            height: 200px; /* Fixed height for the textarea */
            border: 1px solid #ccc; /* Border style */
            border-radius: 5px; /* Rounded corners */
            padding: 10px; /* Padding inside textarea */
            resize: vertical; /* Allow vertical resizing only */
            font-family: 'Josefin Sans', sans-serif; /* Match font */
            box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.1); /* Inner shadow for effect */
        }
        @media (max-width: 768px) {
            .abstract {
                width: 90%; /* Adjust for smaller screens */
                margin-left: 5%;
                margin-top: 20px; /* Reduce top margin */
                margin-bottom: 20px; /* Reduce bottom margin */
            }
            .download-btn {
                font-size: 14px;
                padding: 10px 20px;
            }
            .main-content {
                margin-top: 20px; /* Reduce top margin */
                margin-left: 5%; /* Center it on smaller screens */
            }
        }
        
        @media (max-width: 576px) 
        {
            .sidebar {
                width: 100%; /* Full width for mobile */
                padding: 10px; /* Reduced padding */
            }

            .main_header {
                padding: 10px; /* Reduced padding */
            }

            .abstract {
                
                margin: 20px; /* Margin around the abstract */
                width: auto; /* Auto width for mobile */
                margin-top: 80px; /* Adjust top margin */
            }

            .download-btn {
                width: 100%; /* Full width buttons */
                margin: 5px 0; /* Spacing between buttons */
            }
        }
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
    <script src="https://cdn.tiny.cloud/1/ikqidsva7e09d7ojpfnaadk20er7mkf9ra3u324d63pp5cno/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
      tinymce.init({
        selector: '#abstract',
        plugins: [
          'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
          'checklist', 'mediaembed', 'casechange', 'export', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown',
        ],
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        tinycomments_mode: 'embedded',
        tinycomments_author: 'Author name',
        mergetags_list: [
          { value: 'First.Name', title: 'First Name' },
          { value: 'Email', title: 'Email' },
        ],
        ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
      });
      function downloadAbstract(event) {
            event.preventDefault(); 
            const abstractContent = tinymce.get("abstract").getContent({ format: 'text' });
            const blob = new Blob([abstractContent], { type: 'text/plain' });
            const downloadLink = document.createElement('a');
            downloadLink.href = URL.createObjectURL(blob);
            downloadLink.download = 'abstract.txt'; // Change the name if you want
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }

      function downloadAsPDF() {
            event.preventDefault();
            const abstractContent = tinymce.get("abstract").getContent({ format: 'text' });
            const blob = new Blob([abstractContent], { type: 'application/pdf' });
            const downloadLink = document.createElement('a');
            downloadLink.href = URL.createObjectURL(blob);
            downloadLink.download = 'abstract.pdf';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }

      function downloadAsDOCX() {
            event.preventDefault();  // Prevent form submission if downloadAsDOCX is called
            const abstractContent = tinymce.get("abstract").getContent({ format: 'text' });
            const blob = new Blob([abstractContent], { type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' });
            const downloadLink = document.createElement('a');
            downloadLink.href = URL.createObjectURL(blob);
            downloadLink.download = 'abstract.docx';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
        </script>
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


<section class="abstract">
    <h2>ABSTRACT EDITOR</h2>
            <div class="form-group-stud full-width">
                <!--<label for="abstract">Project Abstract:</label>-->
                <textarea id="abstract" name="abstract" required></textarea>
            </div>
</section>
<div class="button-container" style="text-align: center; ">
    <button class="download-btn" onclick="downloadAbstract()">Download Abstract as TXT</button>  
    <button class="download-btn" onclick="downloadAsPDF()">Download Abstract as PDF</button> 
    <button class="download-btn" onclick="downloadAsDOCX()">Download Abstract as DOCX</button>
</div>
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
