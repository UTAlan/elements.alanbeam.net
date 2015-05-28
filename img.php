<?php
$dbh = mysql_connect("mysql.alanbeam.net", "alon10", "qwer1234");
mysql_select_db("quantum_index", $dbh);

if(!empty($_GET['card_id']))
{
  $img = mysql_fetch_array(mysql_query("SELECT img FROM cards_card WHERE id = {$_GET['card_id']}"));
}
else if(!empty($_GET['mark_id']))
{
  $img = mysql_fetch_array(mysql_query("SELECT img FROM cards_mark WHERE id = {$_GET['mark_id']}"));
}

header('Content-type: image/png');
echo base64_decode($img["img"]);
