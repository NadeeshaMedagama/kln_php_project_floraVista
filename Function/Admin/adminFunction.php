<?php

error_reporting(E_ALL);
ini_set("display_errors",1);

include_once '../../Connection/connection.php';
include_once '../../Logger/logger.php';

global $connection;
function get_flowers_categories(){
    global $connection;
    try{
        $query = "SELECT * FROM flowers_category";
        $result = mysqli_query($connection,$query);
        logger("INFO", "get flowers category successfully");

        if (mysqli_num_rows($result) > 0){
            return $result;
        }else{
            return null;
        }
    }catch(Exception $e){
        logger('ERROR', 'get_flowers_categories function ' . $e->getMessage());
        return null;
    }
}

?>