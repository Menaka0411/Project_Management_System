<?php
session_start();
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $password = $_POST['password'];
    $login_time = date("Y-m-d H:i:s"); // Current timestamp

    // Determine if the user is a student or staff
    if ($role === 'Student') {
        $roll_number = $_POST['roll_number'];
        $_SESSION['roll_number'] = $roll_number;

        // Query the students table using roll number
        $stmt = $conn->prepare("SELECT * FROM students WHERE roll_number = ?");
        $stmt->bind_param("s", $roll_number);
    } else {
        $username = $_POST['username'];
        $_SESSION['username'] = $username; // Store username in session for later use

        // Query the staff table using username
        $stmt = $conn->prepare("SELECT * FROM staff WHERE email = ? AND role = ?");
        $stmt->bind_param("ss", $username, $role); // Bind both email and role
    }

    // Execute the query
    if ($stmt->execute()) {
        $result = $stmt->get_result();

        // Check if user exists and password matches
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Check if the password is correct
            if (password_verify($password, $user['password'])) {
                // Successful login, set session variables
                $_SESSION['role'] = $role;
                $_SESSION['user_id'] = $user['id']; // Assuming each table has an 'id' field

                // Redirect to student dashboard or mentor dashboard based on role
                if ($role === 'Student') {
                    header("Location: stud_dash.php");
                } else {
                    header("Location: mentors_dash.php");
                }
                exit();
            } else {
                echo "Invalid password.";
            }
        } else {
            echo "Invalid credentials.";
        }
    } else {
        echo "Error executing query: " . $stmt->error;
    }
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
