<?php
require_once("includes/config.php");

$marks_result = $db->query("SELECT id, code, name, IF(code!='',0,1) AS other FROM cards_mark WHERE name <> 'Mark' ORDER BY other, name");
$marks_list = array();
while($m = $marks_result->fetch_assoc()) {
	$marks_list[] = $m;
}
$marks_result->close();

$Page['title'] = 'Deck Code Generator';
$Page['jquery'] = 'mark_names = [ ';
foreach($marks_list as $mark) {
    $Page['jquery'] .= '"' . $mark['name'] . '", ';
}
$Page['jquery'] .= ' ]; 

marks = { ';
foreach($marks_list as $mark) {
    $Page['jquery'] .= '"' . $mark['name'] . '":"' . $mark['code'] . '", ';
}
$Page['jquery'] .= ' }; 

mark_codes = { ';
foreach($marks_list as $mark) {
    $Page['jquery'] .= '"' . $mark['code'] . '":"' . $mark['name'] . '", ';
}
$Page['jquery'] .= ' }; 

$("#mark").autocomplete({ 
    source: mark_names,
    change: changeMark
}); 

$("#deckCode").change(function() {
    code = $(this).val();

    if(code.length > 3) {
        var mark_code = code.substring(code.length - 3);
        if(mark_codes[mark_code]) {
            code_hasMark = true;
            $("#mark").val(mark_codes[mark_code]);
        } else {
            code_hasMark = false;
        }
    }

    updateImage();
});

$("#add_cards").autocomplete({ 
    source: function(request, response) {
        $.get("card_search.php", { query: request.term, complex: "false" }, function(data) {
            response(data);
        }, "json");
    },
    minLength: 3,
    change: addCard
});

';

include_once('includes/header.php');
?>

            <div id="main">
                <div class="container">
                    <div class="row main-row">
                        <div class="4u 12u(mobile)">
                            <section>
                                <h2>Deck code</h2>

                                <p><textarea id="deckCode" name="deckCode" style="width:100%; height:100px; font-size: 100%; padding: 5px;"><?php if(!empty($_GET['deckCode'])) { echo $_GET['deckCode']; } ?></textarea></p>

                                <h2>Mark</h2>
                                <p><input type="text" name="mark" id="mark" style="font-size: 100%; width: 100px;" /></p>
                            </section>
                            </section>
                        </div>

                        <div class="8u 12u(mobile) important(mobile)">
                            <section class="right-content">
                                <p><a id="deckImage_link" target="_blank" href="http://dek.im/d/" title="Deck Image Builder by antiaverage"><img src="http://dek.im/deck/" alt="Deck Image" id="deckImage" /></a></p>

                                <h2>Add Cards</h2>

                                <p><input type="text" id="add_cards" name="add_cards" style="font-size: 100%; width: 200px;" /></p>

                                <h2>Existing Cards</h2>

                                <table id="existing_cards" width="100%">
                                    <thead>
                                        <tr>
                                            <th width="70%" align="left">Name</th>
                                            <th width="10%">Qty</th>
                                            <th width="10%">Remove</th>
                                            <th width="10%">Add</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                                <style>
                                #existing_cards {
                                    width: 400px;
                                }

                                #existing_cards tr {
                                    line-height: 150%;
                                }

                                #existing_cards th {
                                    font-weight: bold;
                                    color: #000;
                                }

                                #existing_cards td a {
                                    text-decoration: none;
                                }
                                </style>
                    </div>
                </div>
            </div>

<?php /*
	<div id="code">
        <form id="code_generator_form" action="#" method="post">
        	<label for="code">Code: </label>
                <textarea name="code" id="code" rows="6" cols="40"></textarea>
                <br /><br />
                    
                <label for="showImage">Show Deck Image: </label>
                <input type="checkbox" name="showImage" id="showImage" value="1" />
                <br /><br />
                    
                <label for="generate_button">&nbsp;</label>
                <input type="button" name="generate_button" id="generate_button" value="Generate Code" />
                <input type="button" name="reverse_button" id="reverse_button" value="Load from Code" />
                <br /><br />
                    
                <div id="deckImageDiv"></div>
                    
                <label for="mark">Mark: </label>
                <select name="mark" id="mark">
                	<option value=""></option>
                        <?php
                        $marks = $mark_id = $uncards = $upcards = array();
                        foreach($marks_list as $mark) {
                        	// Save Cards for this Mark
                            	$cards_result = $db->query("SELECT name, code, upgrade, IF(name LIKE 'Mark of %', 1, 0) AS isMark FROM cards_card WHERE mark_id = {$mark['id']} ORDER BY isMark, code");
                            	while($card = $cards_result->fetch_assoc()) {
                                	if((strpos($card['name'], 'Pillar') !== false || strpos($card['name'], 'Pendulum') !== false || strpos($card['name'], 'Tower') !== false || strpos($card['name'], 'Factory') !== false) && strpos($card['name'], 'Shield') === false) {
                                		$maxQty = 60;
        	                        } else {
                                    		$maxQty = 6;
                                	}
                                
	                                if($card['upgrade'])    {
	                                	$upcards[$mark['name']][] = array('name'=>$card['name'], 'code'=>$card['code'], 'maxQty'=>$maxQty);
                                	} else {
                                    		$uncards[$mark['name']][] = array('name'=>$card['name'], 'code'=>$card['code'], 'maxQty'=>$maxQty);
                                	}
                            	}
                            
                            	// Save Mark
                            	$marks[] = $mark['name'];
                            	$mark_id[] = $mark['id'];
                            
                            	// Output Mark
                            	if($mark['code'] != '') {
                                	echo '<option value="' . $mark['code'] . '">' . $mark['name'] . '</option>';
                            	}
                        }
                        ?>
		</select>
                <br /><br />
                    
                <p>* Upgraded Card</p>
                    
		<table width="100%">
                <?php
		$index = 0;
                foreach($uncards as $markName=>$cardArr) {
			if($index % 6 == 0) {
				echo '<tr>';
			}
						
			echo '<td valign="top" width="16%">';
                        echo '<h3><img src="img.php?mark_id=' . $mark_id[$index] . '" alt="' . $markName . '" /> ' . $markName . ' <img src="img.php?mark_id=' . $mark_id[$index] . '" alt="' . $markName . '" /></h3>';
                        echo '<table>';
                        
                        foreach($cardArr as $i=>$card) {
                        	echo '<tr><td><select name="' . $card['code'] . '" id="' . $card['code'] . '" class="qty">';
				for($j = 0; $j <= $card['maxQty']; $j++) {
					echo "<option value='{$j}'>{$j}</option>";
				}
				echo '</select></td><td>' . $card['name'] . '</td></tr>';
                            
                            	echo '<tr><td><select name="' . $upcards[$markName][$i]['code'] . '" id="' . $upcards[$markName][$i]['code'] . '" class="qty">';
				for($j = 0; $j <= $upcards[$markName][$i]['maxQty']; $j++) {
					echo "<option value='{$j}'>{$j}</option>";
				}
				echo '</select></td><td>' . $upcards[$markName][$i]['name'] . '*</td></tr>';
                        }
                        
                        echo '</table>';
						
			echo '</td>';
						
			if($index % 6 == 5) {
				echo '</tr>';
			}
						
			$index++;
		}
        	?>
                </table>
					
                <br clear="all" />
        </form>
	</div> <!-- </div id="code"> -->
    */ ?>

    <script>
    var code = "";
    var code_hasMark = false;
    var marks;
    var mark_names;
    var mark_codes;
    </script>

<?php
include_once('includes/footer.php');
