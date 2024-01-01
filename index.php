<?php 
  session_start();
  if(isset($_SESSION['unique_id'])){
    header("location: users.php");
  }
?>

<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="form signup">
      <header>Chatify - Signup</header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="field input">
            <label>Nickname</label>
            <input type="text" name="nickname" placeholder="Nickname" required>
        </div>
        <div class="field input">
          <label>Password</label>
          <input type="password" name="password" placeholder="Enter new password" required>
          <i class="fas fa-eye"></i>
        </div>
          <div class="field image">
            <!-- Verstecken Sie das ursprüngliche input-Element -->
            <input type="file" id="actual-btn" name="image" accept="image/x-png,image/gif,image/jpeg,image/jpg" required hidden/>
          
            <!-- Benutzerdefinierter Button -->
            
          
            <!-- Span-Element zur Anzeige des ausgewählten Dateinamens -->
            <span id="file-chosen"><button type="button" id="custom-button">Choose a Image</button> No file chosen</span>
          </div>
        <div class="field button">
          <input type="submit" name="submit" value="Signup for Chatify">
        </div>
      </form>
      <div class="link">Already signed up? <a href="login.php">Login now</a></div>
    </section>
  </div>

  <script src="javascript/pass-show-hide.js"></script>
  <script src="javascript/signup.js"></script>

</body>
</html>
