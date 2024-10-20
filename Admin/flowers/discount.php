<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../../Connection/connection.php";
include_once "../../Function/function.php";

if(!isset($_SESSION['admin']['islogin']) || $_SESSION['admin']['islogin'] != true){
    header("Location: ../admin.php");
}

if(isset($_POST['submit'])){

    if(!isset($_POST['flower_discount']) || !isset($_POST['discount_presentage'])){
        echo  "<script>alert('Please fill all fields')</script>";
    }

    $flower_discount = $_POST['flower_discount'];
    $discount_presentage = $_POST['discount_presentage'];
    $flowers_id_array = $_POST['flowers_id'];
    $date = $_POST['date'];

    if($flower_discount == 'today_discount'){

        $tomorrow = strtotime("tomorrow");
        $end_date = date("Y-m-d",$tomorrow);

        foreach($flowers_id_array as $flower_id){
            try{
                // check the flower in flower_discounts table
                $check_query = "SELECT * FROM  flower_discounts WHERE flower_id = '$flower_id'";
                $check_result = mysqli_query($connection,$check_query);

                if(mysqli_num_rows($check_result)>0){
                    // update query ecec
                    $update_query = "UPDATE flower_discounts SET today_dicount ='$discount_presentage', today_discount_end='$end_date' WHERE  flower_id ='$flower_id'";
                    mysqli_query($connection,$update_query);
                }else{
                    $insert_query = "INSERT INTO flower_discounts(flower_id,today_dicount,today_discount_end) VALUES ('$flower_id',$discount_presentage,'$end_date')";

                    mysqli_query($connection,$insert_query);
                }


            }catch (Exception $e){
                logger('ERROR', $e->getMessage());
            }

        }
        header("Location: ./discount.php");

    }elseif($flower_discount == 'loyalty_discount'){
        foreach($flowers_id_array as $flower_id){
            try{
                // check the flower in flower_discounts table
                $check_query = "SELECT * FROM  flower_discounts WHERE flower_id = '$flower_id'";
                $check_result = mysqli_query($connection,$check_query);

                if(mysqli_num_rows($check_result)>0){
                    // update query ecec
                    $update_query = "UPDATE flower_discounts SET loyalty_discount= '$discount_presentage',  loyalty_discount_end= '$date' WHERE  flower_id = '$flower_id'";
                    mysqli_query($connection,$update_query);
                }else{
                    $insert_query = "INSERT INTO flower_discounts(flower_id,loyalty_discount,loyalty_discount_end) VALUES ('$flower_id',$discount_presentage,'$date')";

                    mysqli_query($connection,$insert_query);
                }


            }catch (Exception $e){
                logger('ERROR', $e->getMessage());
            }

        }
        header("Location: ./discount.php");

    }elseif($flower_discount == 'price_off'){
        foreach($flowers_id_array as $flower_id){
            try{
                // check the flower in flower_discounts table
                $check_query = "SELECT * FROM  flower_discounts WHERE flower_id = '$flower_id'";
                $check_result = mysqli_query($connection,$check_query);

                if(mysqli_num_rows($check_result)>0){
                    // update query ecec
                    $update_query = "UPDATE flower_discounts SET price_off_end= '$discount_presentage', price_off_end= '$date' WHERE  flower_id = '$flower_id'";
                    mysqli_query($connection,$update_query);
                }else{
                    $insert_query = "INSERT INTO flower_discounts(flower_id,price_off,price_off_end) VALUES ('$flower_id',$discount_presentage,'$date')";

                    mysqli_query($connection,$insert_query);
                }


            }catch (Exception $e){
                logger('ERROR', $e->getMessage());
            }

        }
        header("Location: ./discount.php");
    }else{
        logger('ERROR', 'Invalid flower discount type');

    }
}

if(isset($_GET['search'])){
    $search = user_input($_GET['search']);

    $query = "SELECT flowers.flower_id,flower_name,dir_path FROM flowers INNER JOIN flower_images ON flowers.flower_id=flower_images.flower_id
                    WHERE flower_name LIKE '%$search%'";

}else{
    $query = "SELECT flowers.flower_id,flower_name,dir_path FROM flowers INNER JOIN flower_images ON flowers.flower_id=flower_images.flower_id";
}

echo "<div>

            <form action='discount.php' method='get'>
                <input type='text' name='search' placeholder='Search'>
                <button type='submit'>Search</button>
            </form>
    
         </div>";


echo "<div>
            <form action='discount.php' method='post'>
                <select name='flower_discount' id='flower_discount'>
                    <option value='today_discount'> Today Discount</option>
                    <option value='loyalty_discount'>loyalty Discount</option>
                    <option value='price_off'> Price Off</option>
                </select> &nbsp &nbsp &nbsp &nbsp 
                <input type='number' name='discount_presentage' placeholder='Discount Presentage' required> &nbsp &nbsp &nbsp &nbsp 
                <input type='date' name='date'> &nbsp &nbsp &nbsp &nbsp
                <button  type='submit' name='submit'> add </button> <br><br>

                <h3> Select Flowers</h3><br>
            ";

$result = mysqli_query($connection,$query);

if(mysqli_num_rows($result)>0){
    while($row = mysqli_fetch_assoc($result)){

        $flower_id = $row['flower_id'];
        $flower_name = $row['flower_name'];
        $dir_path = $row['dir_path'];

        echo "<div>
                            <input type='checkbox' name='flowers_id[]' value='$flower_id'> &nbsp
                            <lable>$flower_name</lable><br>
                            <img src='../../$dir_path' alt='not found image'  width='100px' height='100px'>
                          </div>";
    }
}





echo"        </form>
          </div>";





?>
