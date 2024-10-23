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

                $query = "SELECT * FROM admin WHERE email='$email' AND password='$password' LIMIT 1 ;";

                $result = mysqli_query($connection, $query);

                if(mysqli_num_rows($result) == 1){

                    $token = bin2hex(random_bytes(16));

                    $data = mysqli_fetch_assoc($result);

                    $_SESSION['admin'] = [
                        'admin_id'=> $data['admin_id'],
                        'admin_name'=> $data['admin_name'],
                        'email' => $data['email'],
                        'token'=> $token,
                        'islogin' => true
                    ];
                    

                    setcookie('token',$token, time()+ 3600*2 , '/');
                    $admin_name = $data['admin_name'];

                    logger("INFO", "Admin: $admin_name login successfully. Redirect to the admin_panel");
                    header("Location: admin_panel.php");
                }else{
                    
                    $errors[] = "Invalid Username or Password";
                }

            }catch(Exception $e){

                logger("ERROR", $e->getMessage());
            }
        }
    }

}catch(Exception $e){

    logger("ERROR", $e ->getMessage());
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="adminLogin.css">
</head>
<body>

    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">

            <?php

                if(count($errors) > 0){
                    echo "<div id='error-box'> $errors[0] </div>";
                }
            
            
            ?>
            <div class="form-box">
                <div class = "formHead">
                    <h1>Admin Login</h1>
                </div>

            <label for="email"></label>
            <input type="email" name="email" id="email" placeholder="Email" required/><br><br>

            <label for="password"></label>
            <input type="password" name="password" id="password" placeholder="Password" required/><br><br>

            <button id="submit-btn" type="submit">Login</button><br><br>

            </div>

        </form>
    </div>
    
</body>
</html>