<?php
global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once "../../Connection/connection.php";
include_once "../../Function/function.php";

if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}

$isLoggedIn = isset($_SESSION['user']) && $_SESSION['user']['islogin'] == true;

$category_q = "SELECT * FROM categories";
$category_r = mysqli_query($connection, $category_q);

if (isset($_POST['add_cart'])) {
    if (!$isLoggedIn) {
        header("Location: login.php");
        exit();
    } else {
        $flower_id = $_POST['flower_id'];
        $user_id = $_SESSION['user']['user_id'];

        $check_query = "SELECT * FROM shopping_cart WHERE user_id = '$user_id' AND flower_id = '$flower_id'";
        $check_result = mysqli_query($connection, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $query = "UPDATE shopping_cart SET quantity = quantity + 1 WHERE user_id ='$user_id' AND flower_id = '$flower_id'";
        } else {
            $query = "INSERT INTO shopping_cart(user_id, flower_id, quantity) VALUES ('$user_id', '$flower_id', '1')";
        }

        if (mysqli_query($connection, $query)) {
            header("Location: ./index.php");
            exit();
        }
    }
}

$total_items = 0;
if ($isLoggedIn) {
    $user_id = $_SESSION['user']['user_id'];
    $total_items_query = "SELECT COUNT(*) AS total_items FROM shopping_cart WHERE user_id = '$user_id'";
    $result_total_items = mysqli_query($connection, $total_items_query);
    $total_items = mysqli_fetch_assoc($result_total_items)['total_items'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flower Shop</title>
    <link rel="stylesheet" href="home.css?v=<?= time(); ?>">
</head>
<body>
<header>
    <div class="logo-search">

        <img src="style/images/Flora Vista New.png" alt="Logo" class="logo">

        <?php

        $query = "SELECT flowers.flower_id,flower_name,sale_price,quantity,dir_path FROM flowers
        INNER JOIN flower_images ON flowers.flower_id = flower_images.flower_id";

        if (isset($_GET['search_btn'])) {
            $search = user_input($_GET['search']);
            $query = "SELECT flowers.flower_id,flower_name,sale_price,quantity,dir_path
        FROM flowers
        INNER JOIN flower_images ON flowers.flower_id = flower_images.flower_id
        WHERE flower_name LIKE '%$search%'";
        }

        if (isset($_GET['category_id'])) {
            $category_id = user_input($_GET['category_id']);
            $query = "SELECT flowers.flower_id,flower_name,sale_price,quantity,dir_path
        FROM flowers
        INNER JOIN flower_images ON flowers.flower_id = flower_images.flower_id
        WHERE flowers.flower_id IN (SELECT flower_categories.flower_id
        FROM flower_categories
        WHERE category_id = '$category_id')";
        }


        $result = mysqli_query($connection, $query);


        echo "
        <form action='' method='get' >
            <input type='text' name='search' placeholder='Search for anything...' class='search-bar' value='" . (isset($_GET['search']) ? $_GET['search'] : '') . "'>
            <button type='submit' name='search_btn' class='search-button' >Search</button>
        </form>
        ";


        if (isset($_GET['search_btn'])) {

            if ($result && mysqli_num_rows($result) > 0) {
                echo "<ul>";
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<li>" . $row['flower_name'] . " - $" . $row['sale_price'] . " (Quantity: " . $row['quantity'] . ")</li>";
                }
                echo "</ul>";
            } else {
                echo "<script>alert('No results found.');</script>";
            }
        }
        ?>
    </div>
    <div class="header-info">
        <span class="phone">Happiness Hotline:<br>011 2001122</span>
        <span class="account"><a href = "../../login.php"><img src = "style/images/account.png" width="28px" height="28px" alt="account"><br>Log In.</a></span>

    </div>
</header>

<nav>
    <ul class="main-menu">
        <li>
            <div class="dropdown">
                <button class="dropdown-btn">Categories <span>&#9654;</span></button>
                <div class="dropdown-content">
                    <?php while ($row = mysqli_fetch_assoc($category_r)): ?>
                        <a href="?category_id=<?= $row['category_id'] ?>"><?= $row['category_name'] ?> <span>&gt;</span></a>
                    <?php endwhile; ?>
                </div>
            </div>
        </li>
        <li><a class="dropdown-btn" href="../../new arrivals/arrivals.html">New Arrivals</a></li>
        <li><a class="dropdown-btn" href="../../shop/shop.html">Sample Flowers</a></li>
        <li><a class="dropdown-btn" href="../../offers/offers.html">Special Offers</a></li>
        <li><a class="dropdown-btn" href="../../privacy policy/policy.html">Privacy Policy</a></li>
        <li><a class="dropdown-btn" href="../../contact/contact.html">Contact Us</a></li>
        <li><a class="dropdown-btn" href="../../subscription/subscription.html">Subscriptions </a></li>
        <li><a class="dropdown-btn" href="../../about/about.html">About Us </a></li>
    </ul>
</nav>

<div class="image-container">
    <img id="slideshow-image" src="style/banners/image1.png" alt="Slideshow Image">
</div>

<div class="dots-container">
    <span class="dot" onclick="showImage(0)"></span>
    <span class="dot" onclick="showImage(1)"></span>
    <span class="dot" onclick="showImage(2)"></span>
    <span class="dot" onclick="showImage(3)"></span>
    <span class="dot" onclick="showImage(4)"></span>
    <span class="dot" onclick="showImage(5)"></span>
    <span class="dot" onclick="showImage(6)"></span>
    <span class="dot" onclick="showImage(7)"></span>
    <span class="dot" onclick="showImage(8)"></span>
    <span class="dot" onclick="showImage(9)"></span>
</div>

<script>

    const images = ['style/banners/image1.png',
        'style/banners/image2.png',
        'style/banners/image3.png',
        'style/banners/image4.png',
        'style/banners/image5.png',
        'style/banners/image6.png',
        'style/banners/image7.png',
        'style/banners/image8.png',
        'style/banners/image9.png',
        'style/banners/image10.png'
        ];

    let currentIndex = 0;
    const slideshowImage = document.getElementById('slideshow-image');
    const dots = document.getElementsByClassName('dot');

    function showImage(index) {
        currentIndex = index;
        slideshowImage.src = images[currentIndex];
        updateDots();
        resetAutoChange();
    }

    function updateDots() {
        for (let i = 0; i < dots.length; i++) {
            dots[i].classList.remove('active');
        }
        dots[currentIndex].classList.add('active');
    }

    function autoChange() {
        currentIndex = (currentIndex + 1) % images.length;
        slideshowImage.src = images[currentIndex];
        updateDots();
    }

    function resetAutoChange() {
        clearInterval(autoChangeInterval);
        autoChangeInterval = setInterval(autoChange, 3000);
    }

    let autoChangeInterval = setInterval(autoChange, 3000);
    window.onload = function () {
        updateDots();
    };
</script>

<footer>
    <div class="social-links">
        <ul>
            <li><a href="http://www.facebook.com"><img src="../../icons/img_5.png" alt="Facebook" class="social-icon"></a></li>
            <li><a href="http://www.instagram.com"><img src="../../icons/img_1.png" alt="Instagram" class="social-icon"></a></li>
            <li><a href="http://www.tikitok.com"><img src="../../icons/img_6.png" alt="TikTok" class="social-icon"></a></li>
            <li><a href="http://www.youtube.com"><img src="../../icons/img_7.png" alt="YouTube" class="social-icon"></a></li>
            <li><a href="http://www.twitter.com"><img src="../../icons/img_4.png" alt="Twitter" class="social-icon"></a></li>
        </ul>
    </div>
    <p class="footer-text">©2024 Flora Vista, All rights reserved. Designed by <a href="#">Dev Team</a></p>
</footer>

</body>
</html>

