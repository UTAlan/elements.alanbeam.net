<?php
require_once("includes/config.php");
$Config['page']['title'] = 'Home';
require_once("includes/header.php");
?>

      <?php if(empty($_SESSION['user'])) { ?>
      <section id="login-page" >
        <div class="container">
          <div class="row">
            <div class="col-sm-4 col-sm-offset-1">
              <form id="main-login-form" class="login-form" name="login-form" method="post" action="login.php">
                <h2>Login</h2>
                <?php if(!empty($_GET['invalid'])) { ?>
                <div class="row error">
                  <div class="col-xs-12">
                    <p class="error">Invalid username or password. Please try again.</p>
                  </div>
                </div>
                <?php } ?>
                <div class="form-group">
                  <label>Username</label>
                  <input type="text" name="username" class="form-control" required="required" />
                </div>
                <div class="form-group">
                  <label>Password</label>
                  <input type="password" name="password" class="form-control" required="required" />
                </div>               
                <div class="form-group">
                  <button type="submit" name="submit" class="btn btn-primary btn-lg" required="required">Login</button>
                </div>
              </form>
            </div>
            <div class="col-sm-4 col-sm-offset-2">
              <form id="main-register-form" class="register-form" name="register-form" method="post" action="register.php">
                <h2>Register</h2>
                <?php if(!empty($_GET['register_invalid'])) { ?>
                <div class="row error">
                  <div class="col-xs-12">
                    <p class="error">Error retrieving username. Please try again.</p>
                  </div>
                </div>
                <?php 
                } 
                if(!empty($_GET['register_success'])) { 
                ?>
                <div class="row success">
                  <div class="col-xs-12">
                    <p class="error">Success! Please check your email to complete registration.</p>
                  </div>
                </div>
                <?php } ?>
                <div class="form-group">
                  <label>Username</label>
                  <input type="text" name="username" class="form-control" required="required" />
                </div>      
                <div class="form-group">
                  <button type="submit" name="submit" class="btn btn-primary btn-lg" required="required">Register</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>
      <?php } else { ?>
      <section id="login-page" >
        <div class="container">
          <div class="row">
            <div class="col-sm-4 col-sm-offset-4">
              <h2>Welcome!</h2>
              <p>Please choose an option from the menu above.</p>
            </div>
          </div>
        </div>
      </section>
      <?php } ?>

<?php
require_once("includes/footer.php");