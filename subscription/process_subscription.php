<?php
$host = 'localhost';
$username = 'root';
$password = '1234';
$database = 'kln_php';

$connection = mysqli_connect($host, $username, $password, $database);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

function user_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = user_input($_POST['name']);
    $email = user_input($_POST['email']);

    $stmt = $connection->prepare("INSERT INTO subscriptions (name, email) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $email);

    if ($stmt->execute()) {
        echo "<script>alert('Thank you for subscribing!'); window.location.href = 'thank you/thank_you.html';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.history.back();</script>";
    }

    $stmt->close();
}

mysqli_close($connection);
?>
