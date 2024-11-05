<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);

include_once '../../Function/function.php';
include_once '../../Connection/connection.php';

// admin protection page

if (!isset($_SESSION['admin']['islogin']) || $_SESSION['admin']['islogin'] != true){

    header("Location: ../admin.php");
}

if(isset($_POST['submit'])){
    $user_id = $_POST['user_id'];
    $loyalty_id = uniqid();

    $add_q = "INSERT INTO  loyalty_users (user_id, loyalty_id) VALUES ('$user_id', '$loyalty_id')";

    if(mysqli_query($connection,$add_q)){
        header("Location: ./customer.php");
    }
}

echo "<link rel='stylesheet' href='add_customer.css'>";

echo "<div class='container'>";

echo "<h1>Add Loyalty Customer</h1><br>";

echo "<div class='back'>
     <a href = '../payments/payment.php'><button type='submit' class='backBtn'>Back </button></a>
    </div>";

echo "<div class='remove'> 
      <a href = 'remove.php'><button type='submit' class='removeBtn'>Loyalty Customers Details</button>
      </a></div>";

echo "
            <table border='1'>
                <tr>
                    <th>User ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Add as Loyalty</th>
                </tr>";

$query = "SELECT  * FROM users WHERE user_id NOT IN (SELECT  user_id FROM loyalty_users)";
$result = mysqli_query($connection, $query);

while($row = mysqli_fetch_assoc($result)){
    $user_id =  $row['user_id'];
    $user_name =  $row['user_name'];
    $email =  $row['email'];
    $mobile =  $row['mobile'];


    echo " <tr> 
                    <td>$user_id</td>
                    <td>$user_name</td>
                    <td>$email</td>
                    <td>$mobile</td>
                    <td>
                        <form action='' method='post'>
                        <input type='hidden' name='user_id' value='$user_id'>
                        <button type='submit' name='submit'> Add</button>
                        </form>
                    </td>
               </tr>";
}

echo "</table><br>";

echo "<h2>Loyalty Customer Orders</h2>";

$query = "SELECT * FROM delivery_items WHERE user_id='$user_id'";
$result = mysqli_query($connection, $query);

if(mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $flower_id = $row['flower_id'];
        $quantity = $row['quantity'];
        $reference_no = $row['reference_no'];

        $flower_q = "SELECT * FROM flowers WHERE flower_id='$flower_id'";
        $flower_result = mysqli_query($connection, $flower_q);
        $flower_name = mysqli_fetch_assoc($flower_result)['flower_name'];

        echo "<div class='add'>
              <label>Order ID: $reference_no</label><br>
              <label>Flower Name: $flower_name</label><br> 
              <label>Quantity: $quantity</label><br><br>
              </div>";

    }
}

echo "</div>";

?>
