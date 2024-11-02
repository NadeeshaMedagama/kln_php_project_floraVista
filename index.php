<?php
global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once "./Connection/connection.php";
include_once "./Function/function.php";

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

// Items per page and determine current page
$itemsPerPage = 11;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

// Calculate the offset
$offset = ($page - 1) * $itemsPerPage;

// Count total number of flowers for pagination
$totalFlowersQuery = "SELECT COUNT(*) AS total FROM flowers";
if (isset($_GET['search_btn'])) {
    $search = user_input($_GET['search']);
    $totalFlowersQuery = "SELECT COUNT(*) AS total FROM flowers WHERE flower_name LIKE '%$search%'";
} elseif (isset($_GET['category_id'])) {
    $category_id = user_input($_GET['category_id']);
    $totalFlowersQuery = "SELECT COUNT(*) AS total FROM flowers WHERE flower_id IN 
                          (SELECT flower_id FROM flower_categories WHERE category_id = '$category_id')";
}
$totalFlowersResult = mysqli_query($connection, $totalFlowersQuery);
$totalFlowers = mysqli_fetch_assoc($totalFlowersResult)['total'];
$totalPages = ceil($totalFlowers / $itemsPerPage);

// Query to fetch flowers with optional search or category filter
$query = "SELECT flowers.flower_id, flower_name, sale_price, quantity, dir_path FROM flowers 
          INNER JOIN flower_images ON flowers.flower_id = flower_images.flower_id ";

if (isset($_GET['search_btn'])) {
    $query .= "WHERE flower_name LIKE '%$search%' ";
} elseif (isset($_GET['category_id'])) {
    $query .= "WHERE flowers.flower_id IN 
               (SELECT flower_categories.flower_id FROM flower_categories WHERE category_id = '$category_id') ";
}

$query .= "LIMIT $itemsPerPage OFFSET $offset";
$result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flower Shop</title>
    <link rel="stylesheet" href="style/index.css?v=<?= time(); ?>">
</head>
<body>
<header>
    <div class="logo-search">
        <img src="Admin/home/style/images/Flora Vista New.png" alt="Logo" class="logo">
        <form action="" method="get">
            <input type="text" name="search" placeholder="Search for anything..." class="search-bar" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
            <button type="submit" name="search_btn" class="search-button">Search</button>
        </form>
    </div>
    <div class="header-info">
        <span class="phone">Happiness Hotline:<br>011 2001122</span>
        <span class="account"><a href="profile.php"><img src="Admin/home/style/images/account.png" width="28px" height="28px" alt="account"><br>My Profile</a></span>
        <span class="cart"><a href="cart/cart.php"><img src="Admin/home/style/images/cart.png" width="28px" height="28px" alt="cart"><br>Cart</a></span>
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
        <li><a class="dropdown-btn" href="new arrivals/arrivals.html">New Arrivals</a></li>
        <li><a class="dropdown-btn" href="loyalty program/loyalty.html">Loyalty Program</a></li>
        <li><a class="dropdown-btn" href="offers/offers.html">Special Offers</a></li>
        <li><a class="dropdown-btn" href="privacy policy/policy.html">Privacy Policy</a></li>
        <li><a class="dropdown-btn" href="contact/contact.html">Contact Us</a></li>
        <li><a class="dropdown-btn" href="subscription/subscription.html">Subscriptions</a></li>
        <li><a class="dropdown-btn" href="about/about.html">About Us</a></li>
    </ul>
</nav>

<div class="image-container">
    <img id="slideshow-image" src="Admin/home/style/banners/image1.png" alt="Slideshow Image">
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

<div class="container">
<!--    --><?php //if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="card">
                <a href="flowers/flowers.php?flower_id=<?= $row['flower_id'] ?>">
                    <img src="<?= $row['dir_path'] ?>" alt="flower image" style="width:300px; height:300px">
                </a>
                <h2><?= $row['flower_name'] ?></h2>
                <p class="price">RS. <?= $row['sale_price'] ?>.00</p>
                <?php
                $query_discount = "SELECT * FROM flower_discounts WHERE flower_id = '{$row['flower_id']}'";
                $data_set = mysqli_query($connection, $query_discount);
                $data = mysqli_fetch_assoc($data_set);

                $today_discount = isset($data['today_dicount']) ? $data['today_dicount'] : null;
                $loyalty_discount = isset($data['loyalty_discount']) ? $data['loyalty_discount'] : null;
                $price_off = isset($data['price_off']) ? $data['price_off'] : null;
                $today_discount_end = isset($data['today_discount_end']) ? $data['today_discount_end'] : null;
                $loyalty_discount_end = isset($data['loyalty_discount_end']) ? $data['loyalty_discount_end'] : null;
                $price_off_end = isset($data['price_off_end']) ? $data['price_off_end'] : null;

                if ($today_discount && date('Y-m-d') < $today_discount_end) {
                    echo "<p class='discount'>Today's Discount: $today_discount%</p>";
                }
                if (isset($_SESSION['user']['loyalty_id'])){
                    if (isset($loyalty_discount) && date('Y-m-d') < $loyalty_discount_end) {
                        echo "<p class='loyalty-discount'>Loyalty Discount: $loyalty_discount%</p>";
                    }
                }
                if ($price_off && date('Y-m-d') < $price_off_end) {
                    echo "<p class='price-off'>Price Off: $price_off%</p>";
                }


                if ($row['quantity'] > 0) {
                    echo "<p><form action='' method='post'><input type='hidden' name='flower_id' value='{$row['flower_id']}'><button type='submit' name='add_cart'>Add to Cart</button></form></p>";
                } else {
                    echo "<p style='color: red; font-weight: bold;'>Out of Stock</p>";
                }
                ?>
            </div>
        <?php endwhile; ?>
<!--    --><?php //endif; ?>
</div>

<div class="pagination">

    <div class="pagination-row">
        <a href="?page=1" class="page-button <?= $page == 1 ? 'disabled' : '' ?>">First</a>
        <a href="?page=<?= $page - 1 ?>" class="page-button <?= $page == 1 ? 'disabled' : '' ?>">Previous</a>
    </div>

    <div class="pagination-row">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?>" class="page-number <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
        <?php endfor; ?>
    </div>

    <div class="pagination-row">
        <a href="?page=<?= $page + 1 ?>" class="page-button <?= $page == $totalPages ? 'disabled' : '' ?>">Next</a>
        <a href="?page=<?= $totalPages ?>" class="page-button <?= $page == $totalPages ? 'disabled' : '' ?>">Last</a>
    </div>
</div>



<script>
    const images = [
        'Admin/home/style/banners/image1.png',
        'Admin/home/style/banners/image2.png',
        'Admin/home/style/banners/image3.png',
        'Admin/home/style/banners/image4.png',
        'Admin/home/style/banners/image5.png',
        'Admin/home/style/banners/image6.png',
        'Admin/home/style/banners/image7.png',
        'Admin/home/style/banners/image8.png',
        'Admin/home/style/banners/image9.png',
        'Admin/home/style/banners/image10.png'
    ];

    let currentIndex = 0;
    const imageElement = document.getElementById('slideshow-image');
    const dots = document.getElementsByClassName('dot');

    function showImage(index) {
        currentIndex = index;
        imageElement.src = images[currentIndex];
        updateDots();
    }

    function updateDots() {
        for (let i = 0; i < dots.length; i++) {
            dots[i].classList.remove('active');
        }
        dots[currentIndex].classList.add('active');
    }

    setInterval(function() {
        currentIndex = (currentIndex + 1) % images.length;
        imageElement.src = images[currentIndex];
        updateDots();
    }, 3000);
</script>
<br><br>
<footer>
    <div class="social-links">
        <ul>
            <li><a href="http://www.facebook.com"><img src="icons/img.png" alt="Facebook" class="social-icon"></a></li>
            <li><a href="http://www.instagram.com"><img src="icons/img_1.png" alt="Instagram" class="social-icon"></a></li>
            <li><a href="http://www.tiktok.com"><img src="icons/img_2.png" alt="TikTok" class="social-icon"></a></li>
            <li><a href="http://www.youtube.com"><img src="icons/img_3.png" alt="YouTube" class="social-icon"></a></li>
            <li><a href="http://www.twitter.com"><img src="icons/img_4.png" alt="Twitter" class="social-icon"></a></li>
        </ul>
    </div>
    <p class="footer-text">©2024 Flora Vista, All rights reserved. Designed by <a href="#">Dev Team</a></p>
</footer>
</body>
</html>
