<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../../Connection/connection.php";
include_once "../../Function/function.php";

if(!isset($_SESSION['admin']['islogin']) || $_SESSION['admin']['islogin'] ==false){
    header("Location: ../admin.php");
}

if (isset($_POST['delete'])){
    $order_id = user_input($_POST['order_id']);

    $delete_query = "DELETE FROM  orders WHERE order_id = '$order_id'";

    if(mysqli_query($connection,$delete_query)){
        header("Location: ./delivered_orders.php");
    }else{
        logger("ERROR",  "Failed to delete order with id $order_id");
    }
}

if  (isset($_POST['delivered'])){
    $order_id = user_input($_POST['order_id']);
    $flower_id = user_input($_POST['flower_id']);
    $quantity = user_input($_POST['quantity']);
    $sale_price =  user_input($_POST['sale_price']);


    $update_orders = "UPDATE orders SET isDelivered=true, delivered_date = CURRENT_DATE, order_sale_price='$sale_price' WHERE order_id ='$order_id' ";

    //update the flowers table
    $update_flowers = "UPDATE flowers SET quantity = quantity + '$quantity' ,  sale_price = '$sale_price' WHERE flower_id = '$flower_id' ";

    if(mysqli_query($connection,$update_orders) && mysqli_query($connection,$update_flowers)){
        header("Location: ./delivered_orders.php");
    }else{
        logger("ERROR",  "Failed to update order with id $order_id");
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivered Orders</title>
    <link rel="stylesheet" href="deliverOrders.css">
</head>
<body>

<div id="container">
    <h1>To Accept Delivered Orders</h1>

    <div class='back'>
        <a href = 'orders.php'><button type='submit' class='backBtn'>Back </button></a>
    </div>

    <div class='reg'>
        <a href = 'delivered_orders_history.php'><button type='submit' class='regBtn'>Supplier Delivered Orders History </button></a>
    </div>

    <?php
$query = "SELECT * FROM orders WHERE isAccept_supplier=true  AND isDelivered=false";

$result = mysqli_query($connection,$query);

echo "<table border='1'>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Flower ID</th>
                        <th>Flower Name</th>
                        <th>Supplier ID</th>
                        <th>Supplier Name</th>
                        <th>Quantity</th>
                        <th>Item Price</th>
                        <th>Sale Price</th>
                        <th>Total Price</th>
                        <th>Delivered or Not</th>
                        <th>Delete Order</th>
                        
                    </tr>";

if(mysqli_num_rows($result)>0){
    while($row = mysqli_fetch_assoc($result)){
        $order_id = $row['order_id'];
        $order_date = $row['order_date'];
        $flower_id = $row['flower_id'];
        $quantity = (int)$row ['quantity'];
        $suplier_id = $row['suplier_id'];
        $item_price = (float)$row['purchase_price'];

        $total_price = (float) ($quantity *  $item_price);

        $retrieve_flower = "SELECT * FROM  flowers WHERE flower_id = '$flower_id' LIMIT 1";
        $retrieve_flower_result = mysqli_query($connection,$retrieve_flower);

        $retrieve_suplier = "SELECT * FROM   supliers WHERE suplier_id = '$suplier_id' LIMIT 1";
        $retrieve_suplier_result = mysqli_query($connection,$retrieve_suplier);

        $flower_data= mysqli_fetch_assoc($retrieve_flower_result);
        $flower_name = $flower_data['flower_name'];
        $flower_sale_price =$flower_data['sale_price'];
        $suplier_name = mysqli_fetch_assoc($retrieve_suplier_result)['supplier_username'];

        $item_price = number_format($item_price, 2);
        $total_price = number_format($total_price, 2);

        echo "<tr>
                                    <td>$order_id</td>
                                    <td>$order_date</td>
                                    <td>$flower_id</td>
                                    <td>$flower_name</td>
                                    <td>$suplier_id</td>
                                    <td>$suplier_name</td>
                                    <td>$quantity</td>
                                    <td>Rs: $item_price</td>
                                    <form action='delivered_orders.php' method='post'>
                                        <td><input type='number' name='sale_price' value='$flower_sale_price'></td>
                                        <td>Rs: $total_price</td>
                                        <td>
                                            <input type='hidden' name='order_id' value='$order_id'>
                                            <input type='hidden' name='flower_id' value='$flower_id'>
                                            <input type='hidden' name='quantity' value='$quantity'>
                                            <button type='submit' name='delivered'>Delivered</button>
                                        </td>
                                    </form>
                                    <td>
                                        <form action='delivered_orders.php' method='post'>
                                            <input type='hidden' name='order_id' value='$order_id'>
                                            <div class='delete'>
                                            <button type='submit' name='delete' class='deleteBtn'>Delete</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>";
    }
}


echo "</table>
    </div>";


?>
