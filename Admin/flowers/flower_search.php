<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../../Connection/connection.php";
include_once "../../Function/function.php";

if (!isset($_SESSION['admin']['islogin']) || $_SESSION['admin']['islogin'] != true){

    header("Location: ../admin.php");
}

// retrieve all flowers in database

if(!isset($_GET['search'])){

    $query = "SELECT flowers.flower_id,flower_name,quantity,dir_path FROM flowers INNER JOIN flower_images ON flowers.flower_id=flower_images.flower_id";

}
if(isset($_GET['search'])){
    $search = user_input($_GET['search']);

    $query = "SELECT flowers.flower_id,flower_name,quantity,dir_path FROM flowers INNER JOIN flower_images ON flowers.flower_id=flower_images.flower_id
                    WHERE flower_name LIKE '%$search%'";
}

echo "<div>
            <form action='flower_search.php' method='get'>
            <input type='text' name='search' placeholder='Search flower name'> &nbsp
            <button type='submit'>Search</button>
            </form>
          </div>";

$result_set = mysqli_query($connection,$query);

if(mysqli_num_rows($result_set) > 0){
    while($row = mysqli_fetch_assoc($result_set)){

        $flower_id = (string)$row['flower_id'];
        $flower_name = $row['flower_name'];
        $quantity = $row['quantity'];
        $dir_path = $row['dir_path'];

        echo "<div>

                            <a href='flower_info.php?flower_id=$flower_id'>
                                <img src='../../$dir_path' alt='not found image' width='130px' height='130px' /><br>
                                <h4>$flower_name</h4><br>
                                <lable>Quantity: $quantity</lable>
                            </a>
                
                      </div>";
    }
}

?>
