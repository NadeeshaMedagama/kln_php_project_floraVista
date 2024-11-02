<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once "../../Connection/connection.php";
include_once "../../Function/function.php";

if(! isset($_SESSION["admin"]["islogin"]) || $_SESSION["admin"]["islogin"] != true) {
    header("Location: ../admin.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management</title>
    <link rel="stylesheet" href="orders.css"> <!-- Adjust the path if needed -->
</head>
<body>

<?php

echo "
<div class='button-container'>
    <a class='button' href='delivered_orders.php'>Supplier Delivered Orders</a>
    <a class='button' href='delivered_orders_history.php'>Supplier Delivered Orders History</a>
    
    <div class='back'> 
    <a href = '../admin_panel.php'><button type='submit' class='backBtn'>Back </button></a>
    </div>

</div>
";

if (isset($_POST['request_order'])){
    $flower_id = user_input($_POST['flower_id']);
    $quantity = (int) user_input($_POST['quantity']);
    $suplier_id =  user_input($_POST['suplier_id']);

    $insert_query = "INSERT INTO orders(flower_id,order_date,quantity,suplier_id) VALUES  ('$flower_id',CURRENT_DATE,$quantity,'$suplier_id')";

    if(mysqli_query($connection,$insert_query)){
        header("Location: ./orders.php");
    }else{
        logger("ERROR", "orders table data insert fail");
    }
}

// delete order

if(isset($_POST['delete_order'])){
    $order_id = $_POST['order_id'];

    $delete_query = "DELETE FROM orders WHERE order_id = '$order_id'";

    if(mysqli_query($connection,$delete_query)){
        header("Location: ./orders.php");
    }else{
        logger("ERROR", "orders table data delete order $order_id fail");
    }
}

echo "<h2> Request Order To Suplier </h2>";

echo "<div>
        <form action='orders.php' method='post'>
            <label>Select a flower Name: </label> &nbsp;
            <select name='flower_id' required>";
//get the flower names
$query = "SELECT * FROM flowers";
$result_set = mysqli_query($connection, $query);

if(mysqli_num_rows($result_set) > 0){
    while($row = mysqli_fetch_assoc($result_set)){

        $flower_id = $row['flower_id'];
        $flower_name = $row['flower_name'];

        echo "<option value='$flower_id'>$flower_name</option>";
    }
}
echo "</select> &nbsp;&nbsp;
                <label>Select Supplier: </label> &nbsp;
                <select name='suplier_id' required>";

$query = "SELECT * FROM supliers WHERE verify=true";
$result_set = mysqli_query($connection, $query);

if(mysqli_num_rows($result_set) > 0){
    while($row = mysqli_fetch_assoc($result_set)){
        $suplier_id = $row['suplier_id'];
        $suplier_name = $row['supplier_username'];

        echo "<option value='$suplier_id'>$suplier_name</option>";
    }
}

echo "</select> &nbsp;&nbsp;
                <input type='number'  name='quantity' placeholder='Quantity' min='1' required/> &nbsp;&nbsp;
                <div class='request'><button type='submit' name='request_order'> Request order</button></div> 
                </form>
                </div>";


// show pending orders

$query = "SELECT order_id, flower_id, quantity, order_date, suplier_id
                  FROM orders
                  WHERE orders.isAccept_supplier = false";

$result_set = mysqli_query($connection,$query);

echo "<div id='pending_orders'>
                <h2>Pending Order Requests</h2>
                <table border='1'>
                    <tr>
                        <th>Order ID</th>
                        <th>Flower Name</th>
                        <th>Quantity</th>
                        <th>Order Date</th>
                        <th>Supplier</th>
                        <th></th>
                    
                    </tr>";
if(mysqli_num_rows($result_set)>0){
    while($row = mysqli_fetch_assoc($result_set)){
        $order_id = $row['order_id'];
        $flower_id = $row['flower_id'];
        $quantity = $row['quantity'];
        $order_date = $row['order_date'];
        $suplier_id = $row['suplier_id'];

        $retrieve_flower = "SELECT flower_name FROM  flowers WHERE flower_id = '$flower_id'";
        $result_set_flower = mysqli_query($connection, $retrieve_flower);

        $retrieve_suplier = "SELECT supplier_username FROM   supliers WHERE suplier_id = '$suplier_id'";
        $result_set_suplier = mysqli_query($connection, $retrieve_suplier);

        $flower_name = mysqli_fetch_assoc($result_set_flower)['flower_name'];
        $suplier_name = mysqli_fetch_assoc($result_set_suplier)['supplier_username'];

        echo "
                <tr>
                    <td>$order_id</td>
                    <td>$flower_name</td>
                    <td>$quantity</td>
                    <td>$order_date</td>
                    <td>$suplier_name</td>
                    <td>
                        <form action='orders.php' method='post'>
                        <input type='hidden' name='order_id' value='$order_id'>
                        <button type='submit' name='delete_order'>Delete Order</button>
                        </form>
                    </td>
                </tr>
                ";
    }
}
echo "</table>
        </div>";




?>