<?php
session_start();
include_once "config.php";

if(isset($_POST['nickname']) && isset($_POST['password'])){
    $nickname = mysqli_real_escape_string($conn, $_POST['nickname']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if(!empty($nickname) && !empty($password)){
        // Überprüfen, ob der Nickname bereits existiert
        $sql = $conn->prepare("SELECT * FROM users WHERE nickname = ?");
        $sql->bind_param("s", $nickname);
        $sql->execute();
        $result = $sql->get_result();

        if($result->num_rows > 0){
            echo "$nickname - This nickname already exists!";
        } else {
            if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){
                $img_name = $_FILES['image']['name'];
                $img_type = $_FILES['image']['type'];
                $tmp_name = $_FILES['image']['tmp_name'];

                $img_explode = explode('.',$img_name);
                $img_ext = strtolower(end($img_explode));

                $extensions = ["jpeg", "png", "jpg"];
                if(in_array($img_ext, $extensions) === true){
                    $types = ["image/jpeg", "image/jpg", "image/png"];
                    if(in_array($img_type, $types) === true){
                        $time = time();
                        $new_img_name = $time.$img_name;
                        move_uploaded_file($tmp_name, "images/".$new_img_name);
                    }
                }
            } else {
                // Standardbild, falls kein Bild hochgeladen wird
                $new_img_name = "default/default.png";
            }

            $status = "Active now";
            $encrypt_pass = password_hash($password, PASSWORD_DEFAULT);
            $ran_id = rand(time(), 100000000); // Stellen Sie sicher, dass dies eine Ganzzahl ist
            $insert_query = $conn->prepare("INSERT INTO users (unique_id, nickname, password, img, status) VALUES (?, ?, ?, ?, ?)");
            $insert_query->bind_param("issss", $ran_id, $nickname, $encrypt_pass, $new_img_name, $status);
            $insert_query->execute();

            if($insert_query){
                $_SESSION['unique_id'] = $ran_id;
                echo "success";
            } else {
                echo "Something went wrong. Please try again!";
            }
        }
    } else {
        echo "All input fields are required!";
    }
} else {
    echo "Invalid request!";
}
?>
