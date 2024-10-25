<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $staff_email = $_POST['staff_email'];
    $student_message = $_POST['student_message'];
    $student_email = $_SESSION['email']; 

   
    if (!empty($staff_email) && !empty($student_message)) {
       
        $stmt = $conn->prepare("INSERT INTO messages (staff_email, student_email, message, team_name) VALUES (?, ?, ?, ?)");
        
       
        $student_team = $_SESSION['team'];

        $stmt->bind_param("ssss", $staff_email, $student_email, $student_message, $student_team);

        if ($stmt->execute()) {
           
            header("Location: progress.php?success=Reply sent successfully.");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        
        header("Location: progress.php?error=Please fill in all fields.");
        exit();
    }
}

$conn->close();
?>
