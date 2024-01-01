<?php
    session_start();
    include_once "config.php";

    $outgoing_id = $_SESSION['unique_id'];
    $searchTerm = mysqli_real_escape_string($conn, $_POST['searchTerm']);

    // Ändern Sie die SQL-Abfrage, um nach Nickname zu suchen
    $sql = "SELECT * FROM users WHERE NOT unique_id = {$outgoing_id} AND nickname LIKE '%{$searchTerm}%' ";
    $output = "";
    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query) > 0){
        include_once "data.php";
    }else{
        $output .= 'No user found related to your search term';
    }
    echo $output;
?>
