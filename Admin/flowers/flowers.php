<?php

session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);

include_once  '../../Function/function.php';
include_once '../../Connection/connection.php';


// admin protection page
if (!isset($_SESSION['admin']['islogin']) || $_SESSION['admin']['islogin'] != true){

    header("Location: ../admin.php");
}

echo "<a href='discount.php'> Add Discounts</a>
      <a href='flower_search.php'>Flower Information</a>";

if (isset($_POST['delete_category'])){
    try{

        $category_id = (int) $_POST['category_id'];

        $query = "DELETE FROM flowers_category WHERE category_id=$category_id";
        $result = mysqli_query($connection, $query);
        logger("INFO", "$category_id id delete successfully");

    }catch(Exception $e){
        logger('ERROR', $e->getMessage());
    }
}

if (isset($_POST["add_category"])){
    // form validation
    $add_category = user_input($_POST['category_name']);

    // Void sql injection
    $add_category = mysqli_real_escape_string($connection, $add_category);

    if (!empty($add_category)){
        $query = "INSERT INTO categories(category_name) VALUES ('$add_category')";

        try{
            if (mysqli_query($connection, $query)){
                logger('INFO', 'category: $add_category added successfully');
                info_alert("Category is added Successfully");
                header("Location: ./flowers.php");

            }else{
                throw new Exception(mysqli_error($connection));
            }
        }catch(Exception $e){
            logger('ERROR', $e->getMessage());
        }
    }else{
        logger('WARNING', "user input is empty");
        //error_alert("Input is Empty");
        echo"<script>window.alert('eqwr')</script>";
    }
}

// flower upload
if (isset($_POST["flower_upload"])) {
    try {

        if (!isset($_POST['flower_name']) || !isset($_FILES['image'])) {
            echo "<script> window.alert('All fields are required')</script>";

        }

        $flower_id = uniqid();
        $flower_name = user_input($_POST["flower_name"]);
        $flower_description = user_input($_POST['description']);
        $sale_price = $_POST['sale_price'];

        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $file_name = $_FILES['image']['name'];

        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $unique_file_name = uniqid() . '.' . $file_extension;

        $save_dir = (string) "../../uploads/" . $unique_file_name;
        $upload_dir = (string) "uploads/" . $unique_file_name;

        if (move_uploaded_file($image_tmp_name, $save_dir)) {

            $insert_flower_query = "INSERT INTO flowers(flower_id, flower_name, description,sale_price) 
                                    VALUES ('$flower_id', '$flower_name', '$flower_description','$sale_price')";
            $insert_image_query = "INSERT INTO flower_images(flower_id, dir_path) 
                                   VALUES ('$flower_id', '$upload_dir')";

            logger("INFO","upload file successfully");

            if (mysqli_query($connection, $insert_flower_query) && mysqli_query($connection, $insert_image_query)) {
                header("Location: ./flowers.php");  // reload the this page
            } else {
                echo "<script> window.alert('Database error')</script>";
            }
        } else {
            echo "<script> window.alert('Failed to upload image')</script>";
        }

    } catch (Exception $e) {
        logger("ERROR", $e->getMessage());
    }
}

if (isset($_POST['submit_flowers_categories'])){
    $category_id = $_POST['category_id'];
    $flowers_id_array = $_POST['flower_id_array'];

    $delete_query = "DELETE FROM flower_categories WHERE category_id='$category_id'";

    if(mysqli_query($connection,$delete_query)){
        foreach($flowers_id_array as $flower_id){
            $insert_query = "INSERT INTO flower_categories(flower_id,category_id) VALUES ('$flower_id','$category_id')";

            try{
                mysqli_query($connection,$insert_query);
            } catch (Exception $e){
                logger("ERROR", $e->getMessage());
            }
        }
        header("Location: ./flowers.php");
    }
}

// Js handele show or hide flowers_category
echo "<div id='flowers_category'>";
echo "<h4>Flowers Category</h4>";
echo "<div id='show_category'>";
// show the all category


try{

    $query = "SELECT * FROM categories";

    $result = mysqli_query($connection, $query);

    logger("INFO", "get flowers_category data successfully");

    // display the aviable category

    if (mysqli_num_rows($result) > 0){

        echo "<table id='show_category_table' border='1'>

                            <tr><th>Category</th>
                            <th>Delete</th></tr>       
                    ";
        while($row = mysqli_fetch_assoc($result)){
            $category_id = $row["category_id"];
            $category_name = $row["category_name"];

            echo "<tr><td>$category_name</td>
                            <td>
                            <form action='flowers.php' method='post'>
                            <input type='hidden' name='category_id' value='$category_id'>
                            <button type='submit' name='delete_category'>Delete</button>
                            </form>
                            </td>
                            </tr>
                        ";
        }

        echo "</table>";

    }else{
        echo "<h3>NO Categories in table<h3>";
    }

}catch(Exception $e){
    logger("ERROR", $e->getMessage());
}

echo "</div>";


echo "<div id='add_category'>";

echo "<form action='flowers.php' method='post' id='add_flower_category'>

            <lable>Category Name :</lable> &nbsp
            <input type='text' name='category_name' id='category_name'>
            <br><br>
            <button type='submit' name='add_category'>ADD</button>
            </form>";

echo "</div>";




echo "</div>";

// js handele show or hide upload_flowers
echo "<div id='upload_flowers'>";



echo "<div id='flower_upload_form'>";
echo "<h4> Add flower </h4>";

echo"<form action='flowers.php' method='post' id='upload_flower_form' enctype='multipart/form-data'>
                <input type='text' name='flower_name' id='flower_name' placeholder='Flower Name' required ><br><br>
                
                <textarea name='description' id='description' placeholder='Description'  required/></textarea><br><br>
                <input type='number' name='sale_price' placeholder='Flower sale price' required/><br><br>
                <input type='file' name='image' accept='image/*' required><br><br>
                <button type='submit' name='flower_upload'>upload</button>'";
echo "</form>";

echo "</div>";


echo "</div>";

echo "<div id=flower_categories>";
echo "<h4> Flowers Add to Categories</h4>";

echo "<form action='flowers.php' method='post' id='flowers-categories'>";

$query = "SELECT * FROM categories";
$result_set = mysqli_query($connection,$query);

echo "<lable>Select Category</lable>&nbsp
              <select id='category_name' name='category_id'>";

if(mysqli_num_rows($result) > 0){
    while ($row = mysqli_fetch_assoc($result_set)){
        $category_id = $row['category_id'];
        $category_name = $row['category_name'];
        echo "<option value='$category_id'>$category_name</option>";

    }
}
echo "</select> &nbsp 
                    <button type='submit' name='search_category'>Search </button>
    </form>";


// get the flowers detail from flowers category
if(isset($_POST['search_category'])){

    $category_id = $_POST['category_id'];

    $query = "SELECT * FROM categories WHERE category_id='$category_id' LIMIT 1";
    $result = mysqli_query($connection,$query);
    $category_name = mysqli_fetch_assoc($result)['category_name'];

    echo "<form action='flowers.php' method='post'> 
                    <h4>Select a flowers - category name: $category_name</h4>
                    <input type='hidden' name='category_id' value='$category_id'/>";

    $query = "SELECT flowers.flower_id,flowers.flower_name,flower_images.dir_path FROM flowers INNER JOIN flower_images ON flowers.flower_id=flower_images.flower_id";

    $flowers_data_set = mysqli_query($connection,$query);

    if(mysqli_num_rows($flowers_data_set)> 0){
        while($row = mysqli_fetch_assoc($flowers_data_set)){
            $flower_id = $row['flower_id'];
            $flower_name = $row['flower_name'];
            $dir_path = $row ['dir_path'];

            // ckek flower_id in flower_categories table
            $check_query = "SELECT * FROM flower_categories WHERE flower_id='$flower_id' AND category_id='$category_id' LIMIT 1";
            $result = mysqli_query($connection,$check_query);

            if (mysqli_num_rows($result) > 0){
                echo "<input type='checkbox' name='flowers[]' value='$flower_id' checked>
                            <lable>$flower_name</lable>
                            <img src='../../$dir_path' alt='no image' width='100px' height='100px'/>";

            }else{
                echo "<input type='checkbox' name='flower_id_array[]' value='$flower_id'>
                            <lable>$flower_name</lable>
                            <img src='../../$dir_path' alt='no image' width='100px' height='100px'/>";
            }



        }
    }
    echo "<button type='submit' name='submit_flowers_categories'>Insert</button>
                </form>";
}



echo "</div>";


/*
<select id='category_name' name='category_name'>";
                    //<option value='category_id'> categories </option>
                    try{
                        $query = "SELECT * FROM categories";
                        $result = mysqli_query($connection, $query);


                        if (mysqli_num_rows($result) > 0){
                            while ($row = mysqli_fetch_assoc($result)){
                                $categoryId = $row["category_id"];
                                $categoryName = $row["category_name"];
                                echo "<option value='$categoryId'>$categoryName</option>";
                            }
                        }
                    }catch(Exception $e){
                        logger("ERROR", $e->getMessage());
                    }
                echo "</select><br><br>

                */
?>