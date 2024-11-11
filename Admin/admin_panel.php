<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);

include_once __DIR__ . '/../Function/function.php';
include_once __DIR__ . '/../Connection/connection.php';

if (!isset($_SESSION['admin']['islogin']) || $_SESSION['admin']['islogin'] != true) {
    header('Location: admin.php');
}

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: admin_panel.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>

    <link rel="stylesheet" href="panel.css">
</head>
<body>

<div class="container">

    <h1>Admin Panel</h1>

    <div class="nav-links">
        <a href='flowers/flowers.php'>Flowers</a>
        <a href='orders/orders.php'>Orders</a>
        <a href='suplier/suplier.php'>Suppliers</a>
        <a href='payments/payment.php'>Payments Details</a>
    </div>

    <h4>Total Sale : Rs.
        <?php

        $sum_q = "SELECT SUM(amount) AS amount FROM payments";
        $result = mysqli_query($connection, $sum_q);
        $sum = mysqli_fetch_assoc($result)['amount'];
        echo number_format($sum, 2);
        ?>
    </h4>

    <?php

    $query = "SELECT * FROM flowers WHERE quantity <20";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "<h2>Low Quantity Flowers :</h2>
            <table>
                <tr>
                    <th>Flower ID</th>
                    <th>Flower Name</th>
                    <th>Quantity</th>
                </tr>";

        while ($row = mysqli_fetch_assoc($result)) {
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

    <form action='admin_panel.php' method='post' class="logout-form">
        <button type='submit' name='logout'>Logout</button>
    </form>

</div><br><br><br>

<footer>
    <p>&copy; 2024 Flora Vista. All Rights Reserved.</p>
</footer><br>

</body>
</html>
