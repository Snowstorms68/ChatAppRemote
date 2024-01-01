<?php
include 'config.php'; // Einbeziehung der Konfigurationsdatei für die Datenbankverbindung

session_start();

if (isset($_POST['incoming_id']) && isset($_SESSION['unique_id'])) {
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $outgoing_id = mysqli_real_escape_string($conn, $_SESSION['unique_id']);

    // Zählen Sie Nachrichten zwischen zwei Benutzern
    $query = "SELECT COUNT(*) as message_count FROM messages WHERE (incoming_msg_id = '$incoming_id' AND outgoing_msg_id = '$outgoing_id') OR (incoming_msg_id = '$outgoing_id' AND outgoing_msg_id = '$incoming_id')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        echo $row['message_count'] > 0 ? 'true' : 'false';
    } else {
        echo 'false';
    }
} else {
    echo 'false';
}
?>
