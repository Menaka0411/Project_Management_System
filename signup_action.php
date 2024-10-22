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
    error_log("Signup form submitted"); // Log for debugging

    // Capture inputs
    $user_type = $_POST['user_type'];
    $password = $_POST['password'];
    $identifier = $user_type === 'student' ? $_POST['roll_number'] : $_POST['username'];

    // Validate input
    if (empty($identifier) || empty($password)) {
        echo "All fields are required.";
        exit;
    }

    // Determine table and identifier column
    $table_name = $user_type === 'student' ? 'students' : 'staff';
    $identifier_column = $user_type === 'student' ? 'roll_number' : 'email';

    // Check if user already exists
    $stmt = $conn->prepare("SELECT * FROM $table_name WHERE $identifier_column = ?");
    $stmt->bind_param("s", $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "User already exists.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert user into the database
        if ($user_type === 'student') {
            $insert_stmt = $conn->prepare("INSERT INTO students (roll_number, password) VALUES (?, ?)");
        } else {
            $role = $_POST['role']; // Make sure to capture the role for staff
            $insert_stmt = $conn->prepare("INSERT INTO staff (email, password, role) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $identifier, $hashed_password, $role);
        }
        
        $insert_stmt->bind_param("ss", $identifier, $hashed_password);

        if ($insert_stmt->execute()) {
            header("Location: signin.php"); // Redirect on success
            exit();
        } else {
            echo "Error registering user: " . $conn->error;
        }
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
