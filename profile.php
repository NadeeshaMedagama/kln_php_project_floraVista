<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>


    <link rel="stylesheet" href="/style/profile.css">
</head>
<body>

<div class="container">

<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "./Connection/connection.php";
include_once "./Function/function.php";

if(!isset($_SESSION['user']['islogin']) || $_SESSION['user']['islogin'] == false){
    header("Location: login.php");
}

$user_id = $_SESSION['user']['user_id'];
$user_name = $_SESSION['user']['user_name'];
$user_email = $_SESSION['user']['email'];
$mobile = $_SESSION['user']['mobile'];


if(isset($_POST['submit'])){
    $user_id = $_POST['user_id'];
    $user_name = $_POST['username'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];

    $_SESSION['user']['user_name'] =  $user_name;
    $_SESSION['user']['email'] = $email;
    $_SESSION['user']['mobile'] = $mobile;

    $query = "UPDATE users SET user_name='$user_name', email='$email', mobile='$mobile' WHERE  user_id='$user_id'";
    if(mysqli_query($connection,$query)){
        header("Location: ./profile.php");
    }
}

echo "<h2>User Information</h2>";

echo "<form action='profile.php' method='post' >
            <input type='hidden' name='user_id'  value='$user_id'>
            <lable>Username : </lable> <br>
            <input type='text' name='username' value='$user_name' required>
            <br><br>
            <lable>Email : </lable> <br>
            <input type='email' name='email' value='$user_email' required>
            <br><br>
            <lable>Mobile : </lable> <br>
            <input type='text' name='mobile' value='$mobile' required><br><br>
            <button type='submit' name='submit'>Update</button>
            <button type='submit' name='logout' class='logout'>Logout</button><br>
    
          </form>";
            echo "<br><a href = 'index.php'><button type='submit' class='back-home-btn'>Back to Home</button></a><br>";


    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("Location: Admin/home/home.php");
        exit();
    }

if(isset($_SESSION['user']['loyalty_id']) && isset($_SESSION['user']['points_blance'])){
    echo "<h4>Loyalty Information</h4>";
    echo "Loyalty ID : ".$_SESSION['user']['loyalty_id']."<br>";
    echo "Points Balance : ".$_SESSION['user']['points_blance']."<br>";
}

echo "<br><h3>My Orders</h3>";

$query = "SELECT * FROM delivery_items WHERE user_id='$user_id'";
$result = mysqli_query($connection,$query);

if(mysqli_num_rows($result)>0){
    while($row = mysqli_fetch_assoc($result)){
        $flower_id = $row['flower_id'];
        $quantity = $row['quantity'];
        $reference_no =  $row['reference_no'];

        $flower_q = "SELECT * FROM flowers WHERE  flower_id='$flower_id'";
        $flower_result = mysqli_query($connection,$flower_q);
        $flower_name = mysqli_fetch_assoc($flower_result)['flower_name'];

        echo  "<lable><b>Order ID : </b>$reference_no</lable><br> 
               <lable><b>Flower Name: </b>$flower_name</lable><br> 
               <lable><b>Quantity : </b>$quantity </lable><br>";
    }
}

?>

</div>


</body>
</html>
