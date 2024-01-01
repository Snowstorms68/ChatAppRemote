<?php
session_start();
include_once "config.php"; // Stellen Sie sicher, dass dieser Pfad korrekt ist

if(isset($_SESSION['unique_id']) && isset($_POST['incoming_id'])) {
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $sender_id = $_SESSION['unique_id']; // ID des Benutzers, der die Nachricht sendet
    
    // Holen des Nicknames des Senders aus der Datenbank
    $userQuery = mysqli_query($conn, "SELECT nickname FROM users WHERE unique_id = {$sender_id}");
    if(mysqli_num_rows($userQuery) > 0){
        $userRow = mysqli_fetch_assoc($userQuery);
        $senderNickname = $userRow['nickname'];
    } else {
        $senderNickname = "Unbekannter Sender"; // Fallback, falls der Nickname nicht gefunden wird
    }

    // Webhook-URL des Empfängers aus der Datenbank holen
    $sql = mysqli_query($conn, "SELECT webhook FROM users WHERE unique_id = {$incoming_id}");
    if(mysqli_num_rows($sql) > 0){
        $row = mysqli_fetch_assoc($sql);
        $webhookUrl = $row['webhook'];
        
        // Sicherstellen, dass eine Webhook-URL vorhanden ist
        if(!empty($webhookUrl)) {
            // Webhook an Discord senden
            $data = json_encode([
                "content" => "Du hast eine Nachricht von {$senderNickname}",
                "username" => "ChatApp Notification"
            ]);

            $ch = curl_init($webhookUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $response = curl_exec($ch);
            // Überprüfen Sie hier den Antwortcode und/oder loggen Sie Fehler
            curl_close($ch);
        }
    }
}
?>
