<?php
// Database configuration
$host = 'localhost'; // or your database host
$username = 'root';
$password = '1234';
$database = 'kln_php';

// Create a connection
$connection = mysqli_connect($host, $username, $password, $database);

// Check the connection
if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to sanitize user input
function user_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the input values
    $name = user_input($_POST['name']);
    $email = user_input($_POST['email']);

    // Prepare and bind
    $stmt = $connection->prepare("INSERT INTO subscriptions (name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);

    // Execute the query
    if ($stmt->execute()) {
        // Successfully inserted
        echo "<script>alert('Thank you for subscribing!'); window.location.href = 'thank you/thank_you.html';</script>";
    } else {
        // Handle errors
        echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
    }

    // Close the statement and connection
    $stmt->close();
}

mysqli_close($connection);
?>
