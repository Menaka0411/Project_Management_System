<?php
// Assuming you have already established a database connection
$servername = "localhost";  // Change to your server settings
$username = "root";         // Change to your MySQL username
$password = "";             // Change to your MySQL password
$dbname = "team_phoenix";   // Change to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch completed projects
$sql = "SELECT project_name, status, completion_date FROM projects WHERE status = 'completed'";
$result = $conn->query($sql);

// Initialize an array to store the project data
$completed_projects = array();

if ($result->num_rows > 0) {
    // Fetch the data and add it to the array
    while ($row = $result->fetch_assoc()) {
        $completed_projects[] = $row;
    }
}

// Return the completed projects as a JSON response
echo json_encode($completed_projects);

// Close the database connection
$conn->close();
?>
