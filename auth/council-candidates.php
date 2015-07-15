<?php
$Config['page']['auth']['Council'] = 1;
require_once("includes/config.php");

if(empty($_GET['election_id'])) {
  header("Location: council-setup.php");
  die();
}


if(!empty($_GET['add']) && !empty($_GET['election_id']) && !empty($_GET['id'])) {
  $election_id = $db->real_escape_string($_GET['election_id']);
  $user_id = $db->real_escape_string($_GET['id']);
  $db->query("INSERT INTO council_elections_candidates SET election_id = '$election_id', user_id = $user_id");
  
  header("Location: council-candidates.php?election_id=$election_id");
  die();
} else if(!empty($_GET['remove']) && !empty($_GET['election_id']) && !empty($_GET['id'])) {
  $election_id = $db->real_escape_string($_GET['election_id']);
  $user_id = $db->real_escape_string($_GET['id']);
  $db->query("DELETE FROM council_elections_candidates WHERE election_id = '$election_id' AND user_id = $user_id");
  
  header("Location: council-candidates.php?election_id=$election_id");
  die();
}

$election_id = $db->real_escape_string($_GET['election_id']);
$election_results = $db->query("SELECT id, name FROM council_elections WHERE id = $election_id");
if($election_results->num_rows != 1) {
  header("Location: council-setup.php");
  die();
}
$election = $election_results->fetch_assoc();

$candidates_results = $db->query("SELECT u.id, u.username FROM council_elections_candidates c LEFT JOIN users u ON u.id = c.user_id WHERE c.election_id = $election_id");
$candidates = array();
while($row = $candidates_results->fetch_assoc()) {
  $candidates[] = $row;
}

$Config['page']['jquery'] = <<<JQUERY

$("#candidate_add").autocomplete({
  source: "includes/user_search.php",
  minLength: 2,
  select: function(event, ui) {
    window.location = 'council-candidates.php?add=1&election_id={$election['id']}&id=' + ui.item.id;
  }
})

JQUERY;

$Config['page']['title'] = 'Council Candidates';
require_once("includes/header.php");
?>

      <section id="admin-container" class="container">
        <div class="center">
          <h2>Council Candidates</h2>
          <p>Manage council candidates for "<?php echo $election['name']; ?>"</p>
        </div>

        <div class="admin-wrapper">
          <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
              <h2>Candidates</h2>

              <ul>
                <?php
                foreach($candidates as $candidate) {
                  echo '<li>' . $candidate['username'] . ' (<a href="?remove=1&amp;election_id=' . $election['id'] . '&amp;id=' . $candidate['id'] . '">remove</a>)</li>';
                }
                ?>
              </ul>

              <h3>Add Member</h3>
              <form>
              <input type="text" id="candidate_add" placeholder="Username" />
              </form>
            </div>
          </div>
        </div>
      </section>

<?php
require_once("includes/footer.php");