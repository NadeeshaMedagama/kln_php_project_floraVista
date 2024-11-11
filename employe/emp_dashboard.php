<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once "../Connection/connection.php";
include_once "../Function/function.php";

if(!isset($_SESSION['employe']['islogin']) || $_SESSION['employe']['islogin'] != true){
    header("Location: ./emp_login.php");
}

//echo "<pre>";
//print_r($_SESSION);
//echo "</pre>";


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
$user_mobile =[];

$retrieve_username_q ="SELECT * FROM users";
$username_r = mysqli_query($connection,$retrieve_username_q);
if($username_r){
    while($row = mysqli_fetch_assoc($username_r)){
        $usernames[$row['user_id']] = $row['user_name'];
        $user_mobile[$row['user_id']] = $row['mobile'];
    }
}

// submit the send button
if(isset($_POST['send'])){
    $reference_no = user_input($_POST['reference_no']);
    $flower_id = user_input($_POST['flower_id']);
    $employe_id = $_SESSION['employe']['employe_id'];

    $query = "UPDATE delivery_items SET employe_id='$employe_id' , delivered_curior=true ,delivered_curior_date=CURRENT_DATE 
                  WHERE reference_no='$reference_no' AND flower_id='$flower_id'";

    if(mysqli_query($connection,$query)){
        header("Location: ./emp_dashboard.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="dashboard.css">

</head>
<body>

<header>
    <lable class="name"><b>Employee Name : <?php echo $_SESSION['employe']['employe_name'] ?></b></lable>
    <button type="button" onclick="window.location.href='logout.php'">Log Out</button>
</header>

<div class="container">
    <h1>Courier Items</h1>
    <table border="1">
        <tr>
            <th>Reference No</th>
            <th>Order Date</th>
            <th>Flower ID</th>
            <th>Flower Name</th>
            <th>Quantity</th>
            <th>User ID</th>
            <th>User Name</th>
            <th>Address</th>
            <th>Mobile No</th>
            <th>Send to Courier</th>
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
            <td><?php echo $address[$row['reference_no']] ?></td>
            <td><?php echo $user_mobile[$row['user_id']] ?></td>
            <td>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    <input type="hidden" name="flower_id" value="<?php echo $row['flower_id'] ?>" >
                    <input type="hidden" name="reference_no" value="<?php echo $row['reference_no'] ?>" >
                    <button type="submit" name="send">SEND</button>
                </form>
            </td>
        <tr>
            <?php endwhile;
            endif; ?>
    </table>
</div><br><br><br>

<footer>
    <p>&copy; 2024 Flora Vista. All Rights Reserved.</p>
</footer><br>

</body>
</html>
