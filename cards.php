<?php
require_once("includes/config.php");

$elements_result = $db->query("SELECT id, name FROM cards_mark WHERE name != 'Mark'");
$element_list = array();
while($e = $elements_result->fetch_assoc()) {
	$element_list[] = $e;
}
$elements_result->close();

$cost_result = $db->query("SELECT DISTINCT(q.amount) AS cost FROM cards_card c LEFT JOIN cards_quanta q ON q.id = c.cost_id ORDER BY q.amount");
$cost_list = array();
while($c = $cost_result->fetch_assoc()) {
	$cost_list[] = $c;
}
$cost_result->close();

$elements = empty($_GET['element']) ? "''" : implode(', ', $_GET['element']);
$upgrade = !isset($_GET['upgrade']) ? "0,1" : ($_GET['upgrade'] == 2 ? '0,1' : $_GET['upgrade']);
$cost = !isset($_GET['cost']) ? "''" : implode(', ', $_GET['cost']);
$order = (empty($_GET['order']) || $_GET['order'] == 'name') ? 'c.name' : 'c.code';

if(isset($_GET['cost'])) {
	$cost = implode(', ', $_GET['cost']);
	$costNull = (in_array(0, $_GET['cost']));
} else {
	$cost = "''";
	$costNull = false;
}

$cards_result = $db->query("SELECT c.id, c.name, c.code, m.name AS mark_name FROM cards_card c LEFT JOIN cards_quanta q ON q.id = c.cost_id LEFT JOIN cards_mark m ON m.id = c.mark_id WHERE c.mark_id IN ($elements) AND c.upgrade IN ($upgrade) AND (q.amount IN ($cost) ".($costNull ? "OR q.amount IS NULL" : "").") ORDER BY $order");
$num_cards = $cards_result->num_rows;
$card_list = array();
while($c = $cards_result->fetch_assoc()) {
	$card_list[] = $c;
}

$Page['title'] = 'Card Database';
$Page['jquery'] = <<<JQUERY

$("#reset").click(function() {
  $("#element_all").attr("checked", "");
  $(".element").attr("checked", "");
  $(".upgrade").attr("checked", "");
  $("#upgrade_both").attr("checked", "checked");
  $("#cost_all").attr("checked", "");
  $(".cost").attr("checked", "");
  
  return false;
});

$("#element_all").click(function() {
  $(".element").attr('checked', $('#element_all').is(':checked'));
});

$("#cost_all").click(function() {
  $(".cost").attr('checked', $('#cost_all').is(':checked'));
});

$("#export_code").focus(function() { 
  this.select(); 
});

JQUERY;

include_once("includes/header.php");
?>

		<div id="code">
        <fieldset>
            <form id="card_database_form" action="" method="get">
            <strong>Element</strong><br />
            <label><input type="checkbox" id="element_all" value="1" /> All</label>
            <?php
            foreach($element_list as $element) {
            	echo '<label><input type="checkbox" class="element" name="element[]" id="element_'. $element['id'] . '" value="' . $element['id'] .'" ';
              	if(!empty($_GET['element']) && in_array($element['id'], $_GET['element'])) {
	                echo 'checked="checked" ';
				}
              	echo '/> <img src="img.php?mark_id=' . $element['id'] . '" alt="' . $element['name'] . '" title="' . $element['name'] . '" /></label> ';
            }
            ?>
            <br /><br />
            
            <strong>Upgraded</strong><br />
            <label><input type="radio" class="upgrade" name="upgrade" id="upgrade_yes" value="1" <?php if(!empty($_GET['upgrade']) && $_GET['upgrade'] == 1) { echo 'checked="checked" '; } ?>/> Yes</label>
            <label><input type="radio" class="upgrade" name="upgrade" id="upgrade_no" value="0" <?php if(isset($_GET['upgrade']) && $_GET['upgrade'] == 0) { echo 'checked="checked" '; } ?>/> No</label>
            <label><input type="radio" class="upgrade" name="upgrade" id="upgrade_both" value="2" <?php if(!isset($_GET['upgrade']) || $_GET['upgrade'] == 2) { echo 'checked="checked" '; } ?>/> Both</label>
            <br /><br />
            
            <strong>Cost</strong><br />
            <label><input type="checkbox" id="cost_all" value="1" /> All</label>
            <?php
            foreach($cost_list as $cost) {
            	$cost['cost'] = $cost['cost'] / 1; // Cast as INT
            	echo '<label><input type="checkbox" class="cost" name="cost[]" id="cost_'. $cost['cost'] .'" value="' . $cost['cost'] .'" ';
            	if(!empty($_GET['cost']) && in_array($cost['cost'], $_GET['cost'])) {
                	echo 'checked="checked" ';
              	}
              	echo '/> ' . $cost['cost'] . '</label> ';
            }
            ?>
            <br /><br />
            
            <label>
            	<strong>Order By</strong>:
            	<select name="order" id="order">
            		<option value="name" <?php if(!empty($_GET['order']) && $_GET['order'] == 'name') { echo 'selected="selected" '; } ?>>Card Name</option>
            		<option value="code" <?php if(!empty($_GET['order']) && $_GET['order'] == 'code') { echo 'selected="selected" '; } ?>>Card Code</option>
            	</select>
            </label>
            
            <br /><br />
            
            <input type="button" id="reset" value="Reset" /> <input type="submit" id="go" value="GO" />
            </form>
        </fieldset>
          
        <br /><br />
          
        <fieldset>
            <div id="results">
            	<?php
              	if(empty($num_cards)) {
                	echo '<strong>No Cards Match The Current Criteria.</strong>';
            	} else {
                	$codes = array();
                	ob_start();
                	foreach($card_list as $card) {
                  		$codes[] = $card['code'];
                  		
                  		echo '<div class="card_display" style="float: left; text-align: center; margin: 0 10px 10px;"><strong>' . $card['name'] . '</strong><br /><img src="img.php?card_id=' . $card['id'] .'" alt="" /></div>';
                	}
                	$output = ob_get_contents();
                	ob_end_clean();
                
                	echo '<p><strong>' . $num_cards . ' Result' . ($num_cards != 1 ? 's' : '') . '</strong></p>';
                	echo '<p><strong>Export Code</strong>: <input type="text" id="export_code" readonly="readonly" value="' . implode(' ', $codes) . '" size="120" /></p><br />';
                	echo $output;
              	}
              	?>
            </div> <!-- </div id="results"> -->
        </fieldset>
        </div> <!-- </div id="code"> -->

        <style type="text/css">
        form label { 
          float: none;
          margin: 0;
          padding: 0;
          text-align: left;
          width: auto;
          font-weight: normal;
        }
        </style>

<?php
include_once("includes/footer.php");
