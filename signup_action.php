<?php
session_start();

// Database connection settings
$host = 'localhost'; 
$db = 'teams_management'; 
$user = 'root'; 
$pass = ''; 

// Create a connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_type = $_POST['user_type']; // Get the user type from the form

    // Check if the user type is student or staff/mentor
    if ($user_type === 'student') {
        $identifier = $_POST['roll_number']; // For students, use roll_number
        $identifier_column = 'roll_number';
        $table_name = 'students';
        $role = null; // No role for students
    } else {
        $identifier = $_POST['username']; // For staff/mentors, use username (email)
        $identifier_column = 'email';
        $table_name = 'staff';
        $role = $user_type; // Set the role based on the user type
    }

    $password = $_POST['password'];

    // Validate input
    if (empty($user_type) || empty($identifier) || empty($password)) {
        echo "All fields are required.";
        exit;
    }

    // Check if the user already exists
    $stmt = $conn->prepare("SELECT * FROM $table_name WHERE $identifier_column = ?");
    $stmt->bind_param("s", $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "User already exists.";
    } else {
        // User does not exist, insert new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        // Prepare an insert statement
        if ($table_name === 'students') {
            $insert_stmt = $conn->prepare("INSERT INTO students (roll_number, password) VALUES (?, ?)");
            $insert_stmt->bind_param("ss", $identifier, $hashed_password);
        } else {
            $insert_stmt = $conn->prepare("INSERT INTO staff (email, password, role) VALUES (?, ?, ?)");
            // Bind the parameters including the role
            $insert_stmt->bind_param("sss", $identifier, $hashed_password, $role);
        }

        if ($insert_stmt->execute()) {
            echo "User registered successfully.";
            // Redirect to the sign-in page
            header("Location: signin.php");
            exit();
        } else {
            echo "Error registering user: " . $insert_stmt->error;
        }
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
