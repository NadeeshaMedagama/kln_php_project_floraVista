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

if (isset($_POST['submit'])) {
    if (!isset($_POST['flower_discount']) || !isset($_POST['discount_presentage'])) {
        echo "<script>alert('Please fill all fields')</script>";
    }

    $flower_discount = $_POST['flower_discount'];
    $discount_presentage = $_POST['discount_presentage'];
    $flowers_id_array = $_POST['flowers_id'];
    $date = $_POST['date'];

    if ($flower_discount == 'today_discount') {
        $tomorrow = strtotime("tomorrow");
        $end_date = date("Y-m-d", $tomorrow);

        foreach ($flowers_id_array as $flower_id) {
            try {

                $check_query = "SELECT * FROM flower_discounts WHERE flower_id = '$flower_id'";
                $check_result = mysqli_query($connection, $check_query);

                if (mysqli_num_rows($check_result) > 0) {

                    $update_query = "UPDATE flower_discounts SET today_discount ='$discount_presentage', today_discount_end='$end_date' WHERE flower_id ='$flower_id'";
                    mysqli_query($connection, $update_query);
                } else {
                    $insert_query = "INSERT INTO flower_discounts(flower_id, today_discount, today_discount_end) VALUES ('$flower_id', $discount_presentage, '$end_date')";
                    mysqli_query($connection, $insert_query);
                }
                header("Location: ./discount.php");

            } catch (Exception $e) {
                logger('ERROR', $e->getMessage());
            }
        }

//        header("Location: ./discount.php");

    } elseif ($flower_discount == 'loyalty_discount') {

        if (isset($date)){
            echo "<script>window.alert('Date is Required')</script>";
        }

        foreach ($flowers_id_array as $flower_id) {
            try {

                $check_query = "SELECT * FROM flower_discounts WHERE flower_id = '$flower_id'";
                $check_result = mysqli_query($connection, $check_query);

                if (mysqli_num_rows($check_result) > 0) {

                    $update_query = "UPDATE flower_discounts SET loyalty_discount= '$discount_presentage', loyalty_discount_end= '$date' WHERE flower_id = '$flower_id'";
                    mysqli_query($connection, $update_query);
                } else {
                    $insert_query = "INSERT INTO flower_discounts(flower_id, loyalty_discount, loyalty_discount_end) VALUES ('$flower_id', $discount_presentage, '$date')";
                    mysqli_query($connection, $insert_query);
                }

                header("Location: ./discount.php");

            } catch (Exception $e) {
                logger('ERROR', $e->getMessage());
            }
        }

//        header("Location: ./discount.php");

    } elseif ($flower_discount == 'price_off') {

        if (isset($date)){
            echo "<script>window.alert('Date is Required')</script>";
        }

        foreach ($flowers_id_array as $flower_id) {
            try {

                $check_query = "SELECT * FROM flower_discounts WHERE flower_id = '$flower_id'";
                $check_result = mysqli_query($connection, $check_query);

                if (mysqli_num_rows($check_result) > 0) {

                    $update_query = "UPDATE flower_discounts SET price_off = '$discount_presentage', price_off_end = '$date' WHERE flower_id = '$flower_id'";
                    mysqli_query($connection, $update_query);
                } else {
                    $insert_query = "INSERT INTO flower_discounts(flower_id, price_off, price_off_end) VALUES ('$flower_id', $discount_presentage, '$date')";
                    mysqli_query($connection, $insert_query);
                }

                header("Location: ./discount.php");

            } catch (Exception $e) {
                logger('ERROR', $e->getMessage());
            }
        }
//        header("Location: ./discount.php");

    } else {
        logger('ERROR', 'Invalid Flower Discount type!');
    }
}

if (isset($_GET['search'])) {
    $search = user_input($_GET['search']);

    $query = "SELECT flowers.flower_id, flower_name, dir_path FROM flowers INNER JOIN flower_images ON flowers.flower_id = flower_images.flower_id
                    WHERE flower_name LIKE '%$search%'";
} else {
    $query = "SELECT flowers.flower_id, flower_name, dir_path FROM flowers INNER JOIN flower_images ON flowers.flower_id = flower_images.flower_id";
}

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Discount Management</title>
    <link rel='stylesheet' href='discount.css'> 
</head>
<body>";

echo "<div class='container'> 
        <h1>Discount Page</h1>
        
        <div class='back'> <a href = 'flowers.php'><button type='submit' class='backBtn'>Back </button></a></div>
        
        <form action='discount.php' method='get'>
            <input type='text' name='search' placeholder='Search Flowers'>
            <div class='addSearch'> <button type='submit'>Search </button></div>
        </form>
      </div>";

echo "<div class='container'> 
        <form action='discount.php' method='post'>
            <select name='flower_discount' id='flower_discount'>
                <option value='today_discount'>Today Discount</option>
                <option value='loyalty_discount'>Loyalty Discount</option>
                <option value='price_off'>Price Off</option>
            </select> &nbsp &nbsp &nbsp &nbsp 
            <input type='number' name='discount_presentage' placeholder='Add Discount Percentage' required> &nbsp &nbsp &nbsp &nbsp 
            <input type='date' name='date'> &nbsp &nbsp &nbsp &nbsp
            <div class='addSearch'><button type='submit' name='submit'>Add</button></div> <br>

            <h2>Select Flowers</h2><br>";

$result = mysqli_query($connection, $query);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $flower_id = $row['flower_id'];
        echo "<div class='flower-item'>
                <label>
                    <input type='checkbox' name='flowers_id[]' value='$flower_id'>
                    " . $row['flower_name'] . "
                </label>
                <img src='" . $row['dir_path'] . "' alt='" . $row['flower_name'] . "'>
              </div>";
    }
} else {
    echo "<p>No flowers found.</p>";
}

echo "</form>
      </div>
</body>
</html>";
?>
