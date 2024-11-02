<?php

global $connection;
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/../Function/function.php';
include_once __DIR__ . '/../Connection/connection.php';

try {

    $errors = array();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        $username = user_input($_POST['username']);
        $email = user_input($_POST['email']);
        $password = user_input($_POST['password']);
        $mobile = user_input($_POST['mobile']);

        if (empty($username) || empty($email) || empty($password) || empty($mobile)) {

            $errors[] = 'All fields are required';

        } elseif (!(preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email))) {

            $errors[] = "Email is not valid";

        } elseif (!(ctype_digit($mobile))) {

            $errors[] = "Mobile number is not valid";

        } elseif (strlen($username) < 4 || strlen($password) < 5) {

            $errors[] = "Username or password is too short";
        }

        try{
            $email = mysqli_real_escape_string($connection,$email);
            $mobile =  mysqli_real_escape_string($connection,$mobile);

            $query = "SELECT * FROM supliers WHERE email='$email' OR mobile='$mobile';";

            $result = mysqli_query($connection, $query);
            if (mysqli_num_rows($result) > 0) {

                $errors[] = "Email or Mobile already exits";
            }
            logger("WARNING", "$email or $mobile is already taken");

        }catch(Exception $e){
            logger("ERROR", $e->getMessage());
        }

        if (count($errors) == 0) {

            $password = sha1($password);


            $username = mysqli_real_escape_string($connection, $username);
            $email = mysqli_real_escape_string($connection, $email);
            $password = mysqli_real_escape_string($connection, $password);
            $mobile = mysqli_real_escape_string($connection, $mobile);

            $query = "INSERT INTO supliers (supplier_username, email, password, mobile) VALUES ('$username', '$email', '$password', '$mobile')";

            try {

                if (mysqli_query($connection, $query)) {
                    logger("INFO", "register_suplier.php : Supplier data Inserted Successfully");


                    header("Location: login_suplier.php?Register=true&Username=$username");
                    exit();
                } else {
                    throw new Exception("Query failed: " . mysqli_error($connection));
                }
            } catch (Exception $e) {
                logger("ERROR", "register_suplier.php : " . $e->getMessage());
            }
        }
    }
} catch (Exception $e) {
    logger("ERROR", "register_suplier.php: " . $e->getMessage());
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="script/register.js"></script>
    <link rel="stylesheet" href="../style/register.css">
</head>
<body>

<div class="container">
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

        <?php
        if (count($errors) > 0) {
            echo "<div id='error-box'> $errors[0] </div>";
        }
        ?>

        <div class="form-box">

            <div class = "formHead">
                <h1>Create Account</h1>
            </div>

            <label for="username"></label>
            <input type="text" name="username" id="username" placeholder="Username" required/><br><br>

            <label for="email">
                <input type="email" name="email" id="email" placeholder="Email" required/>
            </label><br><br>

            <label for="mobile">
                <input type="number" name="mobile" id="mobile" placeholder="Mobile" required/>
            </label><br><br>

            <label for="password">
                <input type="password" name="password" id="password" placeholder="Password" required/>
            </label><br><br>

            <button id="submit-btn" type="submit">Register</button><br><br>

        </div>
    </form>

</div>
</body>
</html>