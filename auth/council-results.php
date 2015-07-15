<?php
$Config['page']['auth']['council'] = 1;
require_once("includes/config.php");

$election_id = $db->real_escape_string($_GET['election_id']);

$election_result = $db->query("SELECT name FROM council_elections WHERE id = $election_id");
$election = $election_result->fetch_assoc();
$voter_result = $db->query("SELECT DISTINCT u.id, u.username
  FROM council_elections_votes v 
  LEFT JOIN users u ON u.id = v.user_id 
  LEFT JOIN council_elections_candidates c ON c.id = v.candidate_id 
  WHERE c.election_id = $election_id");
$voters = array();
while($row = $voter_result->fetch_assoc()) {
  $votes_result = $db->query("SELECT u.username
    FROM council_elections_votes v
    LEFT JOIN council_elections_candidates c ON c.id = v.candidate_id
    LEFT JOIN users u ON u.id = c.user_id
    WHERE v.user_id = " . $row['id'] . " AND c.election_id = $election_id
    ORDER BY rank ASC");
  $row['votes'] = array();
  while($r = $votes_result->fetch_assoc()) {
    $row['votes'][] = $r;
  }
  $voters[] = $row;
}
$numCandidates = count($voters[0]['votes']);

if(!empty($_GET['export'])) {
  $now = gmdate("D, d M Y H:i:s");
  header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
  header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
  header("Last-Modified: {$now} GMT");
  header("Content-Type: application/force-download");
  header("Content-Type: application/octet-stream");
  header("Content-Type: application/download");
  header("Content-Disposition: attachment;filename=results.csv");
  header("Content-Transfer-Encoding: binary");

  ob_start();
  $output = fopen('php://output', 'w');

  $columns = array('Voter');
  for($i = 1; $i <= $numCandidates; $i++) {
    $columns[] = $i;
  }
  fputcsv($output, $columns);

  foreach($voters as $voter) {
    $row = array($voter['username']);
    foreach($voter['votes'] as $vote) {
      $row[] = $vote['username'];
    }
    fputcsv($output, $row);
  }
  fclose($output);

  echo ob_get_clean();
  die();
}

$Config['page']['title'] = 'Election Results';
require_once("includes/header.php");
?>

      <section id="admin-container" class="container">
        <div class="center">
          <h2>Election Results</h2>
          <p><?php echo $election['name']; ?></p>
        </div>

        <div class="admin-wrapper">
          <div class="row">
            <div class="col-xs-12">
              <p class="pull-right"><a href="?export=1&amp;election_id=<?php echo $election_id; ?>" class="btn btn-primary btn-lg">Export to CSV</a></p>
              <table width="100%">
                <thead>
                  <tr>
                    <th>Voter</th>
                    <?php for($i = 1; $i <= $numCandidates; $i++) { echo '<th>' . $i . '</th>'; } ?>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  foreach($voters as $voter) {
		    echo '<tr>';
                    echo '<td>' . $voter['username'] . '</td>';
                    foreach($voter['votes'] as $vote) {
                      echo '<td>' . $vote['username'] . '</td>';
                    }
		    echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>

<?php
require_once("includes/footer.php");
