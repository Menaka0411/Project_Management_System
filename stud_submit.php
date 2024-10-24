<?php
// Include your database connection file
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $title = $_POST['title'];
    $team_name = $_POST['team'];
    $status = $_POST['status'];
    $leader = $_POST['leader'];
    $members = $_POST['members']; 
    $mentor_email = $_POST['mentor_id']; 
    $abstract = strip_tags($_POST['abstract']);

    // Initialize mentor ID variable
    $mentor_id = null;
    $mentor_id_valid = false;

    // Prepare statement to check if the mentor ID exists
    $stmt = $conn->prepare("SELECT id FROM staff WHERE email = ?");
    $stmt->bind_param("s", $mentor_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the mentor ID
        $row = $result->fetch_assoc();
        $mentor_id = $row['id']; 
        $mentor_id_valid = true;
    }

    // Check if the mentor ID is valid before proceeding
    if ($mentor_id_valid) {
        // File upload handling
        $ppt_path = null;
        if (isset($_FILES['ppt']) && $_FILES['ppt']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['ppt']['tmp_name'];
            $fileName = $_FILES['ppt']['name'];
            $fileSize = $_FILES['ppt']['size'];
            $fileType = $_FILES['ppt']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            // Specify allowed file types
            $allowedFileExtensions = ['ppt', 'pptx', 'jpg', 'jpeg', 'png', 'mp4'];

            if (in_array($fileExtension, $allowedFileExtensions)) {
                // Set the upload file path
                $uploadFileDir = './uploads/'; // Ensure this directory exists and is writable
                $ppt_path = $uploadFileDir . uniqid('file_', true) . '.' . $fileExtension;

                // Move the file to the specified directory
                if (move_uploaded_file($fileTmpPath, $ppt_path)) {
                    // File successfully uploaded
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Error moving the uploaded file.']);
                    exit;
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Upload failed. Allowed file types: ' . implode(', ', $allowedFileExtensions)]);
                exit;
            }
        }

        $stmt = $conn->prepare("INSERT INTO projects (title, team_name, status, leader, members, mentor, mentor_id, abstract, ppt_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssiss", $title, $team_name, $status, $leader, $members, $mentor_email, $mentor_id, $abstract, $ppt_path); // Adjusted to "ssssssiss"

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Submission successful!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid mentor ID!']);
    }
}

$conn->close();
?>
