<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";


if (!isset($_SESSION['user']['email']) || !isset($_SESSION['user']['mobile'])) {
    echo "Session data is missing!";
    exit;
}

if (!isset($_SESSION['payment'])) {
    header('Location: ../index.php');
    exit;
}

if (isset($_GET['pay_success'])) {
    $_SESSION['payment']['success'] = true;
    $_SESSION['payment']['reference_no'] = urldecode($_GET['ref_no']);
    header("Location: payment.php");
    exit;
}

$total = $_SESSION['payment']['total'] ; // Fallback for safety
$email = $_SESSION['user']['email'] ;
$mobile = $_SESSION['user']['mobile'];
$ref_no = uniqid();

$merchant_id = 1228547;
$currency = "LKR";
$merchant_secret = "ODAwMjE4Mzc5Mjk3NTgzMTI1MDI2Mjc5NDQzNjUzNTkyMTUwMDUx";

$hash = strtoupper(
    md5(
        $merchant_id .
        $ref_no .
        number_format($total, 2, '.', '') .
        $currency .
        strtoupper(md5($merchant_secret))
    )
);

$return_url = 'http://localhost/kln-php/payments/paymentGateWay.php?pay_success=true&ref_no=' . urlencode($ref_no);

$data = [
    'merchant_id' => $merchant_id,
    'amount' => $total,
    'return_url' => $return_url,
    'order_id' => $ref_no,
    'currency' => 'LKR',
    'hash' => $hash,
    'email' => $email,
    'mobile' => $mobile
];


$json_data = json_encode($data);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway</title>
    <link rel="stylesheet" href="../style/payments/paymentGateWay.css">
</head>
<body>

<form method="post" action="https://sandbox.payhere.lk/pay/checkout">   

    <h3>Amount : <?php echo $total; ?></h3>
    <input type="hidden" name="merchant_id" value="<?php echo $merchant_id ?>">    <!-- Replace your Merchant ID -->
    <input type="hidden" name="return_url" value="<?php echo $return_url ?>"> <!-- Replace your Return URL -->
    <input type="hidden" name="cancel_url" value="http://sample.com/cancel">
    <input type="hidden" name="notify_url" value="http://sample.com/notify">  
    <input type="hidden" name="order_id" value="<?php echo $ref_no ?>"> <!-- Replace your Order ID -->
    <input type="hidden" name="items" value="FloraVista Flowers">
    <input type="hidden" name="currency" value="LKR">
    <input type="hidden" name="amount" value="<?php echo $total ?>">  
    </br></br>Customer Details</br>
    <input type="text" name="first_name" value="Saman" required>
    <input type="text" name="last_name" value="Perera" required>
    <input type="text" name="email" value="samanp@gmail.com" required>
    <input type="text" name="phone" value="0771234567" required>
    <input type="text" name="address" value="No.1, Galle Road" required>
    <input type="text" name="city" value="Colombo" required>
    <input type="hidden" name="country" value="Sri Lanka">
    <input type="hidden" name="hash" value="<?php echo $hash ?>">    <!-- Replace with generated hash -->
    <input type="submit" value="Pay BY PayHere">   
</form> 
</body>
</html>
