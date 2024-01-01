<?php
session_start();
include_once "config.php";

if(isset($_SESSION['unique_id']) && isset($_POST['sessionId'])) {
    $sessionId = mysqli_real_escape_string($conn, $_POST['sessionId']);
    $outgoing_id = $_SESSION['unique_id'];

    // SQL-Abfrage, um den Chat zu löschen
    $sql = "DELETE FROM chat_sessions WHERE session_id = '$sessionId' AND (user1_id = '$outgoing_id' OR user2_id = '$outgoing_id')";
    $query = mysqli_query($conn, $sql);

    if($query) {
        echo "success";
    } else {
        echo "Error: Could not delete the chat.";
    }
} else {
    echo "Error: Required data not received.";
}
?>
