<?php
global $connection;

session_start();
error_reporting(E_ALL);

include_once "../../Connection/connection.php";
include_once "../../Function/function.php";

if (isset($_POST['delete'])) {
    $supplier_id = $_POST['suplier_id'];

    $delete_query = "DELETE FROM supliers WHERE suplier_id = ?";
    $stmt = mysqli_prepare($connection, $delete_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $supplier_id);
        mysqli_stmt_execute($stmt);

        if (mysqli_stmt_affected_rows($stmt) > 0) {

            $_SESSION['message'] = "Supplier Deleted Successfully!";
        } else {
            $_SESSION['message'] = "Failed to Delete Supplier!";
        }

        mysqli_stmt_close($stmt);
    }

    header("Location: suplier_details.php");
    exit();
}
?>

