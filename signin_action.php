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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log("Signin form submitted"); // Log for debugging

    $role = $_POST['role'];
    $password = $_POST['password'];
    $identifier = $role === 'student' ? $_POST['roll_number'] : $_POST['username'];

    if ($role === 'student') {
        $stmt = $conn->prepare("SELECT * FROM students WHERE roll_number = ?");
    } else {
        $stmt = $conn->prepare("SELECT * FROM staff WHERE email = ?");
    }
    
    $stmt->bind_param("s", $identifier);
    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['role'] = $role; // Store user role in session
                $_SESSION['user_id'] = $user['id']; // Store user ID for future use

                // Redirect to appropriate dashboard based on role
                if ($role === 'student') {
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
