<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the posted data
    $staff_email = $_POST['staff_email'];
    $student_message = $_POST['student_message'];
    $student_email = $_SESSION['email']; // Assuming you store the student email in the session

    // Validate inputs
    if (!empty($staff_email) && !empty($student_message)) {
        // Prepare and bind the SQL statement
        $stmt = $conn->prepare("INSERT INTO messages (staff_email, student_email, message, team_name) VALUES (?, ?, ?, ?)");
        
        // Use the session to get the team name, assuming it's stored there
        $student_team = $_SESSION['team'];

        $stmt->bind_param("ssss", $staff_email, $student_email, $student_message, $student_team);

        // Execute the statement
        if ($stmt->execute()) {
            // Redirect back to progress.php with a success message
            header("Location: progress.php?success=Reply sent successfully.");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // Redirect back to progress.php with an error message
        header("Location: progress.php?error=Please fill in all fields.");
        exit();
    }
}

$conn->close();
?>
