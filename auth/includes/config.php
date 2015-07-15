<?php
session_start(); 

if((!empty($Config['page']['auth']) && empty($_SESSION['user'])) || (!empty($Config['page']['admin']) && empty($_SESSION['user']['admin']))) {
  header("Location: index.php");
  die();
} else if(!empty($Config['page']['auth']) && is_array($Config['page']['auth']) && empty($_SESSION['user']['admin'])) {
  foreach($Config['page']['auth'] as $type=>$val) {
    if(empty($_SESSION['user']['auth'][$type])) {
      header("Location: index.php");
      die();
    }
  }
}

require_once("db.php"); // If this file doesn't exist, copy/paste it from db.template.php and fill in the database details

$db = mysqli_connect($Config['db']['hostname'], $Config['db']['username'], $Config['db']['password'], $Config['db']['database']);