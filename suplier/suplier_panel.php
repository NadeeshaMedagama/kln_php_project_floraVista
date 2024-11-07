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
$suplier_username = $_SESSION['suplier']['supplier_username'];
$suplier_email = $_SESSION['suplier']['email'];
$mobile = $_SESSION['suplier']['mobile'];

echo "<div class='req'> 
        <a href='orders/suplier_order.php'>
        <button type='submit' class='order'>Request Orders </button>
        </a></div>";


if(isset($_POST['submit'])){
    $suplier_id = $_POST['suplier_id'];
    $suplier_username = $_POST['username'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];

    $_SESSION['suplier']['supplier_username'] = $suplier_username;
    $_SESSION['suplier']['email'] = $email;
    $_SESSION['suplier']['mobile'] = $mobile;

    $query = "UPDATE supliers SET supplier_username='$suplier_username', email='$email', mobile='$mobile' WHERE  suplier_id='$suplier_id'";
    if(mysqli_query($connection,$query)){
        header("Location: ./suplier_panel.php");
    }echo "<div class='success'>Updated Successfully!</div>";
}

if (isset($_POST['logout'])) {

    session_unset();
    session_destroy();

    header("Location: login_suplier.php");

}

echo"<div class='container'>";

echo "<head><link rel='stylesheet' href='panel.css'></head>";


echo "<form action='suplier_panel.php' method='post'>
            <div class='log'> <button type='submit' class='logout'>Logout</button></div>
           </form>";

echo "<h1>Supplier Panel</h1><br>";

echo "<h2>User Information</h2>";

echo "<form action='suplier_panel.php' method='post'>
            <input type='hidden' name='suplier_id'  value='$suplier_id'>
            <lable><b>Username : </b></lable> <br>
            <input type='text' name='username' value='$suplier_username' required>
            <br><br>
            <lable><b>Email : </b></lable> <br>
            <input type='email' name='email' value='$suplier_email' required>
            <br><br>
            <lable><b>Mobile : </b></lable> <br>
            <input type='text' name='mobile' value='$mobile' required><br><br>         
            <button type='submit' name='submit' class='updateBtn'> Update</button>
            
    
          </form></div>";




?>