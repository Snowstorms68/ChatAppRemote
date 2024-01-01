<?php 
session_start();
include_once "config.php";
$nickname = mysqli_real_escape_string($conn, $_POST['nickname']); 
$password = mysqli_real_escape_string($conn, $_POST['password']);

if(!empty($nickname) && !empty($password)){
    // Ändern Sie die SQL-Abfrage, um den Benutzer anhand des Nicknames zu finden
    $sql = mysqli_query($conn, "SELECT * FROM users WHERE nickname = '{$nickname}'");
    if(mysqli_num_rows($sql) > 0){
        $row = mysqli_fetch_assoc($sql);
        $user_pass = $password; // Kein md5 hier, da das Passwort gehasht gespeichert wird
        $enc_pass = $row['password'];
        if(password_verify($user_pass, $enc_pass)){ // Verwenden Sie password_verify
            $status = "Active now";
            $sql2 = mysqli_query($conn, "UPDATE users SET status = '{$status}' WHERE unique_id = {$row['unique_id']}");
            if($sql2){
                $_SESSION['unique_id'] = $row['unique_id'];
                echo "success";
            }else{
                echo "Something went wrong. Please try again!";
            }
        }else{
            echo "Nickname or Password is Incorrect!";
        }
    }else{
        echo "$nickname - This nickname does not exist!";
    }
}else{
    echo "All input fields are required!";
}
?>
