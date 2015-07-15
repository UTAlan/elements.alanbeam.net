<?php 
require_once("config.php");

$term = trim(strip_tags($_GET['term']));

$row_set = array();
$result = $db->query("SELECT id, username AS value FROM users WHERE username LIKE '%".$term."%'");
while ($row = $result->fetch_assoc()) {
  $row['value']=htmlentities(stripslashes($row['value']));
  $row['id']=(int)$row['id'];
  $row_set[] = $row;
}
echo json_encode($row_set);