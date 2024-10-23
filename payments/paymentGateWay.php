<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if(!isset($_SESSION['payment'])){
    header('Location: ../index.php');
}

if (isset($_POST['pay_success'])){
    $_SESSION['payment']['success'] = true;
    $_SESSION['payment']['reference_no'] = uniqid();
    header("Location: payment.php");
}

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

<div class="payment-container">
    <h2>Payment Information</h2>
    <form action="paymentGateWay.php" method="post">

        <div class="input-group">
            <label for="name">Cardholder Name</label>
            <input type="text" id="name" name="name" placeholder="John Doe" required>
        </div>

        <div class="input-group">
            <label for="card-number">Card Number</label>
            <input type="text" id="card-number" name="card_number" placeholder="1111 2222 3333 4444" required>
        </div>

        <div class="input-group">
            <label for="expiry-date">Expiry Date</label>
            <input type="text" id="expiry-date" name="expiry_date" placeholder="MM/YY" required>
        </div>

        <div class="input-group">
            <label for="cvv">CVV</label>
            <input type="text" id="cvv" name="cvv" placeholder="123" required>
        </div>

        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="you@example.com" required>
        </div>

        <button type="submit" name='pay_success' class="btn">Submit Payment</button>
    </form>
</div>

</body>
</html>
