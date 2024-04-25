<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Saved Notes</title>
    <style>
        /* styles.css */
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .notes-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .notes-table th, .notes-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        .notes-table th {
            background-color: #f2f2f2;
        }

        .notes-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .notes-table tr:hover {
            background-color: #e9e9e9;
        }

        .action-buttons {
            display: flex;
            justify-content: space-around;
        }

        .action-button {
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .delete-button {
            background-color: #dc3545;
            color: white;
        }

        .delete-button:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }

        .edit-button {
            background-color: #28a745;
            color: white;
        }

        .edit-button:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        /* Add button style for styled-button */
        .styled-button {
            position: relative;
            display: inline-block;
            padding: 10px 20px;
            font-weight: bold;
            color: #fff;
            font-size: 16px;
            text-decoration: none;
            text-transform: uppercase;
            overflow: hidden;
            transition: .5s;
            margin-top: 40px;
            letter-spacing: 3px;
            border: none;
            background-color: transparent;
            cursor: pointer;
        }

        .styled-button:hover {
            background: #fff;
            color: #272727;
            border-radius: 5px;
        }

        .styled-button span {
            position: absolute;
            display: block;
        }

        .styled-button span:nth-child(1) {
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #fff);
            animation: btn-anim1 1.5s linear infinite;
        }

        @keyframes btn-anim1 {
            0% {
                left: -100%;
            }
            50%, 100% {
                left: 100%;
            }
        }

        .styled-button span:nth-child(2) {
            top: -100%;
            right: 0;
            width: 2px;
            height: 100%;
            background: linear-gradient(180deg, transparent, #fff);
            animation: btn-anim2 1.5s linear infinite;
            animation-delay: .375s;
        }

        @keyframes btn-anim2 {
            0% {
                top: -100%;
            }
            50%, 100% {
                top: 100%;
            }
        }

        .styled-button span:nth-child(3) {
            bottom: 0;
            right: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(270deg, transparent, #fff);
            animation: btn-anim3 1.5s linear infinite;
            animation-delay: .75s;
        }

        @keyframes btn-anim3 {
            0% {
                right: -100%;
            }
            50%, 100% {
                right: 100%;
            }
        }

        .styled-button span:nth-child(4) {
            bottom: -100%;
            left: 0;
            width: 2px;
            height: 100%;
            background: linear-gradient(360deg, transparent, #fff);
            animation: btn-anim4 1.5s linear infinite;
            animation-delay: 1.125s;
        }

        @keyframes btn-anim4 {
            0% {
                bottom: -100%;
            }
            50%, 100% {
                bottom: 100%;
            }
        }

    </style>
</head>

<body>
    <div class="container">
        <h2>Saved Notes</h2>
        <table class="notes-table">
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            <!-- This section is populated by PHP below -->
            <?php
            // Database connection
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

            // Query to retrieve notes
            $sql = "SELECT id, title, description FROM notes ORDER BY created_at DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["title"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["description"]) . "</td>";
                    echo "<td class='action-buttons'>";
                    echo "<button class='action-button edit-button' onclick='editNote(" . $row["id"] . ")'>Edit</button>";
                    echo "<button class='action-button delete-button' onclick='deleteNote(" . $row["id"] . ", this)'>Delete</button>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No notes found.</td></tr>";
            }

            // Close connection
            $conn->close();
            ?>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Function to delete a note
        function deleteNote(noteId, button) {
            // Confirm the deletion
            var confirmation = confirm("Are you sure you want to delete this note?");
            if (confirmation) {
                // AJAX request to delete the note
                $.ajax({
                    url: 'delete_note.php',
                    type: 'POST',
                    data: {
                        note_id: noteId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Remove the row from the table
                            button.closest("tr").remove();
                            alert("Note deleted successfully.");
                        } else {
                            alert("Failed to delete the note: " + response.error);
                        }
                    },
                    error: function() {
                        alert("An error occurred while deleting the note. Please try again.");
                    }
                });
            }
        }

        // Function to edit a note
        function editNote(noteId) {
            // Redirect to the edit note page, passing the note ID as a URL parameter
            window.location.href = 'edit_note.php?note_id=' + noteId;
        }
    </script>
</body>

</html>
