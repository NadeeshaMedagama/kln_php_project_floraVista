<?php
global $connection;
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/../Function/function.php';
include_once __DIR__ . '/../Connection/connection.php';

$errors = array();

try{
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){


        // Get the user input detail
        $email = user_input($_POST['email']);
        $password = user_input($_POST['password']);

        // From validation
        if(empty($email) || empty($password)){
            $errors[] = 'Email or Password is Empty';
        }elseif(!(preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email))){
            $errors[] = "Email is not valied";
        }



        // Void sql injection
        $email = mysqli_real_escape_string($connection, $email);
        $password = mysqli_real_escape_string($connection, $password);


        if (count($errors) == 0){
            // Check the database valid user
            try{
                $password = sha1($password);
                $query = "SELECT * From supliers WHERE email='$email' AND password='$password'  LIMIT 1 ;";

                $query = mysqli_query($connection, $query);

                logger("INFO", "retrieve data from users table successfully");

                if (mysqli_num_rows($query) == 1){
                    $data = mysqli_fetch_assoc($query);

                    if ($data['verify'] == true){
                        // Genarate unique token
                        $token = bin2hex(random_bytes(16));
                        // Data is added to the session save to associative array
                        $_SESSION['suplier'] = [
                            'suplier_id'=> $data['suplier_id'],
                            'suplier_username'=> $data['suplier_username'],
                            'email'=> $data['email'],
                            'mobile'=> $data['mobile'],
                            'token'=> $token,
                            'islogin'=> true
                        ];


                        $username = $data['suplier_name'];

                        // set the cookie with authentication token
                        setcookie('token',$token, time()+ 3600*2, "/");

                        // Login successfully redirect to the index.php page
                        logger("INFO", "Suplier : $username login successfully. redirect to the suplier_panel page.");
                        header("Location: suplier_panel.php");
                        exit();
                    }else{
                        $suplier_username = $data["suplier_username"];
                        logger("INFO", "$suplier_username is not verified");
                        $errors[] = "Account is Not Verified";
                    }

                }else{
                    logger("WARNING", "suplier login invalid username or password");
                    $errors[] = "Invalid Username or Password";
                }
            }catch(Exception $e){
                logger("ERROR", $e->getMessage());
            }
        }

    }
}catch(Exception $e){
    logger("ERROR", $e->getMessage());
}

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../style/login.css">
</head>
<body>

<div class="container">
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">

        <?php

        if (isset($_GET['Register'])){
            if ($_GET['Register'] == true){
                $Username = $_GET['Username'];
                echo "<div class='welcome-box'><b> Hello $Username Please Login </b></div>";
            }
        }

        if (count($errors) > 0){
            echo "<div id='error-box'> $errors[0] </div>";
        }
        ?>

        <div class="form-box">
        <div class = "formHead">
            <h1>Supplier Sign In</h1>
        </div>

        <input type="email" name="email" id="email"  placeholder="Email" required/><br><br>

        <input type="password" name="password" id="password"  placeholder="Password" required/><br><br>

        <button id="submit-btn" type="submit">Login</button><br><br>

        <a href="register_suplier.php" id="link">Create a new account</a>
    </form>
</div>

</body>
</html>
