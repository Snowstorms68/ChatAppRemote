<?php 
  session_start();
  include_once "php/config.php";
  if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
  }
?>
<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="chat-area">
      <header>
        <?php 
          $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
          $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$user_id}");
          if(mysqli_num_rows($sql) > 0){
            $row = mysqli_fetch_assoc($sql);
            // Nachrichten als gelesen markieren und Zeitstempel aktualisieren
            $update_query = "UPDATE messages SET is_read = 1, read_at = NOW() WHERE incoming_msg_id = {$_SESSION['unique_id']} AND outgoing_msg_id = {$user_id} AND is_read = 0";
            mysqli_query($conn, $update_query);
          }else{
            header("location: users.php");
          }
        ?>
        <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <img src="php/images/<?php echo $row['img']; ?>" alt="">
        <div class="details">
          <span><?php echo $row['nickname'] ?></span>
          <p><?php echo $row['status']; ?></p>
       </div>
      </header>

      <div class="chat-box">
        <!-- Chat Box Inhalt wird hier geladen -->
      </div>
      <form action="#" class="typing-area">
          <button class="delete-btn" disabled><i class="fas fa-trash"></i></button>
          <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
          <input type="text" name="message" class="input-field" placeholder="Type a message here..." autocomplete="off">
          <button class="send-btn"><i class="fab fa-telegram-plane"></i></button>
      </form>

    </section>
  </div>

  <script src="javascript/chat.js"></script>

</body>
</html>