<?php
    session_start();
    include_once "config.php";

    $outgoing_id = $_SESSION['unique_id'];
    $searchTerm = mysqli_real_escape_string($conn, $_POST['searchTerm']);

    // Ändern der SQL-Abfrage basierend darauf, ob ein Suchbegriff vorhanden ist oder nicht
    if ($searchTerm != "") {
        // Suchbegriff vorhanden: Suche nur Benutzer, die den Suchbegriff im Namen haben
        $sql = "SELECT * FROM users WHERE unique_id IN (
                    SELECT user1_id FROM chat_sessions WHERE user2_id = {$outgoing_id}
                    UNION
                    SELECT user2_id FROM chat_sessions WHERE user1_id = {$outgoing_id}
                ) AND nickname LIKE '%{$searchTerm}%'";
    } else {
        // Kein Suchbegriff: Zeige alle Benutzer
        $sql = "SELECT * FROM users WHERE unique_id IN (
                    SELECT user1_id FROM chat_sessions WHERE user2_id = {$outgoing_id}
                    UNION
                    SELECT user2_id FROM chat_sessions WHERE user1_id = {$outgoing_id}
                )";
    }

    $output = "";
    $query = mysqli_query($conn, $sql);
    if(mysqli_num_rows($query) > 0){
        include_once "data.php";
    } else {
        $output .= 'No user found related to your search term';
    }
    echo $output;
?>
