<?php
session_start();
include_once "config.php";

if(isset($_SESSION['unique_id'])) {
    $outgoing_id = $_SESSION['unique_id'];
    $output = "";

    // SQL-Abfrage, um Chat-Partner-IDs zu holen
    $sql = "SELECT * FROM chat_sessions WHERE user1_id = '$outgoing_id' OR user2_id = '$outgoing_id'";
    $chat_sessions_query = mysqli_query($conn, $sql);

    $chats = []; // Temporäres Array zum Speichern von Chat-Daten

    if(mysqli_num_rows($chat_sessions_query) > 0) {
        while($chat_session_row = mysqli_fetch_assoc($chat_sessions_query)) {
            $chat_partner_id = ($chat_session_row['user1_id'] == $outgoing_id) ? $chat_session_row['user2_id'] : $chat_session_row['user1_id'];

            // Holen der Benutzerinformationen
            $user_query = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = '$chat_partner_id'");
            if(mysqli_num_rows($user_query) > 0) {
                $user_row = mysqli_fetch_assoc($user_query);
                $result = "No message available";
                $msg_time = "";
                $last_msg_timestamp = 0; // Timestamp der letzten Nachricht

                // Holen der letzten Nachricht mit diesem Benutzer, wenn vorhanden
                $message_query = mysqli_query($conn, "SELECT * FROM messages WHERE (incoming_msg_id = {$user_row['unique_id']}
                        OR outgoing_msg_id = {$user_row['unique_id']}) AND (outgoing_msg_id = {$outgoing_id} 
                        OR incoming_msg_id = {$outgoing_id}) ORDER BY msg_id DESC LIMIT 1");
                
                if(mysqli_num_rows($message_query) > 0) {
                    $message_row = mysqli_fetch_assoc($message_query);
                    $result = $message_row['msg'];
                    $last_msg_timestamp = strtotime($message_row['msg_time']);
                }

                // Speichern der Chat-Daten im Array
                $chats[] = [
                    'user_row' => $user_row,
                    'message' => $result,
                    'msg_timestamp' => $last_msg_timestamp,
                    'chat_session_row' => $chat_session_row,
                    'message_row' => $message_row ?? null // Sicheres Speichern der Nachrichtenzeile, wenn vorhanden
                ];
            }
        }

        // Sortieren der Chats
        usort($chats, function ($a, $b) {
            if ($a['message'] == "No message available" && $b['message'] != "No message available") {
                return 1; // Chats ohne Nachrichten ans Ende
            } elseif ($b['message'] == "No message available" && $a['message'] != "No message available") {
                return -1; // Chats ohne Nachrichten ans Ende
            }
            return $b['msg_timestamp'] - $a['msg_timestamp']; // Sonst nach Timestamp sortieren
        });

        // Erzeugen der Ausgabe nach sortierten Chats
        foreach($chats as $chat) {
            $user_row = $chat['user_row'];
            $result = $chat['message'];
            $message_row = $chat['message_row'];
            $msg_timestamp = $chat['msg_timestamp'];
            $msg_time = ""; // Initialisiert ohne Wert
            
            if($result != "No message available") { // Zeitberechnung nur, wenn Nachrichten vorhanden sind
                $current_time = time();
                $time_diff = $current_time - $msg_timestamp;

                if($time_diff < 60) {
                    $msg_time = "Just now";
                } elseif ($time_diff < 3600) {
                    $minutes = floor($time_diff / 60);
                    $msg_time = $minutes . ($minutes == 1 ? " minute ago" : " minutes ago");
                } elseif ($time_diff < 86400) {
                    $hours = floor($time_diff / 3600);
                    $msg_time = $hours . ($hours == 1 ? " hour ago" : " hours ago");
                } elseif (date('Y-m-d', strtotime('yesterday')) == date('Y-m-d', $msg_timestamp)) {
                    $msg_time = "Yesterday " . date("h:i A", $msg_timestamp);
                } else {
                    $msg_time = date("M d, Y h:i A", $msg_timestamp);
                }
            }

            (strlen($result) > 28) ? $msg = substr($result, 0, 28) . '...' : $msg = $result;
            $you = ($outgoing_id == $message_row['outgoing_msg_id']) ? "You: " : "";

            $msg_with_time = $msg . ($msg_time ? '<br><small>' . $msg_time . '</small>' : ''); 

            $offline = ''; // Bestimmen Sie, wie Sie den Offline-Status festlegen möchten.

            // Chat-Eintrag erstellen
            $output .= '<div class="chat-entry">';
            $output .= '<a href="chat.php?user_id='. $user_row['unique_id'] .'" class="chat-link">
                        <div class="content">
                        <img src="php/images/'. $user_row['img'] .'" alt="">
                        <div class="details">
                            <span>'. ucfirst($user_row['nickname']) .'</span>
                            <p>'. $you . $msg_with_time .'</p>
                        </div>
                        </div>
                        <div class="status-dot '. $offline .'"><i class="fas fa-circle"></i></div>
                        </a>';
            $output .= '<div class="delete-chat" onclick="event.stopPropagation(); deleteChat(' . $chat['chat_session_row']['session_id'] . ')"><i class="fas fa-times"></i></div>';
            $output .= '</div>'; // Schließt chat-entry
        }
    } else {
        $output .= 'No chats found';
    }

    echo $output;
} else {
    echo "Fehler: Benötigte Daten nicht erhalten.";
}
?>
