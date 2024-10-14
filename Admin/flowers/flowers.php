<?php

global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors",1);

include_once  '../../Function/function.php';
include_once '../../Connection/connection.php';


// admin protection page
if (!isset($_SESSION['admin']['islogin']) || !$_SESSION['admin']['islogin']){

    header("Location: ../admin.php");
}

if (isset($_POST['delete_category'])){
    try{

        $category_id = (int) $_POST['category_id'];

        $query = ("DELETE FROM flowers_category WHERE category_id=$category_id");
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
        $query = ("INSERT INTO flowers_category(category_name) VALUES ('$add_category')");

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
        echo"<script>window.alert('error')</script>";
    }
}

if(isset($_POST["flower_upload"] )){
    try{
        if(!isset($_POST['flower_name']) || !isset($_POST['category_name']) || !isset($_FILES['image'])){
            echo "<script> window.alert('required all fields')</script>";
            exit();
        }

        // auto gen flower_id
        $flower_id = uniqid();
        $flower_name = user_input($_POST["flower_name"]);
        $flower_category_id = (int) $_POST["category_name"];
        $flower_description = user_input($_POST['description']);

        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        

       // $image_type = strtolower(pathinfo($_FILES['image']['type'],PATHINFO_EXTENSION));

        $file_name = $_FILES['image']['name'];

        $save_dir = (string) "uploads/images/". $file_name;

        if(move_uploaded_file($image_tmp_name,$save_dir)){
            // save to data to database
            $insert_flower_query = "INSERT INTO flowers(flower_id,flower_name,category_id,description) VALUES ('$flower_id','$flower_name','$flowers_category_id','$$flower_description')";
            $insert_image_query = "INSERT INTO images_links(flower_id,file_path) VALUES ('$flower_id','$$save_dir')";

            if(mysqli_query($connection,$insert_flower_query) && mysqli_query($connection,$insert_image_query)){
                header("Location: ./flowers.php");
            }
            
        }

        echo $flower_name;
        echo "<pre>";
        print_r($_FILES['image']);
        echo "</pre>";

    }catch(Exception $e){
        logger("ERROR", $e->getMessage());
    }
}

// Js handle show or hide flowers_category
echo "<div id='flowers_category'>";
    echo "<h4>Flowers Category</h4>";
    echo "<div id='show_category'>";
        // show the all category

        if (cookie_checker_admin()){
            try{

                $query = "SELECT * FROM flowers_category";

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
        }
    echo "</div>";

if (cookie_checker_admin()){
    echo "<div id='add_category'>";

    echo "<form action='flowers.php' method='post' id='add_flower_category'>

            <lable>Category Name :</lable> &nbsp
            <input type='text' name='category_name' id='category_name'>
            <br><br>
            <button type='submit' name='add_category'>ADD</button>
            </form>";
    
    echo "</div>";
    
}

    

echo "</div>";

// js handele show or hide upload_flowers
echo "<div id='upload_flowers'>";

if(cookie_checker_admin()){
    
    echo "<div id='flower_upload_form'>";
    echo "<h4> Add flower </h4>";

        echo"<form action='flowers.php' method='post' id='upload_flower_form' enctype='multipart/form-data'>
                <input type='text' name='flower_name' id='flower_name' placeholder='Flower Name' required ><br><br>
                <select id='category_name' name='category_name'>";
                    //<option value='category_id'> categories </option>
                    try{
                        $query = "SELECT * FROM flowers_category";
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
                <textarea name='description' id='description' placeholder='Description'  required></textarea><br><br>
                <input type='file' name='image' accept='image/*' required><br><br>
                <button type='submit' name='flower_upload'>upload</button>'";
        echo "</form>";
    
    echo "</div>";
}

echo "</div>";


?>