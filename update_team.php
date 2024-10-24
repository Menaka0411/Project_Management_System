<?php
include('db_connection.php'); // Include the database connection
//include('db.php');
// Check if form data is set
if (isset($_POST['team_id'], $_POST['team_name'], $_POST['team_size'], $_POST['year'], $_POST['department'])) {
    $team_id = (int)$_POST['team_id'];
    $team_name = $conn->real_escape_string($_POST['team_name']);
    $team_size = (int)$_POST['team_size'];
    $year = (int)$_POST['year'];
    $department = $conn->real_escape_string($_POST['department']);

    // Update team query
    $sql = "UPDATE teams SET team_name = '$team_name', team_size = $team_size, year = $year, department = '$department' WHERE id = $team_id";

    if ($conn->query($sql) === TRUE) {
        // Update each member's data
        foreach ($_POST['member_name'] as $index => $member_name) {
            $member_id = (int)$_POST['member_id'][$index]; // Assuming you are passing member IDs in the form
            $member_name = $conn->real_escape_string($member_name);
            $roll_no = $conn->real_escape_string($_POST['roll_no'][$index]);
            $member_role = $conn->real_escape_string($_POST['member_role'][$index]);
            $member_email = $conn->real_escape_string($_POST['member_email'][$index]);
            $member_phone = $conn->real_escape_string($_POST['member_phone'][$index]);

            // Update member query
            $sql_member = "UPDATE team_members SET member_name = '$member_name', roll_no = '$roll_no', member_role = '$member_role', member_email = '$member_email', member_phone = '$member_phone' WHERE id = $member_id";

            if ($conn->query($sql_member) === FALSE) {
                echo "Error updating member ID $member_id: " . $conn->error;
            }
        }
        
        // Redirect to index or success page
        header("Location: create_teams.php");
        exit();
    } else {
        echo "Error updating team: " . $conn->error;
    }
} else {
    echo "Required fields are missing.";
}
?>
