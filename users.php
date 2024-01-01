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
    <section class="users">
      <header>
        <div class="content">
          <?php 
            $sql = mysqli_query($conn, "SELECT * FROM users WHERE unique_id = {$_SESSION['unique_id']}");
            if(mysqli_num_rows($sql) > 0){
              $row = mysqli_fetch_assoc($sql);
            }
          ?>
          <img src="php/images/<?php echo $row['img']; ?>" alt="" id="profile-img" style="cursor: pointer;">
          <form id="image-form" action="php/upload.php" method="post" enctype="multipart/form-data" style="display:none;">
            <input type="file" name="newImage" id="image-input" accept="image/*">
            <input type="hidden" name="currentImage" value="<?php echo $row['img']; ?>">
            <input type="hidden" name="unique_id" value="<?php echo $row['unique_id']; ?>">
          </form>
          <div class="details">
            <span><?php echo ucfirst($row['nickname']); ?></span>
            <p><?php echo $row['status']; ?></p>
          </div>
        </div>

        <button onclick="openWebhookModal()" class="discord-btn"><i class="fab fa-discord"></i></button>

        <div id="webhookModal" class="modal">
          <div class="modal-content">
            <span class="close" onclick="closeWebhookModal()">&times;</span>
            <input type="text" id="webhookUrl" placeholder="Enter Discord Webhook URL">
            <button onclick="saveWebhookUrl('<?php echo $row['unique_id']; ?>')" class="save-webhook-btn"><i class="fas fa-save"></i> Save Webhook URL</button>
          </div>
        </div>
        <div id="modalOverlay" class="modal-overlay"></div>

        <a href="php/logout.php?logout_id=<?php echo $row['unique_id']; ?>" class="logout"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
      </header>
      <div class="navigation">
        <button id="searchBtn" class="searchBtn"><i class="fas fa-search"></i> Search</button>
        <button id="chatsBtn" class="chatsBtn">Chats  <i class="fa-regular fa-comment"></i></button>
      </div>
      
      <!-- Bereich für wichtige Benutzer -->
      <div class="important-users" id="importantUsers" style="display: block;">
        <?php
          $importantUserUniqueIds = ['741716960', '123456789', '987654321']; // Ersetzen Sie dies mit den tatsächlichen unique_ids der wichtigen Benutzer
          $importantUsersSql = "SELECT * FROM users WHERE unique_id IN ('" . implode("','", $importantUserUniqueIds) . "')";
          $importantUsersQuery = mysqli_query($conn, $importantUsersSql);
          if(mysqli_num_rows($importantUsersQuery) > 0){
            while($importantUser = mysqli_fetch_assoc($importantUsersQuery)){
              echo '<a href="chat.php?user_id='. $importantUser['unique_id'] .'" class="user-item">';
              echo '<div class="important-user">';
              echo '<img src="php/images/'. $importantUser['img'] .'" alt="" class="user-img">';
              echo '<span class="user-name">'. ucfirst($importantUser['nickname']) .'</span>';
              echo '</div>';
              echo '</a>';
            }
          } else {
            echo '<div class="no-important-users">No important users found</div>';
          }
        ?>
      </div>




      <div class="search">
        <span class="text">Select a user to start chat</span>
        <input type="text" placeholder="Enter name to search...">
        <button class="search-button"><i class="fas fa-search"></i></button>
      </div>
      <div class="users-list">
        <!-- Benutzerliste oder Chatliste -->
      </div>
    </section>
  </div>

  <script src="javascript/users.js"></script>

</body>
</html>

