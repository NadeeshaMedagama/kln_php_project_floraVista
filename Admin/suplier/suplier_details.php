<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "../../Connection/connection.php";
include_once "../../Function/function.php";

if (!isset($_SESSION['admin']['islogin']) || $_SESSION['admin']['islogin'] != true){

    header("Location: ../admin.php");
}



$query = "SELECT * FROM supliers WHERE verify=true";

$result = mysqli_query($connection,$query);

echo "<div class='container'>
            <h3>Registered Supliers Details</h3> <br>
            
                <table border='1'>
                    <tr>
                        <th>suplier_id</th>
                        <th>suplier_name</th>
                        <th>suplier_email</th>
                        <th>suplier Mobile</th>
                    </tr>";

if(mysqli_num_rows($result)>0){
    while($row = mysqli_fetch_assoc($result)){
        $suplier_id= $row['suplier_id'];
        $suplier_name= $row['suplier_username'];
        $email = $row['email'];
        $mobile = $row['mobile'];

        echo "<tr>
                                    <td>$suplier_id</td>
                                    <td>$suplier_name</td>
                                    <td>$email</td>
                                    <td>$mobile</td>
                                </tr>";
    }
}



echo "</table>
          </div>";

?>
