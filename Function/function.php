<?php

function user_input($data){
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}


function cookie_checker_admin(){
    if ($_SESSION['admin']['token'] != $_COOKIE['token'] || !isset( $_COOKIE['token'] )) {
        echo "<script> window.alert('Cookie is expire. Please Login here')</script>";
        header("Location: admin.php");
    }else{
        return true;
    }
}


function info_alert($msg){
    $msg = addslashes($msg);
    echo "<script>window.alert($msg)</script>";
}


function error_alert($msg){
    echo "<script>window.alert($msg)</script>";
}


?>