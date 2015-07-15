<?php 
if(!empty($_POST['username'])) {
  require_once("includes/config.php");

  $username = $db->real_escape_string($_POST['username']);

  $user_result = $db->query("SELECT id, password, email, hash FROM users WHERE username = '$username'");
  if($user_result->num_rows == 1) {
    $user = $user_result->fetch_assoc();
    if(!empty($user['password'])) {
      $register = 0;
    } else if(!empty($user['hash']) && !empty($user['email'])) {
      $skip_email = 1;
      $register = 1;
      $email = $user['email'];
      $hash = $user['hash'];
    } else {
      $register = 1;
    }
  } else {
    $ip = $_SERVER['REMOTE_ADDR'];
    $db->query("INSERT INTO users SET username = '$username', admin = 0, ip = '$ip'");
    $user_result = $db->query("SELECT id FROM users WHERE username = '$username'");
    $user = $user_result->fetch_assoc();
    $register = 1;
  }

  if($register) {
    if(!$skip_email) {
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $Config['forum']['loginUrl']);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, 'user='.$Config['forum']['username'].'&passwrd='.$Config['forum']['password'].'&cookielength=-1');
      curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $store = curl_exec($ch);

      curl_setopt($ch, CURLOPT_URL, 'http://elementscommunity.org/forum/profile/?user=' . $username);
      $html = curl_exec($ch);
      curl_close($ch);

      $dom = new DOMDocument;
      $dom->loadHTML($html);
      foreach($dom->getElementsByTagName('a') as $link) {
        if(strpos($link->getAttribute('href'), 'emailuser') !== false) {
          $email = $link->textContent;
          break;
        }
      }
    }

    if(!empty($email)) {
      if(empty($hash)) {
        $hash = sha1($username . time() . $email);
        $db->query("UPDATE users SET email = '$email', hash = '$hash' WHERE id = " . $user['id']);
      }

      require_once 'vendor/swiftmailer/swiftmailer/lib/swift_required.php';
      $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, "ssl")
        ->setUsername($Config['gmail']['username'])
        ->setPassword($Config['gmail']['password']);

      $mailer = Swift_Mailer::newInstance($transport);

      $message = Swift_Message::newInstance('Elements Community - Council Voting Registration')
        ->setFrom(array($Config['gmail']['username'] => 'Elements Community'))
        ->setTo(array($email))
        ->setBody("Thanks for registering! Please go to the following URL to create a password: " . $Config['site']['hostname'] . "registered.php?username=$username&hash=$hash");

      $result = $mailer->send($message);
      if($result) {
        $success = 1;
      }
    }

    if(!$success) {
      header("Location: index.php?register_invalid=1");
      die();
    }
  }

  header("Location:index.php?register_success=1");
  die();
}

header("Location: index.php");
die();