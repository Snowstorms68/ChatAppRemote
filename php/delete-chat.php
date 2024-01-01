<?php 
session_start();
include_once "config.php";

if(isset($_SESSION['unique_id']) && isset($_POST['incoming_id'])) {
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);

    $sql = "DELETE FROM messages WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id}) OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id})";
    $query = mysqli_query($conn, $sql);

    if($query) {
        echo "Alle Nachrichten gelöscht";
    } else {
        echo "Fehler beim Löschen der Nachrichten";
    }
} else {
    header("location: ../login.php");
}
?>