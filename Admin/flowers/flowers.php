<?php
global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once '../../Function/function.php';
include_once '../../Connection/connection.php';

// Admin protection page
if (!isset($_SESSION['admin']['islogin']) || $_SESSION['admin']['islogin'] != true) {
    header("Location: ../admin.php");
}

// Handle deletion of a category
if (isset($_POST['delete_category'])) {
    try {
        $category_id = (int) $_POST['category_id'];
        $query = "DELETE FROM flowers_category WHERE category_id=$category_id";
        $result = mysqli_query($connection, $query);
        logger("INFO", "$category_id id deleted successfully");
    } catch (Exception $e) {
        logger('ERROR', $e->getMessage());
    }
}

// Handle addition of a category
if (isset($_POST["add_category"])) {
    $add_category = user_input($_POST['category_name']);
    $add_category = mysqli_real_escape_string($connection, $add_category);

    if (!empty($add_category)) {
        $query = "INSERT INTO categories(category_name) VALUES ('$add_category')";
        try {
            if (mysqli_query($connection, $query)) {
                logger('INFO', 'category: $add_category added successfully');
                info_alert("Category is added Successfully");
                header("Location: ./flowers.php");
            } else {
                throw new Exception(mysqli_error($connection));
            }
        } catch (Exception $e) {
            logger('ERROR', $e->getMessage());
        }
    } else {
        logger('WARNING', "User input is empty");
        echo "<script>window.alert('Input is Empty');</script>";
    }
}

// Flower upload
if (isset($_POST["flower_upload"])) {
    try {
        if (!isset($_POST['flower_name']) || !isset($_FILES['image'])) {
            echo "<script>window.alert('All fields are required')</script>";
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

        $save_dir = "../../uploads/" . $unique_file_name;
        $upload_dir = "uploads/" . $unique_file_name;

        if (move_uploaded_file($image_tmp_name, $save_dir)) {
            $insert_flower_query = "INSERT INTO flowers(flower_id, flower_name, description, sale_price) 
                                    VALUES ('$flower_id', '$flower_name', '$flower_description', '$sale_price')";
            $insert_image_query = "INSERT INTO flower_images(flower_id, dir_path) 
                                   VALUES ('$flower_id', '$upload_dir')";

            logger("INFO", "Upload file successfully");

            if (mysqli_query($connection, $insert_flower_query) && mysqli_query($connection, $insert_image_query)) {
                header("Location: ./flowers.php");
            } else {
                echo "<script>window.alert('Database error')</script>";
            }
        } else {
            echo "<script>window.alert('Failed to upload image')</script>";
        }
    } catch (Exception $e) {
        logger("ERROR", $e->getMessage());
    }
}

// Handling category selection
if (isset($_POST['submit_flowers_categories'])) {
    $category_id = $_POST['category_id'];
    $flowers_id_array = $_POST['flower_id_array'];

    $delete_query = "DELETE FROM flower_categories WHERE category_id='$category_id'";

    if (mysqli_query($connection, $delete_query)) {
        foreach ($flowers_id_array as $flower_id) {
            $insert_query = "INSERT INTO flower_categories(flower_id, category_id) VALUES ('$flower_id', '$category_id')";

            try {
                mysqli_query($connection, $insert_query);
            } catch (Exception $e) {
                logger("ERROR", $e->getMessage());
            }
        }
        header("Location: ./flowers.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Place</title>

    <!-- Link to your CSS file -->
    <link rel="stylesheet" href="flowers.css"> <!-- Add your CSS file here -->
</head>
<body>
<!-- Display navigation links -->
<br><a href="discount.php" class="add">Add Discounts</a><br><br><br>
<a href="flower_search.php" class="add">Flower Information</a><br><br><br>

<!-- Show flowers category section -->
<div id="flowers_category">
    <h2>&nbsp;&nbsp;Flowers Category</h2>
    <div id="show_category">
        <?php
        try {
            $query = "SELECT * FROM categories";
            $result = mysqli_query($connection, $query);
            logger("INFO", "Get flowers_category data successfully");

            if (mysqli_num_rows($result) > 0) {
                echo "<table id='show_category_table' border='1'>
                            <tr><th>Category</th><th>Delete</th></tr>";
                while ($row = mysqli_fetch_assoc($result)) {
                    $category_id = $row["category_id"];
                    $category_name = $row["category_name"];

                    echo "<tr>
                                <td>$category_name</td>
                                <td>
                                    <form action='flowers.php' method='post'>
                                        <input type='hidden' name='category_id' value='$category_id'>
                                        <button type='submit' name='delete_category'>Delete</button>
                                    </form>
                                </td>
                              </tr>";
                }
                echo "</table>";
            } else {
                echo "<h3>No Categories in table</h3>";
            }
        } catch (Exception $e) {
            logger("ERROR", $e->getMessage());
        }
        ?>
    </div>

    <!-- Add category form -->
    <div id="add_category">
        <form action="flowers.php" method="post" id="add_flower_category">
            <label class="cat">Category Name :</label><br>
            <input type="text" name="category_name" id="category_name" placeholder="Category Name"><br><br>
            <button type="submit" name="add_category">ADD</button><br>
        </form>
    </div>
</div>

<!-- Upload flower section -->
<div id="upload_flowers">
    <div id="flower_upload_form">
        <h2>Upload Flower</h2>
        <form action="flowers.php" method="post" id="upload_flower_form" enctype="multipart/form-data">
            <label>Flower Name:</label><br>
            <input type="text" name="flower_name" id="flower_name" placeholder="Flower Name" required><br><br>
            <label>Description:</label><br>
            <textarea name="description" id="description" placeholder="Description" required></textarea><br><br>
            <label>Sale Price:</label><br>
            <input type="number" name="sale_price" placeholder="Flower sale price" required/><br><br>
            <label>Image:</label><br>
            <input type="file" name="image" accept="image/*" required><br><br>
            <button type="submit" name="flower_upload">Upload</button><br>
        </form>
    </div>
</div>

<!-- Flower categories section -->
<div id="flower_categories">
    <h2>&nbsp;&nbsp;Flowers Add to Categorize</h2>
    <form action="flowers.php" method="post" id="flowers-categories">
        <label>Select Category:</label><br>
        <select id="category_name" name="category_id">
            <?php
            $query = "SELECT * FROM categories";
            $result_set = mysqli_query($connection, $query);

            if (mysqli_num_rows($result_set) > 0) {
                while ($row = mysqli_fetch_assoc($result_set)) {
                    $category_id = $row['category_id'];
                    $category_name = $row['category_name'];
                    echo "<option value='$category_id' class='category'>$category_name</option>";
                }
            }
            ?>
        </select> <br>
        <button type="submit" name="search_category">Search</button><br>
    </form>

    <?php
    // Get the flowers detail from flowers category
    if (isset($_POST['search_category'])) {
        $category_id = $_POST['category_id'];
        $query = "SELECT * FROM categories WHERE category_id='$category_id' LIMIT 1";
        $result = mysqli_query($connection, $query);
        $category_name = mysqli_fetch_assoc($result)['category_name'];

        echo "<form action='flowers.php' method='post'>
                    <h4>Select a flower - category name: $category_name</h4>
                    <input type='hidden' name='category_id' value='$category_id'/>";

        $query = "SELECT * FROM flowers";
        $result_set = mysqli_query($connection, $query);

        if (mysqli_num_rows($result_set) > 0) {
            echo "<div id='flower_list'>";
            while ($row = mysqli_fetch_assoc($result_set)) {
                $flower_name = $row['flower_name'];
                $flower_id = $row['flower_id'];

                echo "<input type='checkbox' value='$flower_id' name='flower_id_array[]'> 
                          <label>$flower_name</label><br>";
            }
            echo "</div>";
            echo "<button type='submit' name='submit_flowers_categories'>Add</button>";
            echo "</form>";
        } else {
            echo "No flowers available";
        }
    }
    ?>
</div>
</body>
</html>
