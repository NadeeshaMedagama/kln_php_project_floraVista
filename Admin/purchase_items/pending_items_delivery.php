<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);


include_once "../../Connection/connection.php";

if (!isset($_SESSION['admin']['islogin']) && $_SESSION['admin']['islogin'] != true){
    header("Location: ../adminLogin.php");
}


$query = "SELECT * FROM delivery_items WHERE delivered_curior=false ";
$result = mysqli_query($connection, $query);

$flowers = [];

$retrieve_f_q = "SELECT * FROM flowers";
$retrieve_f_result = mysqli_query($connection, $retrieve_f_q);

if($retrieve_f_result){
    while($row = mysqli_fetch_assoc($retrieve_f_result)){
        $flowers[$row['flower_id']] = $row['flower_name'];
    }
}

$address = [];

$retrieve_a_q = "SELECT * FROM payments";
$retrieve_a_result = mysqli_query($connection, $retrieve_a_q);

if($retrieve_a_result){
    while($row = mysqli_fetch_assoc($retrieve_a_result)){
        $address[$row['reference_no']] = $row['address'];
    }
}

$usernames = [];
$user_mobile = [];

$retrieve_username_q ="SELECT * FROM users";
$username_r = mysqli_query($connection,$retrieve_username_q);
if($username_r){
    while($row = mysqli_fetch_assoc($username_r)){
        $usernames[$row['user_id']] = $row['user_name'];
        $user_mobile[$row['user_id']] = $row['mobile'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="pending.css">
</head>

<body>

<div class="container">
    <h1>Pending to Deliver Items</h1>

    <div class='back'>
        <a href = '../orders/orders.php'><button type='submit' class='backBtn'>Back </button></a>
    </div>

    <div class='reg'>
        <a href = 'delivered_itmes.php'><button type='submit' class='regBtn'>Delivered Items </button></a>
    </div>

    <table border="1">
        <tr>
            <th>reference no</th>
            <th>order date</th>
            <th>flower id</th>
            <th>flower name</th>
            <th>quantity</th>
            <th>user id</th>
            <th>user name</th>
            <th>mobile</th>
            <th>address</th>
        </tr>


        <?php if(mysqli_num_rows($result) > 0):
        while( $row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['reference_no'] ?></td>
            <td><?php echo $row['order_date'] ?></td>
            <td><?php echo $row['flower_id'] ?></td>
            <td><?php echo $flowers[$row['flower_id']] ?></td>
            <td><?php echo $row['quantity'] ?></td>
            <td><?php echo $row['user_id'] ?></td>
            <td><?php echo $usernames[$row['user_id']] ?></td>
            <td><?php echo $user_mobile[$row['user_id']] ?></td>
            <td><?php echo $address[$row['reference_no']] ?></td>
        <tr>
            <?php endwhile;
            endif; ?>
    </table>


</div>


</body>
</html>
