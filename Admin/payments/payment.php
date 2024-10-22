<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once '../../Function/function.php';
include_once '../../Connection/connection.php';

// admin protection page
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

<div class='payments'>
    <h2>&nbsp;Payments List</h2>
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

                echo "<tr>
                        <td>$reference_no</td>
                        <td>$user_id</td>
                        <td>$user_name</td>
                        <td>$amount</td>
                        <td>$date</td>
                     </tr>";
            }
        }
        ?>
    </table>
</div>

</body>
</html>
