<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../Connection/connection.php";
include_once "../Function/function.php";

echo "<link rel='stylesheet' href='../style/cart.css?v=" . time() . "'>";

if (!isset($_SESSION['user']['islogin']) || $_SESSION['user']['islogin'] == false) {
    header("Location: ../login.php");
}

$user_id = $_SESSION['user']['user_id'];
$user_name = $_SESSION['user']['user_name'];

$total_items = "SELECT COUNT(*) AS total_items FROM shopping_cart WHERE user_id = '$user_id'";
$result_total_items = mysqli_query($connection, $total_items);

$num_of_items = mysqli_fetch_assoc($result_total_items)['total_items'];

if (isset($_POST['change_quantity'])) {
    $flower_id = $_POST['flower_id'];
    $user_quantity = $_POST['user_quantity'];

    $update = "UPDATE shopping_cart SET quantity='$user_quantity' WHERE user_id='$user_id' AND flower_id='$flower_id'";
    if (mysqli_query($connection, $update)) {
        header("Location: ./cart.php");
    }
}

if (isset($_POST['delete'])) {
    $flower_id = $_POST['flower_id'];

    $delete = "DELETE FROM shopping_cart WHERE user_id='$user_id' AND flower_id='$flower_id'";

    if (mysqli_query($connection, $delete)) {
        header("Location: ./cart.php");
    }
}

// show the cart items
$query = "SELECT * FROM shopping_cart WHERE user_id = '$user_id'";
$result = mysqli_query($connection, $query);

$total = 0;
$array = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart - Flora Vista</title>
    <link rel="stylesheet" href="../cart/cart.css?v=<?php echo time(); ?>">
</head>
<body>

<header>
    <nav>
        <ul>
            <li><a href="../index.php">Home</a></li>
            <li><a href="../shop/shop.html">Shop</a></li>
            <li><a href="../offers/offers.html">Special Offers</a></li>
            <li><a href="../new arrivals/arrivals.html">New Arrivals</a></li>
            <li><a href="../contact/contact.html">Contact Us</a></li>
            <li><a href="../subscription/subscription.html">Subscribe</a></li>
            <li><a href="../about/about.html">About Us</a></li>

        </ul>
    </nav>
</header>

<div class="container">
    <h1>&nbsp;My Cart</h1>

    <table>
        <thead>
        <tr>
            <th><h2 style="color: black">Item</h2></th>
            <th><h2 style="color: black">Quantity</h2></th>
            <th><h2 style="color: black">Price</h2></th>
            <th><h2 style="color: black">Total</h2></th>
            <th><h2 style="color: black">Remove Item</h2></th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $flower_id = $row['flower_id'];
                $user_quantity = $row['quantity'];

                $img_query = "SELECT * FROM flower_images WHERE flower_id='$flower_id'";
                $img_result = mysqli_query($connection, $img_query);
                $dir_path = mysqli_fetch_assoc($img_result)['dir_path'];

                $flower_query = "SELECT * FROM flowers  WHERE flower_id = '$flower_id'";
                $flower_result = mysqli_query($connection, $flower_query);
                $data = mysqli_fetch_assoc($flower_result);

                $flower_name = $data['flower_name'];
                $sale_price = $data['sale_price'];
                $max_quantity = $data['quantity'];

                $items_price = (float)$user_quantity * $sale_price;
                $discount = 0;

                $array[$flower_id] = $user_quantity;

                $today_discount = isset($data['today_discount']) ? $data['today_discount'] : 0;
                $today_discount_end = isset($data['today_discount_end']) ? $data['today_discount_end'] : '';

                if ($today_discount > 0 && date('Y-m-d') < $today_discount_end) {
                    $discount += ($items_price * $today_discount / 100);
                }

                $loyalty_discount = isset($data['loyalty_discount']) ? $data['loyalty_discount'] : 0;
                $loyalty_discount_end = isset($data['loyalty_discount_end']) ? $data['loyalty_discount_end'] : '';

                if (isset($_SESSION['user']['loyalty_id']) && $loyalty_discount > 0 && date('Y-m-d') < $loyalty_discount_end) {
                    $discount += ($items_price * $loyalty_discount / 100);
                }

                $price_off = isset($data['price_off']) ? $data['price_off'] : 0;
                $price_off_end = isset($data['price_off_end']) ? $data['price_off_end'] : '';

                if ($price_off > 0 && date('Y-m-d') < $price_off_end) {
                    $discount += ($items_price * $price_off / 100);
                }


                $items_total = $items_price - $discount;
                $total += $items_total;

                $sale_price = number_format($sale_price, 2);
                $items_total = number_format($items_total, 2);

                echo "<tr>
                            <td>
                                <img src='../$dir_path' alt='flower image' width='100' height='100'>
                                <p><b>$flower_name</b></p>
                            </td>
                            
                            <td>
                                <form action='cart.php' method='post'>
                                    <input type='number' name='user_quantity' min='1' max='$max_quantity' value='$user_quantity' required>
                                    <input type='hidden' name='flower_id' value='$flower_id'>
                                    <button type='submit' name='change_quantity'>Change</button>
                                </form>
                            </td>
                            
                            <td><b>Rs: $sale_price </b></td>
                            <td><b>Rs: $items_total </b></td>
                            
                            <td>    
                                    <form action='cart.php' method='post'>    
                                    <input type='hidden' name='flower_id' value='$flower_id'>
                                    <button type='submit' name='delete' class='delete-button'>Delete Item</button>   
                                    </form>    
                                                           
                            </td>
                            
                        </tr>";
            }
        } else {
            echo "<tr><td colspan='5'><b>Your Cart is Empty.</b></td></tr>";
        }
        ?>
        </tbody>
    </table>

    <div class="cart-summary">
        <h2>Cart Summary</h2>
        <p><b>Total: Rs: <?php echo number_format($total, 2); ?></b></p>
        <?php

        if ($num_of_items > 0):
            $_SESSION['payment'] = [
                'type' => 'shopping_cart',
                'total' => $total,
                'items' => $array,
                'user_id' => $user_id,
                'success' => false
            ];?>


            <form action="../payments/payment.php" method="get">
                <input type="text" name="address" placeholder="Enter your address" required>
                <button type="submit">Buy Now</button><br>
            </form>

            <br>
        <?php endif; ?>

    </div><br>
    <a href = '../index.php'><button type="submit">Back to Home</button><br><br></a>
</div><br><br><br>

<footer>
    <p>&copy; 2024 Flora Vista. All Rights Reserved.</p>
</footer><br>

</body>
</html>
