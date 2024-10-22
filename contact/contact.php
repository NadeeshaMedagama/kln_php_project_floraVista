<?php

global $connection;
include_once '../Function/function.php';
include_once '../Connection/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $sql = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";


    $stmt = $connection->prepare($sql);

    if ($stmt) {

        $stmt->bind_param('sss', $name, $email, $message);


        if ($stmt->execute()) {
            header("Location: success/success.html");
            exit();
        } else {
            echo "Error: Could not send message.";
        }

        $stmt->close();
    }
}

$connection->close();
