<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../Connection/connection.php";
include_once "../Function/function.php";

echo "<link rel='stylesheet' href='flowers.css?v=" . time() . "'>";


if(isset($_POST['add_cart'])){
    if(!isset($_SESSION['user']['islogin']) ||  $_SESSION['user']['islogin'] == false){
        header("Location:  ../login.php");
        exit;
    }else{
        $user_id = $_SESSION['user']['user_id'];
        $flower_id =  $_POST['flower_id'];
        $quantity =  $_POST['quantity'];

        $check_query = "SELECT * FROM shopping_cart WHERE  user_id = '$user_id' AND flower_id = '$flower_id'";
        $check_result = mysqli_query($connection, $check_query);

        if(mysqli_num_rows($check_result)>0){
            $query = "UPDATE shopping_cart SET quantity = quantity + '$quantity' WHERE user_id ='$user_id' AND  flower_id = '$flower_id'";
        }else{
            $query = "INSERT INTO shopping_cart(user_id,flower_id,quantity) VALUES ('$user_id','$flower_id','$quantity')";
        }

        if(mysqli_query($connection,$query)){
            header("Location: ../index.php");
        }
    }
}

if(isset($_POST['buy_now'])){
//    if(!isset($_SESSION['user']['islogin']) ||  $_SESSION['user']['islogin'])
      if(!isset($_SESSION['user']['islogin']) || $_SESSION['user']['islogin'] == false){

        header("Location:  ../login.php");
    }

//    if(!isset($address) || $address ==''){
//        echo "<script>window.alert('Enter the Shipping Address')</script>";
//        return;
//    }

    $array = [];
    $user_id = $_SESSION['user']['user_id'];
    $flower_id =  $_POST['flower_id'];
    $quantity =  $_POST['quantity'];
    $sale_price = $_POST['sale_price'];
    $address = $_POST['address'];

    if (empty($address)) {
        echo "<script>window.alert('Enter the shipping address')</script>";
        exit;
    }

    $flower_query = "SELECT * FROM flower_discounts WHERE  flower_id = '$flower_id'";
    $flower_result = mysqli_query($connection, $flower_query);
    $data =  mysqli_fetch_assoc($flower_result);


//    $today_discount =  $data['today_discount'];
//    $loyalty_discount =  $data['loyalty_discount'];
//    $price_off = $data['price_off'];
//    $today_discount_end = $data['today_discount_end'];
//    $loyalty_discount_end = $data['loyalty_discount_end'];
//    $price_off_end = $data['price_off_end'];

    $dir_path = isset($row1['dir_path']) ? $row1['dir_path'] : '';
    $today_discount = isset($row2['today_discount']) ? $row2['today_discount'] : 0;
    $loyalty_discount = isset($row2['loyalty_discount']) ? $row2['loyalty_discount'] : 0;
    $price_off = isset($row2['price_off']) ? $row2['price_off'] : 0;
    $today_discount_end = isset($row2['today_discount_end']) ? $row2['today_discount_end'] : '';
    $loyalty_discount_end = isset($row2['loyalty_discount_end']) ? $row2['loyalty_discount_end'] : '';
    $price_off_end = isset($row2['price_off_end']) ? $row2['price_off_end'] : '';

    $items_price = (float) $quantity * $sale_price;

    $discount = 0;

    if (isset($today_discount) && date('Y-m-d') < $today_discount_end) {
        $discount = $discount -  ($items_price * $today_discount / 100);
    }
    if (isset($_SESSION['user']['loyalty_id'])){
        if (isset($loyalty_discount) && date('Y-m-d') < $loyalty_discount_end) {
//            $discount = $discount -  ($items_price * $loyalty_discount/ 100);
            echo "<p class='loyalty-discount'>Loyalty Discount: $loyalty_discount%</p>";
        }
    }
    if (isset($price_off) && date('Y-m-d') < $price_off_end) {
        $discount = $discount -  ($items_price * $price_off / 100);
    }
    $total = $items_price - $discount;

    $array[$flower_id] = $quantity;

    $_SESSION['payment'] = [
        'type' => 'item',
        'total' => $total,
        'items' => $array,
        'user_id' => $user_id,
        'success' => false
    ];
    header("Location: ../payments/payment.php?address=$address");
    exit;
}

if (isset($_POST['add_comment'])){
    if(!isset($_SESSION['user']['islogin']) ||  $_SESSION['user']['islogin'] == false){
        header("Location:  ../login.php");
    }else{
        $user_id = $_SESSION['user']['user_id'];
        $flower_id =  $_POST['flower_id'];
        $comment =  $_POST['comment'];


        $insert_comment = "INSERT  INTO comments(user_id,flower_id,comment) VALUES ('$user_id','$flower_id','$comment')";
        if(mysqli_query($connection,$insert_comment)){
            header("Location: ./flowers.php?flower_id=$flower_id");
        }
    }
}


if(isset($_GET['flower_id'])){
    $flower_id = user_input($_GET['flower_id']);

//    $query = "SELECT f.flower_id, f.flower_name, f.quantity, f.sale_price, f.description, fi.dir_path,
//                  fd.today_discount, fd.loyalty_discount, fd.price_off, fd.today_discount_end,
//                  fd.loyalty_discount_end, fd.price_off_end
//                  FROM flowers AS f
//                  INNER JOIN flower_images AS fi ON f.flower_id = fi.flower_id
//                  INNER JOIN flower_discounts AS fd ON f.flower_id = fd.flower_id
//                  WHERE f.flower_id = '$flower_id'";


    $query = "select * from flowers where flower_id = '$flower_id'";
    $query1 = "select * from flower_images WHERE flower_id='$flower_id'";
    $query2 = "select * from flower_discounts WHERE flower_id='$flower_id'";
    $result = mysqli_query($connection,$query);
    $result1 = mysqli_query($connection,$query1);
    $result2 = mysqli_query($connection,$query2);
    $row = mysqli_fetch_assoc($result);
    $row1 = mysqli_fetch_assoc($result1);
    $row2 = mysqli_fetch_assoc($result2);

        $flower_id = $row['flower_id'];
        $flower_name = $row['flower_name'];
        $sale_price = $row['sale_price'];
        $quantity = $row['quantity'];
        $description = $row['description'];
        $dir_path = $row1['dir_path'];
        $today_discount = $row2['today_discount'] ?? 0;
        $loyalty_discount = $row2['loyalty_discount'] ?? 0;
        $price_off = $row2['price_off'] ?? 0;
        $today_discount_end = $row2['today_discount_end'] ?? 0;
        $loyalty_discount_end = $row2['loyalty_discount_end'] ?? 0;
        $price_off_end = $row2['price_off_end'] ?? 0;

    $sale_price = number_format($sale_price, 2);

//    }else {
//        echo "<p>Flowers not found!</p>";
//        exit;
//    }

//    $row = mysqli_fetch_assoc($result);
//
//    $flower_id = $row['flower_id'];
//    $flower_name = $row['flower_name'];
//    $sale_price = $row['sale_price'];
//    $quantity = $row['quantity'];
//    $description = $row['description'];
//    $dir_path = $row['dir_path'];
//
//    $today_discount =  $row['today_discount'];
//    $loyalty_discount =  $row['loyalty_discount'];
//    $price_off = $row['price_off'];
//    $today_discount_end = $row['today_discount_end'];
//    $loyalty_discount_end = $row['loyalty_discount_end'];
//    $price_off_end = $row['price_off_end'];

    echo "<header>
    <nav>
        <ul>
        
            <li><a href='../index.php'>Home</a></li>
            <li><a href='../shop/shop.html'>Shop</a></li>
            <li><a href='../offers/offers.html'>Special Offers</a></li>
            <li><a href='../new arrivals/arrivals.html'>New Arrivals</a></li>
            <li><a href='../contact/contact.html'>Contact Us</a></li>
            <li><a href='../about/about.html'>About Us</a></li>
            
            <div class='header-info'>
                <span class='cart'>
                <a href='../cart/cart.php'><img src='../Admin/home/style/images/cart.png' width='28px' height='28px' alt='cart'><br>Cart</a>
                </span>
            </div>
            
        </ul>
    </nav>
</header>

            <div class='container'>
                <div class='image-container'>
                    <img src='../$dir_path' alt='$flower_name'>
                </div>
                
     <!--           <div class='back'> 
                    <a href = '../index.php'><button type='submit' class='backBtn'>Back </button></a>
                </div>  -->
    
                <div class='flower-details'>
                    <h2>$flower_name</h2>
                    <p class='description'><b>Description : </b>$description</p>
                    <p class='price'><b>Price: </b> RS. $sale_price</p>
                    <p class='quantity'><b>Available Quantity: </b> $quantity</p>";

    if (isset($today_discount) && date('Y-m-d') < $today_discount_end) {
        echo "<p class='discount'>Today's Discount: $today_discount%</p>";
    }
    if (isset($loyalty_discount) && date('Y-m-d') < $loyalty_discount_end) {
        echo "<p class='loyalty-discount'>Loyalty Discount: $loyalty_discount%</p>";
    }
    if (isset($price_off) && date('Y-m-d') < $price_off_end) {
        echo "<p class='price-off'>Price Off: $price_off%</p>";
    }

    if($quantity >0){
        echo "
                            <div class='button-container'>
                                <form action='flowers.php'  method='post'>
                                <input type='hidden' name='flower_id' value='$flower_id'>
                                <input type='hidden' name='sale_price' value='$sale_price'>
                                <label><b>Quantity :  </b></label>
                                <input type='number' id='quantity' name='quantity' min='1' max='$quantity' value='1' step='1' style='cursor: pointer' required><br><br>
                                
                                <textarea name='address'  class= 'address' placeholder='Enter your address'></textarea><br><br>
                                
                                <button  type='submit'  name='add_cart' class='add-to-cart'>Add to Cart</button>
                                <button name='buy_now' class='buy-now'>Buy Now</button>
                                </form>
                            </div>";
    }else{
        echo "<p style='color: red; font-weight: bold;'>Out of Stock</p>";
    }



    echo "
                </div>
            </div>";


    echo "<div class='comment'>
                    <form action='' method='post'>
                        <input type='hidden' name='flower_id' value='$flower_id'>
                        <textarea name='comment' placeholder='Write your comment here...' required></textarea>
                        <button type='submit' name='add_comment' class='add-comment'>Add Comment</button><br>
                    </form>
            ";

    // show comments
    $query = "SELECT * FROM comments WHERE flower_id = '$flower_id'";
    $result = mysqli_query($connection, $query);

    if(mysqli_num_rows($result)>0){
        while($row = mysqli_fetch_assoc($result)){
            $user_id = $row['user_id'];
            $comment = $row['comment'];

            $user_data = "SELECT * FROM users WHERE user_id='$user_id'";
            $user_result = mysqli_query($connection, $user_data);
            $user_name = mysqli_fetch_assoc($user_result)['user_name'];

            echo "
                        <br>
                        <h3><b>Name : </b>$user_name</h3> <br>
                        <p><b>Comment : </b>$comment</p>
                    ";
        }
    }


    echo "</div><br><br><br>

<footer>
    <p>&copy; 2024 Flora Vista. All Rights Reserved.</p>
</footer><br>";
}



?>
