<?php
session_start();
include_once "config.php";

if(isset($_FILES['newImage'])){
    $unique_id = $_POST['unique_id'];  // Stellen Sie sicher, dass diese ID validiert wird.
    $currentImage = $_POST['currentImage'];
    $newImage = $_FILES['newImage'];

    // Pfad für das hochgeladene Bild
    $uploadPath = "images/" . $currentImage;

    // Bild-Upload und -Ersetzung
    if(move_uploaded_file($newImage['tmp_name'], $uploadPath)){
        // Erfolg, zurück zur Benutzerseite
        header("Location: ../users.php");
    } else {
        // Fehler beim Hochladen
        echo "Fehler beim Hochladen des Bildes";
    }
} else {
    header("Location: ../users.php");
}
?>
