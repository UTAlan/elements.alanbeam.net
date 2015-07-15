<?php
if(!empty($_POST['username']) && !empty($_POST['password'])) {
  require_once("includes/config.php");
  
  $username = $db->real_escape_string($_POST['username']);
  $password = $db->real_escape_string(sha1($_POST['password']));
  
  $user_result = $db->query("SELECT id, username, admin FROM users WHERE username = '$username' AND password = '$password'");
  if($user_result->num_rows == 1) {
    $_SESSION['user'] = $user_result->fetch_assoc();
    $auth_result = $db->query("SELECT t.name FROM users_auth a LEFT JOIN users_auth_types t ON t.id = a.type_id WHERE a.user_id = " . $_SESSION['user']['id']);
    $_SESSION['user']['auth'] = array();
    while($row = $auth_result->fetch_assoc()) {
      $_SESSION['user']['auth'][$row['name']] = 1;
    }

    $db->query("UPDATE users SET ip = '" . $_SERVER['REMOTE_ADDR'] . "', last_login = NOW() WHERE id = '" . $_SESSION['user']['id'] ."'");
  } else {
    header("Location: index.php?invalid=1");
    die();
  }
}

header("Location: index.php");
die();