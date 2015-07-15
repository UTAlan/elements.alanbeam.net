<?php
$Config['page']['auth'] = 1;
require_once("includes/config.php");

if(!empty($_POST)) {
  $db->query("DELETE FROM council_elections_votes WHERE user_id = " . $_SESSION['user']['id']);
  foreach ($_POST['candidate'] as $rank=>$value) {
    $candidate_result = $db->query("SELECT c.id FROM council_elections_candidates c LEFT JOIN users u ON u.id = c.user_id WHERE u.id = '" . $value . "'");
    $candidate = $candidate_result->fetch_assoc();
    $db->query("INSERT INTO council_elections_votes SET user_id = " . $_SESSION['user']['id'] . ", candidate_id = " . $candidate['id'] . ", rank = $rank");
  }

  die();
}

$election_id = $db->real_escape_string($_GET['election_id']);
$election_result = $db->query("SELECT id, name, start_date, end_date, IF(start_date > NOW(), 0, 1) AS started, IF(end_date < NOW(), 1, 0) AS ended FROM council_elections WHERE id = $election_id");
if($election_result->num_rows != 1) {
  header("Location: council.php");
  die();
}
$election = $election_result->fetch_assoc();

if(!$election['started']) {
  header("Location: council.php");
  die();
}

$candidates_result = $db->query("SELECT u.id, u.username, v.rank FROM council_elections_votes v LEFT JOIN council_elections_candidates c ON c.id = v.candidate_id LEFT JOIN users u ON u.id = c.user_id WHERE c.election_id = $election_id AND v.user_id = " . $_SESSION['user']['id'] . " ORDER BY v.rank ASC");
$candidates = array();
while($row = $candidates_result->fetch_assoc()) {
  $candidates[] = $row;
}

$status = 'Saved';
$status_class = 'green';

if(empty($candidates)) {
  $status = 'Unsaved';
  $status_class = 'red';
  $candidates_result = $db->query("SELECT u.id, u.username FROM council_elections_candidates c LEFT JOIN users u ON u.id = c.user_id WHERE c.election_id = $election_id ORDER BY RAND()");
  $candidates = array();
  while($row = $candidates_result->fetch_assoc()) {
    $candidates[] = $row;
  }
}

if($election['started'] && !$election['ended']) {
$Config['page']['jquery'] = <<<JQUERY

$("#sortable").sortable({
  placeholder: "ui-state-highlight",
  update: function(event, ui) {
    console.log('sortable update');
    var data = $(this).sortable('serialize');
    $("#status").html("Saving...");
    $("#status").removeClass("red");
    $("#status").removeClass("green");
    $.ajax({
      data: data,
      type: 'POST',
      url: 'council-vote.php',
      success: function(data) {
        $("#status").html("Saved");
        $("#status").removeClass("red");
        $("#status").addClass("green");
      }
    });
  },
});

$("#sortable").disableSelection();

JQUERY;
}

$Config['page']['title'] = 'Council Voting';
require_once("includes/header.php");
?>

      <section id="admin-container" class="container">
        <div class="center">
          <h2><?php echo $election['name'] ?></h2>
          <p>Status: <span id="status" class="<?php echo $status_class; ?>"><?php echo $status; ?></span></p>
        </div>

        <div class="admin-wrapper">
          <div class="row">
            <div class="col-xs-4 col-xs-offset-4">
              <ul id="sortable">
                <?php
                foreach($candidates as $c) {
                  echo '<li class="ui-state-default" id="candidate_' . $c['id'] . '">' . $c['username'] . '</li>';
                }
                ?>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <style>
        #sortable { list-style-type: none; margin: 0; padding: 0; width: 100%; }
        #sortable li { margin: 0 5px 5px 5px; padding: 5px; font-size: 1.2em; height: 1.5em; text-align: center; cursor: pointer; }
        html>body #sortable li { height: 1.9em; line-height: 1.2em; }
        .ui-state-highlight { height: 1.9em; line-height: 1.2em; }
        #status { font-weight: bold; }
        .red { color: red; }
        .green { color: green; }
        .yellow { color: yellow; }
      </style>

<?php 
require_once("includes/footer.php");