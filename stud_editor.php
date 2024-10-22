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
        <img src="assets/img/girlprofile.png" alt="" width="100px "/>
        <h2 class="profile-name">Vaishali</h2>
        <h2 class="profile-roll">21CS053</h2>
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
