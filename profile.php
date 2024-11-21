<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page</title>
    <link rel="stylesheet" href="/style/profile.css">

</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="shop/shop.html">Shop</a></li>
            <li><a href="offers/offers.html">Special Offers</a></li>
            <li><a href="new arrivals/arrivals.html">New Arrivals</a></li>
            <li><a href="contact/contact.html">Contact Us</a></li>
            <li><a href="subscription/subscription.html">Subscribe</a></li>
            <li><a href="about/about.html">About Us</a></li>

            <div class='header-info'>
                <span class='cart'>
                <a href='cart/cart.php'><img class="cartimg" src='Admin/home/style/images/cart.png' width='28px' height='28px' alt='cart'><br>Cart</a>
                </span>
            </div>

        </ul>
    </nav>
</header><br><br>

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

echo "<h2>My Profile Information</h2>";

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
            <div class='logoutBtn'>
            <button type='submit' name='logout' class='logout'>Logout</button>
            </div><br>
    
          </form>";
            //echo "<br><a href = 'index.php'><button type='submit' class='back-home-btn'>Back to Home</button></a><br>";


    if (isset($_POST['logout'])) {
        session_unset();
        session_destroy();
        header("Location: Admin/home/home.php");
        exit();
    }

if(isset($_SESSION['user']['loyalty_id']) && isset($_SESSION['user']['points_balance'])){
    echo "<br><br><h2>Loyalty Information</h2>";
    echo "<b>Loyalty ID : </b>".$_SESSION['user']['loyalty_id']."<br>";
    echo "<b>Points Balance : </b>".$_SESSION['user']['points_balance']."<br>";
}

echo "<br><h2>My Orders</h2>";

$query = "SELECT * FROM delivery_items WHERE user_id='$user_id'";
$result = mysqli_query($connection,$query);

echo "<table>
        <thead>
        <tr>
        
            <th><h3 class='item' style='color: #555'>Item</h3></th>
            <th><h3 style='color: #555'>Order ID</h3></th>
            <th><h3 style='color: #555'>Quantity</h3></th>
            
        </tr>
        </thead>
        <tbody>";

if(mysqli_num_rows($result)>0){
    while($row = mysqli_fetch_assoc($result)){
        $flower_id = $row['flower_id'];
        $quantity = $row['quantity'];
        $reference_no =  $row['reference_no'];

        $flower_q = "SELECT * FROM flowers WHERE  flower_id='$flower_id'";
        $flower_result = mysqli_query($connection,$flower_q);
        $flower_name = mysqli_fetch_assoc($flower_result)['flower_name'];

        $img_query = "SELECT * FROM flower_images WHERE flower_id='$flower_id'";
        $img_result = mysqli_query($connection, $img_query);
        $dir_path = mysqli_fetch_assoc($img_result)['dir_path'];

        echo  "<tr>

               <td>
               <div class='sizeimg'>
               <img src='$dir_path' alt='flower image' width='80' height='80'><br>
               </div>
               <div class='sizetext'>
               <p><b>$flower_name</b></p> <br>
               </div>
               </td>
               
               <td>
               <p><b>$reference_no</b></p> 
               </td>
               
               <td>
               <div class='size'>
               <p><b>$quantity </b></p> 
               </div>
               </td>
               
                </tr>";

    }
}
else {
    echo "<tr><td colspan='3'><b>You have not placed Any Orders</b></td></tr>";
}

echo "        </tbody>
               </table>";

?>

</div><br><br><br>

<footer>
    <p>&copy; 2024 Flora Vista. All Rights Reserved.</p>
</footer><br>

</body>
</html>
