<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost'; 
$db = 'teams_management'; 
$user = 'root'; 
$pass = ''; 

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture inputs
    $user_type = $_POST['user_type'];
    $password = $_POST['password'];
    $role = $_POST['role']; // This is the hidden role field from the form
    $identifier = $user_type === 'student' ? $_POST['roll_number'] : $_POST['username'];

    // Check if fields are empty
    if (empty($identifier) || empty($password)) {
        echo "<script>
                alert('All fields are required.');
              </script>";
    } else {
        // Determine table and column names based on user type
        $table_name = $user_type === 'student' ? 'students' : 'staff';
        $identifier_column = $user_type === 'student' ? 'roll_number' : 'email';

        // Check if user already exists
        $stmt = $conn->prepare("SELECT * FROM $table_name WHERE $identifier_column = ?");
        $stmt->bind_param("s", $identifier);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>
                    alert('User already exists. Please sign in.');
                    window.location.href = 'signin.php';
                  </script>";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Prepare the insert statement based on user type
            if ($user_type === 'student') {
                $stmt = $conn->prepare("INSERT INTO students (roll_number, password, login_time, profile_image, total_projects, completed_projects, ongoing_projects, overdue_projects) VALUES (?, ?, NULL, NULL, 0, 0, 0, 0)");
                $stmt->bind_param("ss", $identifier, $hashed_password);
            } else {
                // Staff, Mentor, Admin, etc.
                $stmt = $conn->prepare("INSERT INTO staff (email, password, role, profile_image, login_time) VALUES (?, ?, ?, NULL, NULL)");
                $stmt->bind_param("sss", $identifier, $hashed_password, $role);
            }

            // Execute the query and check for errors
            if ($stmt->execute()) {
                echo "<script>
                        alert('Registration successful! Redirecting to sign-in page...');
                        window.location.href = 'signin.php';
                      </script>";
            } else {
                echo "<script>
                        alert('Error registering user: " . $stmt->error . "');
                      </script>";
            }
        }

        // Close the statement
        $stmt->close();
    }
}

$conn->close();
?>
