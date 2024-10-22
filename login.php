<?php
global $connection;

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once __DIR__ . '/Function/function.php';
include_once __DIR__ . '/Connection/connection.php';

$errors = array();

try{
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){


        $email = user_input($_POST['email']);
        $password = user_input($_POST['password']);


        if(empty($email) || empty($password)){
            $errors[] = 'Email or Password is Empty';
        }elseif(!(preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email))){
            $errors[] = "Email is not valid";
        }


        $email = mysqli_real_escape_string($connection, $email);
        $password = mysqli_real_escape_string($connection, $password);


        if (count($errors) == 0){

            try{
                $password = sha1($password);
                $query = "SELECT * From users WHERE email='$email' AND password='$password' LIMIT 1 ;";

                $query = mysqli_query($connection, $query);

                logger("INFO", "Retrieve data from Users table Successfully");

                if (mysqli_num_rows($query) == 1){
                    $data = mysqli_fetch_assoc($query);

                    $user_id =  $data['user_id'];

                    $check_query = "SELECT * FROM loyalty_users WHERE  user_id='$user_id'";
                    $check_query = mysqli_query($connection, $check_query);



                    $token = bin2hex(random_bytes(16));
                    // Data is added to the session
                    /*$_SESSION['user'] = [
                        'user_id'=> $data['user_id'],
                        'user_name'=> $data['user_name'],
                        'email'=> $data['email'],
                        'mobile'=> $data['mobile'],
                        'token'=> $token,
                        'islogin'=> true
                    ]; */
                    $_SESSION['user']['user_id'] =  $data['user_id'];
                    $_SESSION['user']['user_name'] =  $data['user_name'];
                    $_SESSION['user']['email'] =  $data['email'];
                    $_SESSION['user']['mobile'] =  $data['mobile'];
                    $_SESSION['user']['token'] =  $token;
                    $_SESSION['user']['islogin'] =  true;

                    if(mysqli_num_rows($check_query)>0){
                        $loyalty_data = mysqli_fetch_assoc($check_query);
                        $loyalty_id = $loyalty_data['loyalty_id'];
                        $loyalty_points = $loyalty_data['points_blance'];
                        $_SESSION['user']['loyalty_id'] = $loyalty_id;
                        $_SESSION['user']['points_blance'] = $loyalty_points;
                    }

                    $username = $data['user_name'];


                    setcookie('token',$token, time()+ 3600*2, "/");


                    logger("INFO", "User : $username Login Successfully. Redirect to the Index page.");
                    header("Location: index.php");
                    exit();

                }else{
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
    <link rel="stylesheet" href="style/login.css">
</head>
<body>

<div class="container">
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
        <?php

        if (isset($_GET['UserRegister'])){
            $Username = $_GET['Username'];
            echo "<div class='welcome-box'><b> Hello $Username Please Login </b></div>";
        }


        if (count($errors) > 0){
            echo "<div id='error-box'> $errors[0] </div>";
        }
        ?>
        <div class="form-box">
            <div class = "formHead">
                <h1>Sign In</h1>
            </div>

            <label for="email"></label>
            <input type="email" name="email" id="email" placeholder="Email" required/><br><br>

            <label for="password"></label>
            <input type="password" name="password" id="password" placeholder="Password" required/><br><br>

            <button id="submit-btn" type="submit">Login</button><br><br>

            <a href="register.php" id="link">Create a New Account</a>
        </div>
    </form>
</div>

</body>
</html>
