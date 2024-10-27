<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../../Connection/connection.php";
include_once "../../Function/function.php";

if(!isset($_SESSION['suplier']['islogin']) ||  $_SESSION['suplier']['islogin'] == false){
    header("location: ../login_suplier.php");
}

$suplier_id = $_SESSION['suplier']['suplier_id'];
$suplier_name = $_SESSION['suplier']['supplier_username'];

if(isset($_POST['accept_admin_order_request'])){

    $order_id = (int) $_POST['order_id'];
    $item_price = (float) user_input($_POST['item_price']);

    if (!isset($item_price)){
        echo "<script>window.alert('enter the item price')</script>";
    }
    $update_query = "UPDATE orders SET isAccept_suplier=true ,accept_suplier_date = CURRENT_DATE, purchase_price= '$item_price' WHERE order_id = '$order_id' ";

    if (mysqli_query($connection,$update_query)){
        header("Location: ./suplier_order.php");

    }else{
        logger("ERROR",  "Error update order status to accept order_id: $order_id");
    }
}

$query = "SELECT * FROM orders WHERE  suplier_id = '$suplier_id' AND isAccept_suplier=false";
$result = mysqli_query($connection, $query);

echo "<head><link rel='stylesheet' href='suppOrder.css'></head>";

echo "<div class='container'>
            <h1>Order Requests</h1>
            
            <div class='back'> <a href = '../suplier_panel.php'><button type='submit' class='backBtn'>Back </button></a></div>
            
            <table border='1'>
                <tr>
                    <th>Flower Name</th>
                    <th>Request Quantity</th>
                    <th>Admin Requested Date</th>
                    <th>Add Item Price</th>
                    <th></th>
                </tr>";

if(mysqli_num_rows($result)>0){
    while($row = mysqli_fetch_assoc($result)){
        $order_id = $row['order_id'];
        $flower_id = $row['flower_id'];
        $quantity = $row['quantity'];
        $requested_date = $row['order_date'];

        $retrieve_flower = "SELECT flower_name FROM flowers WHERE  flower_id = '$flower_id'";
        $retrieve_flower_result = mysqli_query($connection, $retrieve_flower);
        $flower_name =  mysqli_fetch_assoc($retrieve_flower_result)['flower_name'];

        echo "<tr>
                                <td>$flower_name</td>
                                <td>$quantity</td>
                                <td>$requested_date</td>
                                
                                    <form action='suplier_order.php' method='post'>
                                        <input type='hidden' name='order_id' value='$order_id'>
                                        <td>
                                            <input type='number' name='item_price' required>
                                        </td>
                                        <td>
                                            <button type='submit' name='accept_admin_order_request'>Deliver Order</button>
                                        </td>
                                        
                                    </form>
                                
                              </tr>";
    }
}


echo "</table>
    </div>";


?>
