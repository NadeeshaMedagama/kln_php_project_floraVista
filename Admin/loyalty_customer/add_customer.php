<?php
global $connection;
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../../Connection/connection.php";
include_once "../../Function/function.php";

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

echo "<head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>User Profile</title>
        <link rel='stylesheet' href='addCustomer.css'> <!-- Link to your CSS file -->
      </head>";

echo "<body>";
echo "<div class='container'>"; // Start the container for styling
echo "<h2>User Information</h2>";

echo "<form action='profile.php' method='post'>
        <input type='hidden' name='user_id' value='$user_id'>
        <label>Username: </label> <br>
        <input type='text' name='username' value='$user_name' required>
        <br><br>
        <label>Email: </label> <br>
        <input type='email' name='email' value='$user_email' required>
        <br><br>
        <label>Mobile: </label> <br>
        <input type='text' name='mobile' value='$mobile' required><br><br>
        <button type='submit' name='submit'>Update</button>
      </form>";

if(isset($_SESSION['user']['loyalty_id']) && isset($_SESSION['user']['points_blance'])){
    echo "<h4>Loyalty Information</h4>";
    echo "Loyalty ID: " . $_SESSION['user']['loyalty_id'] . "<br>";
    echo "Points Balance: " . $_SESSION['user']['points_blance'] . "<br>";
}

echo "<h4>My Orders</h4>";

$query = "SELECT * FROM delivery_items WHERE user_id='$user_id'";
$result = mysqli_query($connection, $query);

if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
        $flower_id = $row['flower_id'];
        $quantity = $row['quantity'];
        $reference_no =  $row['reference_no'];

        $flower_q = "SELECT * FROM flowers WHERE flower_id='$flower_id'";
        $flower_result = mysqli_query($connection, $flower_q);
        $flower_name = mysqli_fetch_assoc($flower_result)['flower_name'];

        echo "<label>Order ID: $reference_no</label> &nbsp;
              <label>Flower Name: $flower_name</label> &nbsp;
              <label>Quantity: $quantity</label><br>";
    }
}
echo "</div>"; // End the container
echo "</body>";
?>
