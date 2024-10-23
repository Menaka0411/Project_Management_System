<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$host = 'localhost'; 
$db = 'teams_management'; 
$user = 'root'; 
$pass = ''; 

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message
$errorMessage = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = strtolower($_POST['user_type']); // Get the user type from the form
    $login_time = $_POST['login_time'];
    
    // Handle the role-based login
    if ($role === 'student') {
        $identifier = $_POST['roll_number']; // Use roll number for students
        $stmt = $conn->prepare("SELECT * FROM students WHERE roll_number = ?");
    } else {
        $identifier = $_POST['username']; // Use username for staff/mentor, assuming email
        $stmt = $conn->prepare("SELECT * FROM staff WHERE email = ?");
    }

    // Check if query preparation was successful
    if ($stmt === false) {
        die("Error preparing the query: " . $conn->error);
    }

    // Bind the identifier (roll number or username) to the query
    $stmt->bind_param("s", $identifier);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $password = $_POST['password'];

            // Verify the password
            if (password_verify($password, $user['password'])) {
                // Set session variables based on the role
                $_SESSION['role'] = $role;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['login_time'] = $login_time; // Store login time

                if ($role === 'student') {
                    $_SESSION['roll_number'] = $user['roll_number']; // Save roll number in session
                    header("Location: stud_dash.php");
                } elseif ($role === 'staff') {
                    $_SESSION['username'] = $user['username'];
                    error_log("Redirecting to mentors_dash.php for role: " . $role);
                    header("Location: mentors_dash.php"); // Redirect mentors to their dashboard
                    exit();
                } else {
                    $_SESSION['username'] = $user['username'];
                    // Adjust this based on what you want for other roles
                    header("Location: another_dashboard.php"); // Change this to the intended redirect for other roles
                    exit();
                }
                exit(); // Stop further script execution
            } else {
                $errorMessage = "Invalid password.";
                error_log("Invalid password for identifier: " . $identifier);
            }
        } else {
            $errorMessage = "Invalid credentials.";
            error_log("No user found for identifier: " . $identifier);
        }
    } else {
        $errorMessage = "Error executing query: " . $stmt->error;
    }

    $stmt->close();
}

// Close the database connection
$conn->close();
?>