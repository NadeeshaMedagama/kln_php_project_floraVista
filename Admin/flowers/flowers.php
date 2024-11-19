<?php
global $connection;
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once '../../Function/function.php';
include_once '../../Connection/connection.php';


if (!isset($_SESSION['admin']['islogin']) || $_SESSION['admin']['islogin'] != true) {
    header("Location: ../admin.php");
}

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

// handle addition of a category
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

// flower upload
if (isset($_POST["flower_upload"])) {
    try {
        if (!isset($_POST['flower_name']) || !isset($_FILES['image'])) {
            echo "<script>window.alert('All fields are required')</script>";
        }

        $flower_id = uniqid();
        $flower_name = user_input($_POST["flower_name"]);
        $flower_description = user_input($_POST['description']);
        $sale_price = $_POST['sale_price'];
        $quantity = $_POST['quantity'];

        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $file_name = $_FILES['image']['name'];

        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $unique_file_name = uniqid() . '.' . $file_extension;

        $save_dir = "../../uploads/" . $unique_file_name;
        $upload_dir = "uploads/" . $unique_file_name;

        if (move_uploaded_file($image_tmp_name, $save_dir)) {
            $insert_flower_query = "INSERT INTO flowers(flower_id, flower_name, quantity, description, sale_price) 
                                    VALUES ('$flower_id', '$flower_name', '$quantity', '$flower_description', '$sale_price')";
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

// handling category selection
if (isset($_POST['submit_flowers_categories'])) {
    $category_id = $_POST['category_id'];
    $flowers_id_array = $_POST['flower_id_array'];

    $delete_query = "DELETE FROM flower_categories WHERE category_id='$category_id'";

    if (mysqli_query($connection,$delete_query)) {
        foreach ($flowers_id_array as $flower_id) {
            $insert_query = "INSERT IGNORE INTO flower_categories(flower_id, category_id) VALUES ('$flower_id', '$category_id')";

            try {
                mysqli_query($connection, $insert_query);
            } catch (Exception $e) {
                logger("ERROR", $e->getMessage());
            }
        }

    }
    header("Location: ./flowers.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Place</title>

    <link rel="stylesheet" href="flowers.css">
</head>
<body>

<div class="container">

    <br><div class="head"> <a href="discount.php" class="added">Add Discounts&nbsp;</a><br><br>
        <a href="flower_search.php" class="added">Flower Information</a></div><br><br>

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
                                        
                                        <div class='delete'>
                                        <button type='submit' name='delete_category'><b>Delete</b></button>
                                        </div>
                                        
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

        <div id="add_category">
            <form action="flowers.php" method="post" id="add_flower_category">
                <label class="cat">Category Name :</label><br>
                <input type="text" name="category_name" id="category_name" placeholder="Category Name"><br><br>
                <button type="submit" name="add_category">ADD</button><br>
            </form>

            <div class="back"> <a href = "../admin_panel.php"><button type="submit" class="backBtn">Back </button></a></div>

        </div>
    </div>

    <!-- upload flower section -->
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
                <label>Quantity : </label><br>
                <input type='number' name='quantity' placeholder='Flower Quantity' step='1' min='0' required><br><br>
                <label>Image:</label><br>
                <input type="file" name="image" accept="image/*" required><br><br>
                <button type="submit" name="flower_upload">Upload</button><br>
            </form>
        </div>
    </div>

    <div id=flower_categories>
        <h2> Flowers Add to Categories</h2><br>

        <form action='flowers.php' method='post' id='flowers-categories'>

            <?php
            $query = "SELECT * FROM categories";
            $result_set = mysqli_query($connection,$query);

            echo "<lable><h3><b>Select Category</b></h3></lable>&nbsp
                  <select id='category_name' name='category_id'>";

            if(mysqli_num_rows($result) > 0){
                while ($row = mysqli_fetch_assoc($result_set)){
                    $category_id = $row['category_id'];
                    $category_name = $row['category_name'];
                    echo "<option value='$category_id'>$category_name</option>";

                }
            }
            ?>
            </select> &nbsp
            <button type='submit' name='search_category'>Search </button>
        </form><br><br>


        <?php
        if (isset($_POST['search_category'])) {

            $category_id = $_POST['category_id'];


            $query = "SELECT * FROM categories WHERE category_id='$category_id' LIMIT 1";
            $result = mysqli_query($connection, $query);
            $category_name = mysqli_fetch_assoc($result)['category_name'];


            $query = "SELECT flowers.flower_id, flowers.flower_name, flower_images.dir_path 
              FROM flowers 
              INNER JOIN flower_images ON flowers.flower_id = flower_images.flower_id";
            $flowers_data_set = mysqli_query($connection, $query);


            $flowers = [];
            if (mysqli_num_rows($flowers_data_set) > 0) {
                while ($row = mysqli_fetch_assoc($flowers_data_set)) {
                    $flower_id = $row['flower_id'];


                    $check_query = "SELECT * FROM flower_categories WHERE flower_id='$flower_id' AND category_id='$category_id' LIMIT 1";
                    $check_result = mysqli_query($connection, $check_query);
                    $is_checked = mysqli_num_rows($check_result) > 0;

                    $flowers[] = [
                        'flower_id' => $flower_id,
                        'flower_name' => $row['flower_name'],
                        'dir_path' => $row['dir_path'],
                        'is_checked' => $is_checked
                    ];
                }
            }
        }
        ?>

        <?php if (isset($category_name) && isset($flowers)): ?>

            <form action="flowers.php" method="post">

                <h3>Select a flowers - Category Name: <?php echo htmlspecialchars($category_name); ?></h3><br>

                <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category_id); ?>"/>

                <?php foreach ($flowers as $flower): ?>

                    <div class="image">

                        <input type="checkbox"
                               name="flower_id_array[]"
                               value="<?php echo htmlspecialchars($flower['flower_id']); ?>"



                            <?php
                            echo "class='select'";
                            echo $flower['is_checked'] ? 'checked' : '';
                            ?>
                        >

                        <b><label class="select"><?php echo htmlspecialchars($flower['flower_name']); ?></label></b>

                       <br> <img src="../../<?php echo htmlspecialchars($flower['dir_path']); ?>" alt="No image" width="150px" height="150px"/><br><br><br>

                    </div>

                <?php endforeach; ?>

                <div class='center-container'>
                <button type="submit" name="submit_flowers_categories" class='add'>Insert</button>
                </div>
            </form>
        <?php endif; ?>

    </div>
</div>

</body>
</html>

<?php mysqli_close($connection) ?>
