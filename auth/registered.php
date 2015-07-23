<?php 
if(!empty($_GET['username']) && !empty($_GET['hash'])) {
  require_once("includes/config.php");

  $username = $db->real_escape_string($_GET['username']);
  $hash = $db->real_escape_string($_GET['hash']);

  $user_result = $db->query("SELECT id FROM users WHERE username = '$username' AND hash = '$hash' AND password IS NULL");
  if($user_result->num_rows == 1) {
    $user = $user_result->fetch_assoc();

    if(!empty($_POST['password']) && !empty($_POST['confirm_password']) && $_POST['password'] == $_POST['confirm_password']) {
      $password = $db->real_escape_string(sha1($_POST['password']));
      $db->query("UPDATE users SET password = '$password' WHERE id = '" . $user['id'] . "'");
    } else {
      $Config['page']['title'] = 'Set Password';
      require_once("includes/header.php");
      ?>

        <section id="login-page" >
          <div class="container">
            <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
              <h2>Set Password</h2>

              <form id="main-registered-form" class="registered-form" name="registered-form" method="post" action="registered.php?username=<?php echo $username; ?>&hash=<?php echo $hash; ?>">
                 <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required="required" />
                  </div>  
                  <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required="required" />
                  </div>               
                  <div class="form-group">
                    <button type="submit" name="submit" class="btn btn-primary btn-lg" required="required">Save</button>
                  </div>
              </form>
            </div>
            </div>
          </div>
        </section>

      <?php
      require_once("includes/footer.php");
      die();
    }
  }
}

header("Location: index.php");
die();
