<?php
global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);

include_once "../../Connection/connection.php";
include_once "../../Function/function.php";

// session protection
if (!isset($_SESSION['admin']['islogin']) || $_SESSION['admin']['islogin'] != true){

    header("Location: ../admin.php");
}

if(isset($_POST['delete_flower'])){
    try {
        $flower_id = $_POST['flower_id'];

        if (!isset($flower_id)) {
            echo "<script> window.alert('Flower ID is Missing!')</script>";
        } else {

            $query = "DELETE FROM flowers WHERE flower_id = '$flower_id'";

            if (mysqli_query($connection, $query)) {

                header("Location: flower_search.php");
                exit();
            } else {
                throw new Exception("Failed to Delete Flower with ID: $flower_id !");
            }
        }
    } catch (Exception $e) {
        logger("ERROR", $e->getMessage());
    }
}


if(isset($_POST['update_flower'])){
    try{
        $flower_id = $_POST['flower_id'];
        $flower_name = $_POST['flower_name'];
        $sale_price = $_POST['sale_price'];
        $quantity = $_POST['quantity'];
        $description = $_POST['description'];



        if(!isset($flower_id) || !isset($flower_name) || !isset($sale_price) || !isset($quantity) || !isset($description)){
            echo "<script> window.alert('fill the all fields')</script>";
        }

        // update the flowers data
        $query = "UPDATE flowers SET flower_name = '$flower_name', sale_price='$sale_price'  , quantity='$quantity' , description='$description' WHERE flower_id = '$flower_id'";

        if (mysqli_query($connection, $query)){
            header("Location: ./flower_info.php?flower_id=$flower_id");
            exit();
        }else{
            throw new Exception("flower_id : $flower_id Update fail");
        }

    }catch (Exception $e){
        logger("ERROR",$e->getMessage());
    }
}

if(isset($_GET['flower_id'])){

    $flower_id = user_input($_GET['flower_id']);

    $query = "SELECT flowers.flower_id,flower_name,quantity,sale_price,description,dir_path FROM flowers INNER JOIN flower_images 
                  ON flowers.flower_id=flower_images.flower_id WHERE flowers.flower_id='$flower_id' LIMIT 1";

    $result_set = mysqli_query($connection,$query);


    if(mysqli_num_rows($result_set)>0){

        $data = mysqli_fetch_assoc($result_set);
        $flower_id = $data['flower_id'];
        $flower_name = $data['flower_name'];
        $quantity = (int) $data['quantity'];
        $sale_price = $data['sale_price'];
        $description = $data['description'];
        $dir_path = $data['dir_path'];

        echo "<head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Flower Information Update</title>
                <link rel='stylesheet' href='flowersInfo.css'> 
              </head>";

        echo "<body>";

        echo "<div class= 'container'>";

        echo "<div id='flower_image'>
                     <img src='../../$dir_path' alt='no image found' width='300px' height='300px'/> 
                   </div>";

        echo "<div>
                        <form action='flower_info.php'  method='post'>

                            <input type='hidden' name='flower_id' value='$flower_id' />

                            <lable>Flower Name :</lable><br/>
                            <input type='text' name='flower_name' value='$flower_name' placeholder='Flower Name' required/><br/><br/>

                            <lable>Quantity : </lable><br/>
                            <input type='number' name='quantity' value='$quantity' placeholder='Quantity' required/><br/><br/>

                            <lable>Sale Price : </lable><br/>
                            <input type='text' name='sale_price' value='$sale_price' placeholder='Sale Price' required/><br/><br/>

                            <lable>Description : </lable><br/>
                            <textarea name='description' id='description' placeholder='Description' required/>$description</textarea><br/><br/>

                            <button type='submit' name='update_flower'>Update</button>
                            
                            <div class='delete'>
                            <button type='submit' name='delete_flower' class='deleteBtn' onclick='return confirm('Are you sure you want to delete this flower?')'>Delete Flower</button>
                            </div>
                            <br><br> 

                            <a href='flower_search.php' class='button'>Back to Search Page</a>

                        </form>
                        </div>
                  </div>";



    }else{
        echo "<h3>Not Found</h3>";
    }

}

?>
