<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);

include_once __DIR__ . '/../Function/function.php';
include_once __DIR__ . '/../Connection/connection.php';

//echo "<pre>";
//var_dump($_SESSION);
//var_dump($_COOKIE);
//echo "</pre>";



if (!isset($_SESSION['admin']['islogin']) || $_SESSION['admin']['islogin'] != true ){


    header('Location: admin.php');

}

if (isset($_POST['logout'])) {

    session_unset();
    session_destroy();

    header("Location: admin_panel.php");

}

echo "<form action='admin_panel.php' method='post'>
            <button type='submit' name='logout'>Logout</button>
           </form>";

echo "<a href='flowers/flowers.php'>Flowers</a> 
          <a href='orders/orders.php'>Orders</a>
          <a href='suplier/suplier.php'>Supliers</a>
          <a href='payments/payment.php'>Payments Details</a>";


$sum_q = "SELECT  SUM(amount) AS amount FROM payments";
$result = mysqli_query($connection, $sum_q);

$sum = mysqli_fetch_assoc($result)['amount'];
echo "<h3>Tatal sale : $sum</h3>";

$query = "SELECT * FROM flowers WHERE quantity <20";
$result = mysqli_query($connection, $query);

if(mysqli_num_rows($result)>0){
    echo "<h3>Low quantity flowers</h3>
        
            <table border='1'>
                <tr>
                    <th>flower_id</th>
                    <th>flower_name</th>
                    <th>quantity</th>
                    ";

    while($row = mysqli_fetch_assoc($result)){
        $flower_id = $row['flower_id'];
        $flower_name = $row['flower_name'];
        $quantity = $row['quantity'];

        echo "<tr>
                        <td>$flower_id</td>
                        <td>$flower_name</td>
                        <td>$quantity</td>
                 </tr>";
    }

    echo "</table>";
}







?>