<?php

$servername = "localhost";
$username_db = "root";
$password_db = "";
$database = "notese";

$conn = new mysqli($servername, $username_db, $password_db, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL query
    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the user's hashed password
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Login successful
            echo "<script>alert('Login successful'); window.location.href = 'notes.html';</script>";
        } else {
            echo "<script>alert('Invalid login credentials'); window.location.href = 'login.html';</script>";
        }
    } else {
        echo "<script>alert('Invalid login credentials'); window.location.href = 'login.html';</script>";
    }

    // Close statement
    $stmt->close();
}

// Close database connection
$conn->close();
?>