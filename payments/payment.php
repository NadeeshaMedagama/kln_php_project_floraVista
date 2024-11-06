<?php

global $connection;
session_start();

if (!isset($_SESSION['payment'])) {
    $_SESSION['payment'] = [];
}
if (!isset($_SESSION['payment']['success'])) {
    $_SESSION['payment']['success'] = false;
}
if (!isset($_SESSION['payment']['total'])) {
    $_SESSION['payment']['total'] = 0;
}

if (!isset($_SESSION['user']['points_balance'])) {
    $_SESSION['user']['points_balance'] = 0;
}

error_reporting(E_ALL);
ini_set('display_errors',1);

include_once "../Connection/connection.php";
include_once "../Function/function.php";

echo "<link rel='stylesheet' href='../style/payments/payment.css?v=". time()."'>";

if(!isset($_SESSION['user']['islogin']) ||  $_SESSION['user']['islogin'] == false){
    header("Location: ../index.php");
}

//    echo "<pre>";
//    print_r($_SESSION);
//    echo "</pre>";


$user_id = $_SESSION['user']['user_id'];
$user_name = $_SESSION['user']['user_name'];
$total =  $_SESSION['payment']['total'];

if(isset($_GET['address'])){
    $address = $_GET['address'];
    $_SESSION['payment']['address'] = $address;
}


if(isset($_POST['add']) && $_POST['points']>0){
    $points =(float) $_POST['points'];

    $_SESSION['payment']['total'] =  $_SESSION['payment']['total'] -  $points;
    $_SESSION['user']['points_balance'] = $_SESSION['user']['points_balance'] - ((int) $points);
//    $_SESSION['user']['points_balance'] =  $new_balance;
    $_SESSION['payment']['total'] =  $total;



    header("Location: ./payment.php");

}

if(isset($_SESSION['user']['loyalty_id']) &&  $_SESSION['user']['points_balance'] > 0 && $_SESSION['payment']['success'] == false){
    $points =  $_SESSION['user']['points_balance'];
    echo "<div class='back'>
        <a href = '../cart/cart.php'><button type='submit' class='backBtn'>Back </button></a>
          </div>
          
            <form action='payment.php' method='post'>
                <lable> <h2>Loyalty Points: </h2><h4>".$points."</h4></lable><br><br>";
    echo  "<lable> <h2>Enter Points: </h2></lable>";
    echo "<input type='number' id='points' name='points' min='0' max='$points' value='0' step='1' ><br>
                <div class='submit'>
                <button type='submit' name='add'>Add Points</button>
                </div> 
        <br><br>";

}

if(isset($_POST['pay_online'])){
    header("Location: ./paymentGateWay.php");
}

if(isset($_SESSION['payment']) && $_SESSION['payment']['success'] == false){
    $total =  $_SESSION['payment']['total'];

    if (isset($_SESSION['payment']['address'])) {
        $address = $_SESSION['payment']['address'];
    } else {
        $address = "No address provided";
    }


    echo "

            <form action='payment.php' method='post'>
                 <input type='hidden' name='total' value='$total'>
                 <lable> <h2>User Name :</h2> <h4>$user_name </h4></lable><br><br>
                 <lable> <h2>Total Price:</h2> <h4>". $_SESSION['payment']['total'] . "</h4></lable><br><br>
                 <lable> <h2>Shipping  Address:</h2> <h4>$address </h4></lable><br><br>
                 <div class='submit'>
                 <button type='submit' name='pay_online'>Pay Online</button>
                 </div>
        
               </form>";
}

if(isset($_SESSION['payment']['reference_no'])  && $_SESSION['payment']['success'] == true) {

    if (isset($_SESSION['payment']['address'])) {
        $address = $_SESSION['payment']['address'];
    } else {
        $address = "No address provided";
    }
//echo"$total";
    $reference_no = $_SESSION['payment']['reference_no'];
    $total = $_SESSION['payment']['total'];
    $current_points = $_SESSION['user']['points_balance'];


    $new_points = $current_points + (int)($total / 100);
    $_SESSION['user']['points_balance'] = $new_points;
    $query = "UPDATE loyalty_users SET points_balance = '$new_points' WHERE user_id = '$user_id'";
    mysqli_query($connection, $query);

    $querys = "INSERT  INTO payments (reference_no,user_id,amount,date,address) VALUES ('$reference_no','$user_id','$total',CURRENT_DATE,'$address')";
    mysqli_query($connection, $querys);

    if (isset($_SESSION['payment']['items']) && is_array($_SESSION['payment']['items'])) {
        foreach ($_SESSION['payment']['items'] as $flower_id => $quantity) {

            $update_flower_query = "UPDATE flowers SET quantity = quantity - $quantity WHERE flower_id = '$flower_id'";
            mysqli_query($connection, $update_flower_query);


            $delete_cart_query = "DELETE FROM shopping_cart WHERE user_id = $user_id AND flower_id = '$flower_id'";
            mysqli_query($connection, $delete_cart_query);


            $insert_delivery_query = "INSERT INTO delivery_items (flower_id, user_id, quantity, reference_no) 
                                  VALUES ('$flower_id', '$user_id', '$quantity', '$reference_no')";
            mysqli_query($connection, $insert_delivery_query);
        }
    } else {

        echo "<h2>No items found in the session to process.</h2>";
    }


    unset($_SESSION['payment']);

    echo "<div class='container'>
            <div class='success_payment'>
                    <h3> Your Payment has been Successfully Done. </h3> <br>
                    <lable>  <h2>Reference No: </h2>$reference_no </lable><br><br>
                    <lable>  <h2>Total Price: </h2>$total </lable><br><br>
                    <lable>  <h2>Shipping Address: </h2>$address </lable><br><br>
                    
                    <a href='../index.php'>                    
                    <button type='submit' name='home'> Go to home page </button>
                    </a><br>
              </div>
            </div>";
}

?>`
