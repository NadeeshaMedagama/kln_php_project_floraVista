<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../Connection/connection.php";
include_once "../Function/function.php";

if(!isset($_SESSION['suplier']['islogin']) || $_SESSION['suplier']['islogin'] == false){
    header("Location: login_suplier.php");
}

$suplier_id = $_SESSION['suplier']['suplier_id'];
$suplier_username = $_SESSION['suplier']['suplier_username'];
$suplier_email = $_SESSION['suplier']['email'];
$mobile = $_SESSION['suplier']['mobile'];

echo "<a href='orders/suplier_order.php'>Request Orders</a>";


if(isset($_POST['submit'])){
    $suplier_id = $_POST['suplier_id'];
    $suplier_username = $_POST['username'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];

    $_SESSION['suplier']['suplier_username'] = $suplier_username;
    $_SESSION['suplier']['email'] = $email;
    $_SESSION['suplier']['mobile'] = $mobile;

    $query = "UPDATE supliers SET suplier_username='$suplier_username', email='$email', mobile='$mobile' WHERE  suplier_id='$suplier_id'";
    if(mysqli_query($connection,$query)){
        header("Location: ./suplier_panel.php");
    }
}

if (isset($_POST['logout'])) {

    session_unset();
    session_destroy();

    header("Location: login_suplier.php");

}

echo "<form action='suplier_panel.php' method='post'>
            <button type='submit' name='logout'>Logout</button>
           </form>";

echo "<h4>User Information</h4>";

echo "<form action='suplier_panel.php' method='post'>
            <input type='hidden' name='user_id'  value='$suplier_id'>
            <lable>Username : </lable> <br>
            <input type='text' name='username' value='$suplier_username' required>
            <br><br>
            <lable>Email : </lable> <br>
            <input type='email' name='email' value='$suplier_email' required>
            <br><br>
            <lable>Mobile : </lable> <br>
            <input type='text' name='mobile' value='$mobile' required><br><br>
            <button type='submit' name='submit'> Update</button>
    
          </form>";




?>