<?php
// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "notese";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve note_id from POST data
$note_id = isset($_POST['note_id']) ? intval($_POST['note_id']) : 0;

$response = [];

if ($note_id > 0) {
    // Prepare and execute delete statement
    $stmt = $conn->prepare("DELETE FROM notes WHERE id = ?");
    $stmt->bind_param("i", $note_id);
    
    if ($stmt->execute()) {
        // Deletion successful
        $response['success'] = true;
    } else {
        // Error occurred during deletion
        $response['success'] = false;
        $response['error'] = $stmt->error;
    }
    
    // Close statement
    $stmt->close();
} else {
    // Invalid note ID
    $response['success'] = false;
    $response['error'] = "Invalid note ID";
}

// Close connection
$conn->close();

// Return response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
