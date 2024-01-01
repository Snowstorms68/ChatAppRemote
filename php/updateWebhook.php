<?php
session_start();
include_once "config.php";

if(isset($_SESSION['unique_id']) && isset($_POST['webhook'])){
    $unique_id = $_SESSION['unique_id'];
    $webhook = $_POST['webhook'];

    // Update the user's webhook URL
    $sql = "UPDATE users SET webhook = '{$webhook}' WHERE unique_id = {$unique_id}";
    $query = mysqli_query($conn, $sql);
    
    if($query){
        echo "Webhook updated successfully";
    } else {
        echo "Failed to update webhook";
    }
} else {
    echo "Unauthorized or Invalid Request";
}
?>
