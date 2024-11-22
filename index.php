<?php
global $connection, $sale_price;
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

//$isLoggedIn = isset($_SESSION['user']) && $_SESSION['user']['islogin'] == true;
$isLoggedIn = isset($_SESSION['user']['islogin']) && $_SESSION['user']['islogin'] == true;

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

$itemsPerPage = 11;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) $page = 1;

$offset = ($page - 1) * $itemsPerPage;

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
          rel="stylesheet"/>

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

<section class="top-bar">
    <div class="container">
        <span class="line"></span>
        <p class="text">BEST FLORIST FLOWER DELIVERY NYC</p>
        <span class="line"></span>
    </div>
</section>

<section class="florist-section">

    <div class="section-item shop-online">

        <h3>SHOP ONLINE</h3>
        <p>Browse NYC's best curated collection of premium flowers, lush foliage plants and orchid collections.</p>
        <p>Hand-delivery is available anywhere in New York City, any day of the week. The best flower delivery NYC has to offer!</p>
        <a href = "shop/shop.html"><button>BROWSE</button></a>

    </div>

    <div class="section-item events">

        <h3>EVENTS</h3>
        <p>Details matter - our dedicated NYC Florist team will work to bring your vision to life. We provide visual mood boards and are mindful of your budget, which means no surprises. Our team will be onsite for a seamless and professional experience.</p>
        <button>LEARN MORE</button>

    </div>

    <div class="section-item plantshed-cafe">

        <h3>PLANTSHED CAFÉ</h3>
        <p>Calm and relaxing atmosphere to enjoy premium beverages and locally baked pastries. A curated selection of plants and seasonal fresh-cut flowers provides an inviting oasis for our customers who wish to escape the bustle of New York City.</p>
        <button>VISIT</button>

    </div>

</section>

<div class="container">
<!--    --><?php //if (mysqli_num_rows($result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="card">
                <a href="flowers/flowers.php?flower_id=<?= $row['flower_id'] ?>">
                    <img src="<?= $row['dir_path'] ?>" alt="flower image" style="width:300px; height:300px">
                </a>
                <h2><?= $row['flower_name'] ?></h2>
                <?php $row['sale_price'] = number_format($row['sale_price'], 2); ?>
                <p class="price"><b>RS. <?= $row['sale_price'] ?></b></p>

                <?php

                $query_discount = "SELECT * FROM flower_discounts WHERE flower_id = '{$row['flower_id']}'";
                $data_set = mysqli_query($connection, $query_discount);
                $data = mysqli_fetch_assoc($data_set);

                $today_discount = isset($data['today_discount']) ? $data['today_discount'] : null;
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
                    echo "<p><form action='' method='post'><input type='hidden' name='flower_id' value='{$row['flower_id']}'>
                    <button type='submit' name='add_cart'>Add to Cart</button></form></p>";

                } else {
                    echo "<p style='color: red; font-weight: bold;'>Out of Stock</p>";
                }

                ?>
            </div>
        <?php endwhile; ?>

<!--    --><?php //endif; ?>
</div><br>

<div class="pagination">

    <div class="pagination-row">
        <a href="?page=1" class="page-button <?= $page == 1 ? 'disabled' : '' ?>">First</a>
        <a href="?page=<?= $page - 1 ?>" class="page-button <?= $page == 1 ? 'disabled' : '' ?>">Previous</a>
    </div>

    <div class="pagination-row">
        <?php for ($i = 1; $i < $totalPages; $i++): ?>
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

<section class="floral-section">

    <div class="container">

        <div class="left-column">

            <h2>Centerpieces & Floral Installations</h2>
            <h1>Extreme Floral Experiences</h1>
            <p>We are expert floral designers and floral builders in every way. From gorgeous centerpieces to large scale immersive floral installations, we can do it all.</p>
            <a href="#" class="view-work-btn">VIEW OUR WORK</a>

        </div>

    </div>

</section>

<section class="reviews-section">

    <h2>Our reviews</h2>

    <p>Over 20,000 verified reviews from customers all across the SL</p>

    <div class="reviews-container">

        <div class="review-card">
            <div class="stars">★★★★★</div>
            <h3>Recipient's of flower have always been...</h3>
            <p>Recipient's of flower have always been happy and service delivery is always on the right day.</p>
            <div class="review-footer">
                <span>By Mr Butler</span>
                <span>21 hours ago</span>
            </div>
        </div>

        <div class="review-card">
            <div class="stars">★★★★★</div>
            <h3>Great customer experience</h3>
            <p>Great customer experience - seamless order, delivered at speed, beautiful bouquet! Thank you!</p>
            <div class="review-footer">
                <span>By Paige</span>
                <span>1 day ago</span>
            </div>
        </div>

        <div class="review-card">
            <div class="stars">★★★★★</div>
            <h3>Easy to order and delivery quick and...</h3>
            <p>Easy to order and delivery quick and efficient.</p>
            <div class="review-footer">
                <span>By Bordon Customer</span>
                <span>1 day ago</span>
            </div>
        </div>

        <div class="review-card">
            <div class="stars">★★★★★</div>
            <h3>Very helpful and efficient...</h3>
            <p>Very helpful and efficient service... excellent product.</p>
            <div class="review-footer">
                <span>By Anne Davies</span>
                <span>1 day ago</span>
            </div>
        </div>

        <div class="review-card">
            <div class="stars">★★★★★</div>
            <h3>My good friend was delighted...</h3>
            <p>My good friend was delighted with beautiful flowers. Thank you very much!</p>
            <div class="review-footer">
                <span>By CAROLINE VASMER-D</span>
                <span>1 day ago</span>
            </div>
        </div>

        <div class="review-card">
            <div class="stars">★★★★★</div>
            <h3>Recipient's of flower have always been...</h3>
            <p>Recipient's of flower have always been happy and service delivery is always on the right day.</p>
            <div class="review-footer">
                <span>By Mr Butler</span>
                <span>21 hours ago</span>
            </div>
        </div>

        <div class="review-card">
            <div class="stars">★★★★★</div>
            <h3>Great customer experience</h3>
            <p>Great customer experience - seamless order, delivered at speed, beautiful bouquet! Thank you!</p>
            <div class="review-footer">
                <span>By Paige</span>
                <span>1 day ago</span>
            </div>
        </div>

        <div class="review-card">
            <div class="stars">★★★★★</div>
            <h3>Easy to order and delivery quick and...</h3>
            <p>Easy to order and delivery quick and efficient.</p>
            <div class="review-footer">
                <span>By Bordon Customer</span>
                <span>1 day ago</span>
            </div>
        </div>

        <div class="review-card">
            <div class="stars">★★★★★</div>
            <h3>Very helpful and efficient...</h3>
            <p>Very helpful and efficient service... excellent product.</p>
            <div class="review-footer">
                <span>By Anne Davies</span>
                <span>1 day ago</span>
            </div>
        </div>

        <div class="review-card">
            <div class="stars">★★★★★</div>
            <h3>My good friend was delighted...</h3>
            <p>My good friend was delighted with beautiful flowers. Thank you very much!</p>
            <div class="review-footer">
                <span>By CAROLINE VASMER-D</span>
                <span>1 day ago</span>
            </div>
        </div>

    </div>

    <div class="rating-summary">
        <div class="stars">★★★★★</div>
        <p><strong>4.6/5</strong> | 20,787 Reviews</p>
        <p>Powered by Trustpilot</p>
    </div>

</section>

<div class="carousel-container">

    <div class="swiper">

        <div class="swiper-wrapper">

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/01.jpg" alt="Image 1" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/02.jpg" alt="Image 2" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/03.jpg" alt="Image 3" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/04.jpg" alt="Image 1" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/05.jpg" alt="Image 2" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/06.jpg" alt="Image 3" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/07.jpg" alt="Image 2" />
            </div>

            <div class="swiper-slide">
                <img src="style/images/logos/08.jpg" alt="Image 3" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/09.jpg" alt="Image 1" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/10.jpg" alt="Image 2" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/11.jpg" alt="Image 3" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/12.jpg" alt="Image 2" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/13.jpg" alt="Image 3" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/14.jpg" alt="Image 1" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/15.jpg" alt="Image 2" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/16.jpg" alt="Image 3" />
            </div>

            <div class="swiper-slide">
                <img src="Admin/home/style/images/logos/17.jpg" alt="Image 2" />
            </div>

        </div>

        <div class="swiper-pagination"></div>

        <div class="swiper-button-next"><i class="fas fa-chevron-right"></i></div>

        <div class="swiper-button-prev"><i class="fas fa-chevron-left"></i></div>

    </div>

</div>

<div class="highlight-section">

    <div class="highlight-card">

        <div class="icon">

            <img src="Admin/home/style/images/trophy.png" alt="Trophy Icon">

        </div>

        <h3>The SL's #1 Rated Ethical Flower Company</h3>

        <p>We help mark life’s moments with beautiful flowers that don’t cost the earth.</p>

    </div>

    <div class="highlight-card">

        <div class="icon">

            <img src="Admin/home/style/images/hand.png" alt="Hand Icon">

        </div>

        <h3>Every Bouquet Hand-tied</h3>

        <p>Just as each customer is different, so is each bouquet, so we treat each one individually.</p>

    </div>

    <div class="highlight-card">

        <div class="icon">

            <img src="Admin/home/style/images/tree.png" alt="Tree Icon">

        </div>

        <h3>1 Million+ Trees Planted</h3>

        <p>For every bouquet you buy, we plant two trees in countries experiencing deforestation.</p>

    </div>

</div>

<section class="ethical-score-section">

    <div class="content">

        <h2>100/100 Ethical Company Score</h2>

        <p>
            We haven’t used fossil-based single-use plastics in our packaging since 2017,
            we pioneered the industry’s first closed-loop waste system,
            and we continually work to reduce emissions and save resources.
        </p>

        <p>
            These initiatives have earned us the top ranking on the Good Shopping Guide’s Ethical Company Index
            for the past ten years, with 100/100 for the last five, and we’re determined to grow into an even
            bigger force for good.
        </p>

        <a href="https://www.arenaflowers.com/pages/sustainability/report/" class="learn-more-btn">Learn More</a>

    </div>

    <div class="image">
        <img src="Admin/home/style/images/flower.jpg" alt="Bouquet of Flowers">
    </div>

</section>

<section class="top-bar">
    <div class="container">
        <span class="line"></span>
        <p class="text">SELECT CLIENTS</p>
        <span class="line"></span>
    </div>
</section>

<section class="client-logos">

    <div class="container">

        <div class="logos">

            <div class="logo-item">
                <img src="Admin/home/style/images/clients/01.png" alt="WeWork Logo">
            </div>
            <div class="logo-item">
                <img src="Admin/home/style/images/clients/02.png" alt="Away Logo">
            </div>
            <div class="logo-item">
                <img src="Admin/home/style/images/clients/03.png" alt="Sweetgreen Logo">
            </div>
            <div class="logo-item">
                <img src="Admin/home/style/images/clients/04.png" alt="JPMorgan Logo">
            </div>
            <div class="logo-item">
                <img src="Admin/home/style/images/clients/05.png" alt="WeWork Logo">
            </div>
            <div class="logo-item">
                <img src="Admin/home/style/images/clients/06.png" alt="Away Logo">
            </div>
            <div class="logo-item">
                <img src="Admin/home/style/images/clients/07.png" alt="Sweetgreen Logo">
            </div>
            <div class="logo-item">
                <img src="Admin/home/style/images/clients/08.png" alt="Sweetgreen Logo">
            </div>
            <div class="logo-item">
                <img src="Admin/home/style/images/clients/09.png" alt="JPMorgan Logo">
            </div>
            <div class="logo-item">
                <img src="Admin/home/style/images/clients/10.png" alt="WeWork Logo">
            </div>
            <div class="logo-item">
                <img src="Admin/home/style/images/clients/11.png" alt="Away Logo">
            </div>
            <div class="logo-item">
                <img src="Admin/home/style/images/clients/12.png" alt="Sweetgreen Logo">
            </div>

        </div>


        <div class="review">

            <div class="rating">

                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>
                <i class="fa fa-star"></i>

            </div>

        </div>
    </div>

</section>

<footer>
    <div class="social-links">

        <ul>

            <li><a href="http://www.facebook.com"><img src="icons/img_5.png" alt="Facebook" class="social-icon"></a></li>
            <li><a href="http://www.instagram.com"><img src="icons/img_1.png" alt="Instagram" class="social-icon"></a></li>
            <li><a href="http://www.tiktok.com"><img src="icons/img_6.png" alt="TikTok" class="social-icon"></a></li>
            <li><a href="http://www.youtube.com"><img src="icons/img_7.png" alt="YouTube" class="social-icon"></a></li>
            <li><a href="http://www.twitter.com"><img src="icons/img_4.png" alt="Twitter" class="social-icon"></a></li>

        </ul>

    </div>

    <p class="footer-text">©2024 Flora Vista, All rights reserved. Designed by <a href="#">Dev Team</a></p>

</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
<script src="Admin/home/script.js"></script>

</body>
</html>
