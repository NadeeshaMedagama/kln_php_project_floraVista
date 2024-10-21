<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../../Connection/connection.php";
include_once "../../Function/function.php";

if (!isset($_SESSION['admin']['islogin']) || $_SESSION['admin']['islogin'] != true) {
    header("Location: ../admin.php");
}

$query = "SELECT * FROM supliers WHERE verify=true";
$result = mysqli_query($connection, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Suppliers</title>
    <link rel="stylesheet" href="supplierDetails.css"> <!-- Update the path as needed -->
</head>
<body>

<div class='container'>
    <h1>Registered Suppliers Details</h1><br>

    <table border='1'>
        <tr>
            <th>Supplier ID</th>
            <th>Supplier Name</th>
            <th>Supplier Email</th>
            <th>Supplier Mobile</th>
        </tr>

        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $supplier_id = $row['suplier_id'];
                $supplier_name = $row['supplier_username'];
                $email = $row['email'];
                $mobile = $row['mobile'];

                echo "<tr>
                        <td>$supplier_id</td>
                        <td>$supplier_name</td>
                        <td>$email</td>
                        <td>$mobile</td>
                      </tr>";
            }
        }
        ?>

    </table>
</div>

</body>
</html>
