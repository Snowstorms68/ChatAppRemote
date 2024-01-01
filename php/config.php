<?php
  $hostname = "178.254.2.86";  // Die IP-Adresse des Datenbankservers
  $username = "chatuser";       // Der neue Benutzername
  $password = "SNOWSTORMS0110!db";  // Das Passwort
  $dbname = "chatapp";        // Der Name der Datenbank

  $conn = mysqli_connect($hostname, $username, $password, $dbname);
  if(!$conn){
    echo "Database connection error".mysqli_connect_error();
  }
?>
