<?php 
session_start();
if(isset($_SESSION['unique_id'])){
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = mysqli_real_escape_string($conn, $_POST['incoming_id']);
    $output = "";

    // Funktion, um Nachrichten zu löschen, die älter als 2 Tage sind
    function deleteOldMessages($conn) {
        $deleteQuery = "DELETE FROM messages WHERE msg_time < NOW() - INTERVAL 2 DAY";
        $conn->query($deleteQuery);
    }

    // Alte Nachrichten löschen, bevor neue abgerufen werden
    deleteOldMessages($conn);

    // Überprüfen, ob eine Session bereits existiert und Nachrichten als gelesen markieren
    $check_session = "SELECT * FROM chat_sessions WHERE (user1_id = {$outgoing_id} AND user2_id = {$incoming_id}) OR (user1_id = {$incoming_id} AND user2_id = {$outgoing_id})";
    $session_result = mysqli_query($conn, $check_session);
    if(mysqli_num_rows($session_result) == 0){
        // Erstellen einer neuen Session, wenn sie noch nicht existiert
        $insert_session = "INSERT INTO chat_sessions (user1_id, user2_id) VALUES ({$outgoing_id}, {$incoming_id})";
        mysqli_query($conn, $insert_session);
    } else {
        // Nachrichten als gelesen markieren und Zeitstempel aktualisieren
        $update_query = "UPDATE messages SET is_read = 1, read_at = NOW() WHERE incoming_msg_id = {$incoming_id} AND outgoing_msg_id = {$outgoing_id} AND is_read = 0";
        mysqli_query($conn, $update_query);
    }

    // Nachrichten abrufen
    $sql = "SELECT messages.*, users.img FROM messages 
            LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
            WHERE (outgoing_msg_id = {$outgoing_id} AND incoming_msg_id = {$incoming_id})
            OR (outgoing_msg_id = {$incoming_id} AND incoming_msg_id = {$outgoing_id}) 
            ORDER BY msg_id";
    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query) > 0){
        while($row = mysqli_fetch_assoc($query)){
            // Vergleichslogik für Datum
            $msg_timestamp = strtotime($row['msg_time']);
            if(date('Y-m-d') == date('Y-m-d', $msg_timestamp)) {
                $formatted_time = "Today " . date("h:i A", $msg_timestamp);
            } elseif (date('Y-m-d', strtotime('yesterday')) == date('Y-m-d', $msg_timestamp)) {
                $formatted_time = "Yesterday " . date("h:i A", $msg_timestamp);
            } else {
                $formatted_time = date("M d, Y h:i A", $msg_timestamp);
            }
            $small_time = '<small style="font-size: smaller; display: block; opacity: 0.6;">' . $formatted_time . '</small>';

            if($row['outgoing_msg_id'] === $outgoing_id){
                // Symbol für den Lesestatus
                $statusSymbol = $row['is_read'] ? '<i class="fas fa-check-double status-symbol read"></i>' : '<i class="fas fa-check status-symbol not-read"></i>';
                $output .= '<div class="chat outgoing">
                            <div class="details">
                                <p>'. $row['msg'] . $small_time . '</p>' . $statusSymbol . '
                            </div>
                            </div>';
            } else {
                $output .= '<div class="chat incoming">
                            <img src="php/images/'.$row['img'].'" alt="Profile Image">
                            <div class="details">
                                <p>'. $row['msg'] . $small_time . '</p>
                            </div>
                            </div>';
            }
        }
    } else {
        $output .= '<div class="text">No messages are available. Once you send a message, it will appear here.</div>';
    }
    echo $output;
} else {
    header("location: ../login.php");
}
?>
