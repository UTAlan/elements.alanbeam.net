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
    buildTable();
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

$Page['title'] = 'Code Generator';
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

            <script>
            var code = "";
            var code_hasMark = false;
            var marks;
            var mark_names;
            var mark_codes;
            </script>

<?php
include_once('includes/footer.php');
