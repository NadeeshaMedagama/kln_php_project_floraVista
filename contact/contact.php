<?php
// Include the database connection file
global $connection;
include_once '../Function/function.php';
include_once '../Connection/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    // Prepare SQL statement to insert data
    $sql = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";


    $stmt = $connection->prepare($sql);

    // Bind parameters
    if ($stmt) {
        // Bind parameters
        $stmt->bind_param('sss', $name, $email, $message);

        // Execute the statement
        if ($stmt->execute()) {
            header("Location: success/success.html");
            exit();
        } else {
            echo "Error: Could not send message.";
        }

        $stmt->close();
    }
}

// Close connection
$connection->close();
