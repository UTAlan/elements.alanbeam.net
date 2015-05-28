<?php
require_once("includes/config.php");

$code_arr = explode(' ', $_GET['code']);
$code_qty = array();
foreach($code_arr as $c) {
	if(empty($code_qty[$c])) { 
		$code_qty[$c] = 1;
	} else {
		$code_qty[$c]++;
	}
}

$code = str_replace(" ", "', '", $db->real_escape_string($_GET['code']));
$results = $db->query("SELECT name, code FROM cards_card WHERE code IN ('" . $code . "') ORDER BY code");
$html = "";
while($r = $results->fetch_assoc()) {
	$html .= "<tr>";
	$html .= "<td>" . $r['name'] . "</td>";
    $html .= "<td align=\"center\">" . $code_qty[$r["code"]] . "</td>";
    $html .= "<td align=\"center\"><a class=\"remove_link\" id=\"remove_" . $r["code"] . "\" href=\"#\"><img src=\"images/delete.png\" /></a></td>";
    $html .= "<td align=\"center\"><a class=\"add_link\" id=\"add_" . $r["code"] . "\" href=\"#\"><img src=\"images/add.png\" /></a></td>";
	$html .= "</tr>";
}

$html .= '<script>
$(".remove_link").click(function() {
	var c = $(this).attr("id").substring(7);
    
	code = code.replace(c, "");
	code = code.replace("  ", " ");
	
	sortCode();
	$("#deckCode").val(code);
	updateImage();
	buildTable();

    return false;
}); 
$(".add_link").click(function() {
	var c = $(this).attr("id").substring(4);
    
	code = code.replace(c, c + " " + c);
	
	$("#deckCode").val(code);
	updateImage();
	buildTable();

    return false;
});
</script>';

echo $html;