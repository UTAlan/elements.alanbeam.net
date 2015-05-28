<?php
require_once("includes/config.php");

// Process if submitted
if(!empty($_GET['deckCode'])) {
	// Get Deck Code as Array
	$code_arr = explode(" ", $_GET['deckCode']);
	$mark_code = array_pop($code_arr);

	$exists = $db->query("SELECT id FROM cards_mark WHERE code = '$mark_code'");
	if($exists->num_rows == 0) {
		$code_arr[] = $mark_code;
	}
	$exists->close();

	if($_GET['numDiscards'] > count($code_arr)) {
		$_GET['numDiscards'] = count($code_arr);
	}

	$rand_arr = array_rand($code_arr, $_GET['numDiscards']);
	$rand_code_arr = array();
	for($i = 0; $i < $_GET['numDiscards']; $i++) {
		$rand_code_arr[] = $code_arr[$rand_arr[$i]];
	}
	$random_code = implode(" ", $rand_code_arr);
}

$Page['title'] = 'Card Discarder';
include_once('includes/header.php');
?>

      <div id="main">
        <div class="container">
          <div class="row main-row">
            <div class="4u 12u(mobile)">
              <section>
                <h2>Deck code</h2>

                <form action="#" method="get" id="discarderForm">
                <p><textarea id="deckCode" name="deckCode" style="width:100%; height:100px; font-size: 100%; padding: 5px;"><?php if(!empty($_GET['deckCode'])) { echo $_GET['deckCode']; } ?></textarea></p>

                <p style="text-align: right;">
                  <label for="numDiscards" style="margin-right: 10px;">Number of Discards: </label>
                  <input type="text" style="font-size: 100%; width: 35px;" name="numDiscards" id="numDiscards" value="<?php echo !empty($_GET['numDiscards']) ? $_GET['numDiscards'] : '6'; ?>" />
                </p>

                <p style="float: right;"><a href="#" class="button" onclick="$('#discarderForm').submit(); return false;">Randomize Discards</a></p>
                </form>
              </section>
            </div>

            <div class="8u 12u(mobile) important(mobile)">
              <section class="right-content">
                <?php if(!empty($random_code)) { ?>
                <p><a target="_blank" href="http://dek.im/d/<?php echo $random_code; ?>" title="Deck Image Builder by antiaverage"><img src="http://dek.im/deck/<?php echo $random_code; ?>" alt="Deck Image" id="deckImage" /></a></p>

                <h2>Discards</h2>
                <textarea style="font-size: 100%; width: 75%; height: 100px; margin-bottom: 25px" id="discardsCode" name="discardsCode"><?php echo $random_code; ?></textarea>

                <h2>Formatted</h2>
                <textarea style="font-size: 100%; width: 75%; height: 100px;" id="discardsForums" name="discardsForums" rows="6" cols="40">[color=#e8cf77][i][font=georgia][size=36pt]Discard & Salvage[/size][/font][/i][/color]
[deck author=LOSER DISCARDS ALL OF THESE, WINNER SALVAGES FROM THESE]<?php echo $random_code; ?>[/deck]</textarea>

                <?php } ?>
              </section>
            </div>
          </div>
        </div>
      </div>

<?php
include_once('includes/footer.php');
