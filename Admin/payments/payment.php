<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once '../../Function/function.php';
include_once '../../Connection/connection.php';


if (!isset($_SESSION['admin']['islogin']) || $_SESSION['admin']['islogin'] != true) {
    header("Location: ../admin.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments</title>
    <link rel="stylesheet" href="payment.css">
</head>
<body>

<div class='container'>
    <h1>&nbsp;Payments List :</h1>

    <div class='loyal'> <a href = '../loyalty_customer/add_customer.php'><button type='submit' class='add'>Add Loyalty Customer</button></a></div>

    <div class='back'> <a href = '../admin_panel.php'><button type='submit' class='backBtn'>Back </button></a></div>

    <table>
        <tr>
            <th>Reference No</th>
            <th>User ID</th>
            <th>Username</th>
            <th>Amount</th>
            <th>Date</th>
        </tr>

        <?php
        $query = "SELECT * FROM payments ORDER BY DATE DESC";
        $result = mysqli_query($connection, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $reference_no = $row['reference_no'];
                $amount = $row['amount'];
                $user_id = $row['user_id'];
                $date = $row['date'];

                $user_q = "SELECT * FROM users WHERE user_id = '$user_id'";
                $user_result = mysqli_query($connection, $user_q);
                $user_name = mysqli_fetch_assoc($user_result)['user_name'];
                $amount = number_format($amount, 2);

                echo "<tr>
                        <td>$reference_no</td>
                        <td>$user_id</td>
                        <td>$user_name</td>
                        <td>Rs: $amount</td>
                        <td>$date</td>
                     </tr>";
            }
        }
        ?>
    </table>
</div>

</body>
</html>
