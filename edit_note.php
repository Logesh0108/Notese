<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Note</title>
    <style>
        /* styles.css */
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 10px;
        }

        input[type="text"],
        textarea {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            width: 100%;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Note</h2>
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

        // Get the note ID from the URL or form
        $noteId = isset($_GET['note_id']) ? $_GET['note_id'] : (isset($_POST['note_id']) ? $_POST['note_id'] : null);

        if ($noteId === null) {
            die("No note ID provided.");
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Get the updated title and description from the form
            $updatedTitle = $_POST['title'];
            $updatedDescription = $_POST['description'];

            // Prepare the SQL statement to update the note in the database
            $updateSql = "UPDATE notes SET title = ?, description = ? WHERE id = ?";
            $stmt = $conn->prepare($updateSql);

            if ($stmt) {
                // Bind the parameters to the query
                $stmt->bind_param("ssi", $updatedTitle, $updatedDescription, $noteId);

                // Execute the query
                if ($stmt->execute()) {
                    echo "<script>alert('Note updated successfully!');</script>";
                    // Redirect to the show notes page
                    echo "<script>window.location.href = 'shownotes.php';</script>";
                } else {
                    echo "Error updating note: " . $stmt->error;
                }

                // Close the statement
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        } else {
            // Fetch the current title and description of the note
            $fetchSql = "SELECT title, description FROM notes WHERE id = ?";
            $stmt = $conn->prepare($fetchSql);

            if ($stmt) {
                // Bind the note ID to the query
                $stmt->bind_param("i", $noteId);

                // Execute the query
                $stmt->execute();

                // Fetch the result
                $stmt->bind_result($currentTitle, $currentDescription);

                if ($stmt->fetch()) {
                    // Display the form for editing the note
                    echo "<form method='POST' action='edit_note.php'>";
                    echo "<input type='hidden' name='note_id' value='" . htmlspecialchars($noteId) . "'>";
                    echo "<label for='title'>Title:</label>";
                    echo "<input type='text' id='title' name='title' value='" . htmlspecialchars($currentTitle) . "' required><br>";
                    echo "<label for='description'>Description:</label>";
                    echo "<textarea id='description' name='description' required>" . htmlspecialchars($currentDescription) . "</textarea><br>";
                    echo "<input type='submit' value='Save Changes'>";
                    echo "</form>";
                } else {
                    echo "Note not found.";
                }

                // Close the statement
                $stmt->close();
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
        }

        // Close the database connection
        $conn->close();
        ?>
    </div>
</body>

</html>
