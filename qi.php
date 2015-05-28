<?php
require_once("includes/config.php");

// Redirect if no code
if(!empty($_GET['deckCode'])) {
	// Initialize variables
	$numCards = 0;
	$report = array();
	$mark = array();
	$marks = array();
	$other_list = array();
	$numGen = 0;

	// Initialize Mark Array
	$getMarks = $db->query("SELECT name, code, img FROM cards_mark WHERE name NOT IN ('Mark', 'Other') ORDER BY name");
	while($r = $getMarks->fetch_assoc()) {
		$marks[$r['name']] = array();
		$marks[$r['name']]['name'] = $r['name'];
		$marks[$r['name']]['code'] = $r['code'];
		$marks[$r['name']]['img'] = $r['img'];
		$marks[$r['name']]['cost'] = 0;
		$marks[$r['name']]['special'] = 0;
		$marks[$r['name']]['generation'] = 0;
		$marks[$r['name']]['qi'] = 0;
	}
	$getMarks->close();

	// Get Deck Code as Array
	$code_arr = explode(" ", $_GET['deckCode']);

	// Get Mark from Code
	$getMark = $db->query("SELECT name, code, img FROM cards_mark WHERE code IN ('".implode("', '", $code_arr)."')");
	if($getMark->num_rows > 0) {
		$mark = $getMark->fetch_assoc();
		$marks[$mark['name']]['generation'] += 5;
	}
	$getMark->close();

	// Loop through Code List
	foreach($code_arr as $code) {
		// Ignore Mark
    	if(!empty($mark['code']) && $code == $mark['code']) {
      		continue;
    	}
    
 	   // Get Card from database
    	$getCard = $db->query("SELECT m.name AS mark_name, c.name, cq.amount AS cost, cm.name AS cost_mark, sq.amount AS special, sm.name AS special_mark, c.upgrade, c.rare, c.other, c.code FROM cards_card c LEFT JOIN cards_mark m ON m.id = c.mark_id LEFT JOIN cards_quanta cq ON cq.id = c.cost_id LEFT JOIN cards_mark cm ON cm.id = cq.mark_id LEFT JOIN cards_quanta sq ON sq.id = c.special_id LEFT JOIN cards_mark sm ON sm.id = sq.mark_id WHERE c.code = '$code'");
    
    	if($getCard->num_rows == 0) {
	    	// Ignore Cards that can't be found
	    	$getCard->close();
      		continue;
	    }

		$card = $getCard->fetch_assoc();
		$getCard->close();

		if($card['other'] == 1) {
			// Push OTHER cards to the end
			$other_list[] = $code;
			continue;
		}
    
    	// Get Cost
    	if($card['cost'] > 0) {
      		$marks[$card['cost_mark']]['cost'] += $card['cost'];
    	}
    
    	// Get Special
    	if($card['special'] > 0) {
      		$marks[$card['special_mark']]['special'] += $card['special'];
    	}
    
	    $getGeneration = $db->query("SELECT q.amount, m.name AS mark_name FROM cards_card c LEFT JOIN cards_card_generation g ON g.card_id = c.id LEFT JOIN cards_quanta q ON q.id = g.quanta_id LEFT JOIN cards_mark m ON m.id = q.mark_id WHERE c.code = '$code'");
    
    	if($getGeneration->num_rows > 0) {
      		while($row = $getGeneration->fetch_assoc()) {
		        if(empty($row['amount'])) {
        			continue;
        		}
        
		        if($row['mark_name'] == 'Mark') {
          			$marks[$mark['name']]['generation'] += $row['amount'];
        		} else if($row['mark_name'] == 'Other') {
          			foreach($marks as $m) {
            			$marks[$m['name']]['generation'] += $row['amount'];
          			}
        		} else {
          			$marks[$row['mark_name']]['generation'] += $row['amount'];
        		}
      		}
    	}
    	$getGeneration->close();
 	   
    	$numCards++;
    }

	// Count how many Marks are generating quanta
  	foreach($marks as $m) {
    	if($m['generation'] > 0) {
      		$numGen++;
   	 	}
  	}

  	// Process OTHER cards
  	foreach($other_list as $code) {
    	// Get Card from database
    	$getCard = $db->query("SELECT m.name AS mark_name, c.name, cq.amount AS cost, cm.name AS cost_mark, sq.amount AS special, sm.name AS special_mark, c.upgrade, c.rare, c.other, c.code FROM cards_card c LEFT JOIN cards_mark m ON m.id = c.mark_id LEFT JOIN cards_quanta cq ON cq.id = c.cost_id LEFT JOIN cards_mark cm ON cm.id = cq.mark_id LEFT JOIN cards_quanta sq ON sq.id = c.special_id LEFT JOIN cards_mark sm ON sm.id = sq.mark_id WHERE c.code = '$code'");
    	$card = $getCard->fetch_assoc();
    	$getCard->close();
    	
	    // Get Cost
    	if($card['cost'] > 0) {
      		foreach($marks as $m) {
        		if($m['generation'] > 0 && $numGen > 0) {
          			$m['cost'] += $card['cost'] / $numGen;
        		}
      		}
    	}
    
    	// Get Special
    	if($card['special'] > 0) {
      		foreach($marks as $m) {
        		if($m['generation'] > 0 && $numGen > 0) {
          			$m['special'] += $card['special'] / $numGen;
        		}
      		}
    	}
    
    	$getGeneration = $db->query("SELECT q.amount, m.name AS mark_name FROM cards_card c LEFT JOIN cards_card_generation g ON g.card_id = c.id LEFT JOIN cards_quanta q ON q.id = g.quanta_id LEFT JOIN cards_mark m ON m.id = q.mark_id WHERE c.code = '$code'");
    
   		if($getGeneration->num_rows > 0) {
      		while($row = $getGeneration->fetch_assoc()) {
        		foreach($marks as $m) {
          			if($m['generation'] == 0) {
            			$numGen++;
          			}
          			$m['generation'] += $row['amount'] / 12;
        		}
      		}
    	}
    	$getGeneration->close();
    
	    $numCards++;
  	}
  
  	foreach($marks as $m) {
    	$m['cost'] = round($m['cost'], 0);
    	$m['special'] = round($m['special'], 0);
    	$m['generation'] = round($m['generation'], 0);
    
    	if($m['generation'] > 0) {
      		$m['qi'] = round(($m['cost'] + $m['special']) / ($m['generation'] / 5), 2);
    	} else {
      		$m['qi'] = 'N/A';
    	}
    
    	$report[] = array("mark"=>$m['name'], "img"=>$m['img'], "cost"=>$m['cost'], "special"=>$m['special'], "generation"=>$m['generation'], "qi"=>$m['qi']);
  	}
}

$Page['title'] = 'Quanta Index';
include_once('includes/header.php');
?>

      <div id="main">
        <div class="container">
          <div class="row main-row">
            <div class="4u 12u(mobile)">
              <section>
                <h2>Deck code</h2>

                <form action="#" method="get" id="qiForm">
                <p><textarea id="deckCode" name="deckCode" style="width:100%; height:100px; font-size: 100%; padding: 5px;"><?php if(!empty($_GET['deckCode'])) { echo $_GET['deckCode']; } ?></textarea></p>

                <p style="float: right;"><a href="#" class="button" onclick="$('#qiForm').submit(); return false;">Calculate QI</a></p>
                </form>
              </section>
            </div>

            <div class="8u 12u(mobile) important(mobile)">
              <section class="right-content">
                <?php if(!empty($report)) { ?>
                <p><a target="_blank" href="http://dek.im/d/<?php echo $_GET['deckCode']; ?>" title="Deck Image Builder by antiaverage"><img src="http://dek.im/deck/<?php echo $_GET['deckCode']; ?>" alt="Deck Image" id="deckImage" /></a></p>

                <?php 
                foreach($report as $r) { 
                  if($r['qi'] == 'N/A' || ($r['cost'] == 0 && $r['special'] == 0)) {
                      continue;
                  }
                ?>
                <div class="markStats" style="float: left; width:250px">
                    <p class="mark" style="float: left;">
                      <img height="84px" src="images/<?php echo $r['mark']; ?>.png" alt="<?php echo $r['mark']; ?>" />
                    </p>
                    
                    <p class="stats" style="float: left; margin: 0 10px 10px 10px; line-height: 125%; font-size: 95%;">
                      <strong>Cost:</strong> <?php echo $r['cost']; ?><br />
                      <strong>Ability Cost:</strong> <?php echo $r['special']; ?><br />
                      <strong>Quanta Generation:</strong> <?php echo $r['generation']; ?><br />
                      <strong>QI:</strong> <?php echo $r['qi']; ?> 
                      (<strong><?php echo ($r['qi'] >= 6 ? 'too few pillars' : ($r['qi'] <= 4 ? 'too many pillars' : 'good # of pillars')); ?></strong>)<br />
                    </p>
                </div>
                <?php } } ?>
              </section>
            </div>
          </div>
        </div>
      </div>
 
<?php
include_once('includes/footer.php');
