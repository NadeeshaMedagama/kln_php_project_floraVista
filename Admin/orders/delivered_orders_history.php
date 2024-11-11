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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accepted Delivered Orders</title>
    <link rel="stylesheet" href="deliverOrdersHistory.css">
</head>
<body>

<div id="container">
    <h1>Accepted Delivered Orders History</h1>

    <div class='back'>
        <a href = 'delivered_orders.php'><button type='submit' class='backBtn'>Back </button></a>
    </div>

    <?php

$query = "SELECT * FROM orders  WHERE isAccept_supplier=true  AND isDelivered=true ORDER BY delivered_date DESC";

$result = mysqli_query($connection,$query);

echo "<table border='1'>
                    <tr>
                        <th>Order ID</th>
                        <th>Order Date</th>
                        <th>Flower ID</th>
                        <th>Flower Name</th>
                        <th>Suplier ID</th>
                        <th>Suplier Name</th>
                        <th>Quantity</th>
                        <th>Item Price</th>
                        <th>Sale Price</th>
                        <th>Total Price</th>
                        <th>Delivered Date</th>
                        
                    </tr>";

if(mysqli_num_rows($result)>0){
    while($row = mysqli_fetch_assoc($result)){
        $order_id = $row['order_id'];
        $order_date = $row['order_date'];
        $flower_id = $row['flower_id'];
        $quantity = (int)$row ['quantity'];
        $suplier_id = $row['suplier_id'];
        $item_price = (float)$row['purchase_price'];
        $flower_sale_price = $row['order_sale_price'];
        $delivered_date = $row['delivered_date'];
        $total_price = (float) ($quantity *  $item_price);

        $retrieve_flower = "SELECT * FROM  flowers WHERE flower_id = '$flower_id' LIMIT 1";
        $retrieve_flower_result = mysqli_query($connection,$retrieve_flower);

        $retrieve_suplier = "SELECT * FROM   supliers WHERE suplier_id = '$suplier_id' LIMIT 1";
        $retrieve_suplier_result = mysqli_query($connection,$retrieve_suplier);

        $flower_data= mysqli_fetch_assoc($retrieve_flower_result);
        $flower_name = $flower_data['flower_name'];

        $suplier_name = mysqli_fetch_assoc($retrieve_suplier_result)['supplier_username'];

        $item_price = number_format($item_price, 2);
        $flower_sale_price = number_format($flower_sale_price, 2);
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
                                    
                                    <td>Rs: $flower_sale_price</td>
                                    <td>Rs: $total_price</td>
                                    <td>$delivered_date</td>
                                        
                                    
                                </tr>";
    }
}


echo "</table>
    </div>";


?>
