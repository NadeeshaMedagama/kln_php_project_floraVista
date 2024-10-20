<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);

include_once "./Connection/connection.php";
include_once "./Function/function.php";

// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

echo "<link rel='stylesheet' href='style/main.css?v=". time()."'>";

echo " <a href='../cart/cart.php'>cart</a>
        <a href='../login.php'>login</a> ";

if(isset($_SESSION['user'])){
    echo "<form method='post'>
                <button type='submit' name='logout'>Logout</button>
            </form>
            <a href='../profile.php'>My Profile</a>";
}

if (isset($_POST['logout'])) {

    session_unset();
    session_destroy();

    header("Location: index.php");

}

$category_q = "SELECT * FROM categories";
$category_r = mysqli_query($connection, $category_q);

echo "<br><br>";

while($row = mysqli_fetch_assoc($category_r)){
    $cat_id = $row['category_id'];
    $cat_name = $row['category_name'];
    echo "&nbsp;<a href='?category_id=$cat_id'>$cat_name</a>&nbsp;&nbsp;";
}

if(isset($_POST['add_cart'])){

    if(!isset($_SESSION['user']['islogin']) || $_SESSION['user']['islogin'] == false){
        header("Location:  login.php");
    }else{
        $flower_id =  $_POST['flower_id'];
        $user_id = $_SESSION['user']['user_id'];

        $check_query = "SELECT * FROM shopping_cart WHERE  user_id = '$user_id' AND flower_id = '$flower_id'";
        $check_result = mysqli_query($connection, $check_query);

        if(mysqli_num_rows($check_result)>0){
            $query = "UPDATE shopping_cart SET quantity = quantity + 1 WHERE user_id ='$user_id' AND  flower_id = '$flower_id'";
        }else{
            $query = "INSERT INTO shopping_cart(user_id,flower_id,quantity) VALUES ('$user_id','$flower_id','1')";
        }

        if(mysqli_query($connection,$query)){
            header("Location: ./index.php");
        }

    }

}

if(isset($_SESSION['user'])){
    $user_id = $_SESSION['user']['user_id'];
    $total_items = "SELECT  COUNT(*) AS  total_items FROM shopping_cart WHERE user_id = '$user_id'";
    $result_total_items = mysqli_query($connection, $total_items);

    echo "Cart Items:  ".mysqli_fetch_assoc($result_total_items)['total_items'];
}

$query = "SELECT flowers.flower_id,flower_name,sale_price,quantity,dir_path FROM flowers INNER JOIN flower_images ON flowers.flower_id=flower_images.flower_id";

if (isset($_GET['search_btn'])){
    $search = user_input($_GET['search']);
    $query = "SELECT flowers.flower_id,flower_name,sale_price,quantity,dir_path FROM flowers INNER JOIN flower_images ON flowers.flower_id=flower_images.flower_id
                  WHERE flower_name LIKE '%$search%'";
}

if (isset($_GET['category_id'])){

    $category_id = user_input($_GET['category_id']);
    $query = "SELECT flowers.flower_id,flower_name,sale_price,quantity,dir_path FROM flowers INNER JOIN flower_images ON flowers.flower_id=flower_images.flower_id
                  WHERE flowers.flower_id IN (SELECT flower_categories.flower_id FROM  flower_categories WHERE category_id = '$category_id')";
}

$result = mysqli_query($connection,$query);

echo "<form action='' method='get'>
            <input type='text' name='search' placeholder='search'> &nbsp;
            <button type='submit' name='search_btn'>Search</button>
        </form>";

echo "<div class='container'>";

if(mysqli_num_rows($result)>0){
    while($row = mysqli_fetch_assoc($result)){
        $flower_id = $row['flower_id'];
        $flower_name = $row['flower_name'];
        $sale_price = $row['sale_price'];
        $dir_path = $row['dir_path'];
        $quantity =(int) $row['quantity'];

        // check the discount
        $query_discount = "SELECT * FROM flower_discounts WHERE flower_id = '$flower_id'";
        $data_set =  mysqli_query($connection, $query_discount);
        $data =  mysqli_fetch_assoc($data_set);

        $today_discount =  $data['today_dicount'];
        $loyalty_discount =  $data['loyalty_discount'];
        $price_off = $data['price_off'];
        $today_discount_end = $data['today_discount_end'];
        $loyalty_discount_end = $data['loyalty_discount_end'];
        $price_off_end = $data['price_off_end'];


        echo "
                <div class='card'>
                <a href='flowers/flowers.php?flower_id=$flower_id' >
                    <img src='$dir_path' alt='flower image' style='width:300px; height:300px'>
                </a>
                    <h2>$flower_name</h2>
                    <p class='price'>RS. $sale_price.00</p>";

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
            echo "    <p>
                        <form action='' method='post'>
                        <input type='hidden' name='flower_id' value='$flower_id'>
                        <button type='submit' name='add_cart'>Add to Cart</button>
                        </form>
                      </p>";
        }else{
            echo "<p style='color: red; font-weight: bold;'>Out of Stock</p>";
        }

        echo "    </div>
            
            ";
    }
}

echo "</div>";

?>
