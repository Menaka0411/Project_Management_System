<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$host = 'localhost'; // Database host
//$db = 'project_management_db';
$db = 'teams_management'; // Database name
$user = 'root'; // Database username
$pass = ''; // Database password

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
        // Fetch user data
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id']; // Assuming 'id' is the primary key
            $_SESSION['role'] = $user_type;
            $_SESSION['user'] = $user;
            $_SESSION['name'] = $user['name'] ?? $identifier; // Set the name or identifier
            $_SESSION['roll_number'] = $user['roll_number'] ?? ''; // Only for students

            // Redirect to the appropriate dashboard
            if ($user_type === 'student') {
                header("Location: stud_dash.php");
            } else {
                header("Location: staff_dash.php");
            }
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
