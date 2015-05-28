<?php
require_once("includes/config.php");

if($_GET['complex'] == "false") {
	$results = $db->query("SELECT name FROM cards_card WHERE name LIKE '%" . $_GET['query'] . "%' ORDER BY name");

	$cards = array();

	while($r = $results->fetch_assoc()) {
		$cards[] = $r['name'];
	}

	echo json_encode($cards);
} else {
	$results = $db->query("SELECT code FROM cards_card WHERE name = '" . $_GET['query'] . "' ORDER BY name LIMIT 1");

	if($results && $results->num_rows > 0) {
		$r = $results->fetch_assoc();
		echo $r['code'];
	}
}