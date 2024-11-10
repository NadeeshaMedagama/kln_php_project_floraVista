<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);


include_once "../../Connection/connection.php";

if (!isset($_SESSION['admin']['islogin']) && $_SESSION['admin']['islogin'] != true){
    header("Location: ../adminLogin.php");
}


$query = "SELECT * FROM delivery_items WHERE delivered_curior=true ";
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

$employe_names = [];

$emp_q = "SELECT * FROM employe";
$emp_r = mysqli_query($connection, $emp_q);
if($emp_r){
    while($row = mysqli_fetch_assoc($emp_r)){
        $employe_names[$row['employe_id']] = $row['employe_name'];
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="deliver.css">
</head>
<body>

<div class="container">
    <h1>Delivered Items</h1>

    <div class='back'>
        <a href = 'pending_items_delivery.php'><button type='submit' class='backBtn'>Back </button></a>
    </div>

    <div class='reg'>
        <a href = '../employe/employe_manage.php'><button type='submit' class='regBtn'>Create Employee </button></a>
    </div>

    <table border="1">
        <thead>
        <tr>
            <th colspan="9">User Details</th>
            <th colspan="3">Employee Details</th>
        </tr>

        <tr>
            <th>Reference No</th>
            <th>Order Date</th>
            <th>Flower ID</th>
            <th>Flower Name</th>
            <th>Quantity</th>
            <th>User ID</th>
            <th>User Name</th>
            <th>Mobile</th>
            <th>Address</th>
            <th>Employee ID</th>
            <th>Employee Name</th>
            <th>Packed Date</th>
        </tr>
        </thead>



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
            <td><?php echo $row['employe_id'] ?></td>
            <td><?php echo $employe_names[$row['employe_id']] ?></td>
            <td><?php echo $row['delivered_curior_date'] ?></td>
        <tr>
            <?php endwhile;
            endif; ?>
    </table>
</div>

</body>
</html>
