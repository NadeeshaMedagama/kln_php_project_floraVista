<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);

include_once  '../../Function/function.php';
include_once  '../../Connection/connection.php';


if (!isset($_SESSION['admin']['islogin']) || $_SESSION['admin']['islogin'] != true){

    header("Location: ../admin.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Suppliers</title>
    <link rel="stylesheet" href="supplier.css">
</head>
<body>

<?php

if (isset($_POST['submit_verify'])){
    try{
        $suplier_id = (int) $_POST['suplier_id'];

        $query = "UPDATE supliers SET verify=true WHERE suplier_id=$suplier_id;";

        if (mysqli_query($connection, $query)){

            logger("INFO", "supplier verify successfully");
        }else{

            throw new Exception(mysqli_error($connection));
        }
    }catch(Exception $e){
        logger("ERROR", "supplier submit verify" . $e->getMessage());
    }
}

echo "<div id='supplier-verify'>";

if(cookie_checker_admin()){

    try{

        echo "<h1>Supplier Verify</h1><br>";

        echo "<div class='back'> <a href = '../admin_panel.php'><button type='submit' class='backBtn'>Back </button></a></div>";

        echo "<div class='reg'> <a href = 'suplier_details.php'><button type='submit' class='regBtn'>Registered Suppliers </button></a></div>";

        $query = "SELECT * FROM supliers WHERE verify=false ;";

        $result = mysqli_query($connection, $query);
        logger("INFO", "get the supplier data in db successfully");

        echo "<table id='table-supplier-verify border='1'>
                <th>Supplier id</th>
                <th>Username</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Verify</th>
            ";


        while($row = mysqli_fetch_assoc($result)){

            $suplier_id = $row["suplier_id"];
            $suplier_username = $row["supplier_username"];
            $email = $row["email"];
            $mobile = $row["mobile"];

            echo "<tr><td> $suplier_id </td> <td> $suplier_username </td> <td>$email</td> <td> $mobile </td>
                <td> 
                    <form action='' method='post'>
                        <input type='hidden' value='$suplier_id' name='suplier_id'>
                        <button type='submit' name='submit_verify'>Verify</button>
                    </form>

                </td></tr>";


        }

        echo "</table>";

    }catch(Exception $e){
        logger("ERROR", $e->getMessage());
    }
}

echo "</div>";


?>