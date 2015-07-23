<?php
ini_set('display_errors', 'On');
$Config['page']['auth']['council'] = 1;
require_once("includes/config.php");

// Get election info
$election_id = $db->real_escape_string($_GET['election_id']);
$election_result = $db->query("SELECT name, seats FROM council_elections WHERE id = $election_id");
$election = $election_result->fetch_assoc();

$voter_result = $db->query("SELECT DISTINCT u.id, u.username
  FROM council_elections_votes v 
  LEFT JOIN users u ON u.id = v.user_id 
  LEFT JOIN council_elections_candidates c ON c.id = v.candidate_id 
  WHERE c.election_id = $election_id
  ORDER BY RAND()");
$voters = array();
while($row = $voter_result->fetch_assoc()) {
  $votes_result = $db->query("SELECT u.id, u.username
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

// Get Candidates
$candidates = array();
$candidate_ids = array();
$candidate_results = $db->query("SELECT u.id, u.username FROM council_elections_candidates c LEFT JOIN users u ON u.id = c.user_id WHERE c.election_id = $election_id");
while($c = $candidate_results->fetch_assoc()) {
  $candidate_ids[] = $c['id'];
  $candidates[] = $c;
}
$numCandidates = count($candidates);

if(!empty($_GET['blt'])) {
  $now = gmdate("D, d M Y H:i:s");
  header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
  header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
  header("Last-Modified: {$now} GMT");
  header("Content-Type: application/force-download");
  header("Content-Type: application/octet-stream");
  header("Content-Type: application/download");
  header("Content-Disposition: attachment;filename=results.blt");
  header("Content-Transfer-Encoding: binary");

  ob_start();
  $output = fopen('php://output', 'w');

  fputcsv($output, array($numCandidates, $election['seats']), "\t");

  foreach($voters as $voter) {
    $row = array("1");
    foreach($voter['votes'] as $vote) {
      $row[] = array_search($vote['id'], $candidate_ids) + 1;
    }
    $row[] = "0";
    fputcsv($output, $row, "\t");
  }

  fputcsv($output, array("0"), "\t");
  foreach($candidates as $candidate) {
    fputcsv($output, array($candidate['username'] . ' '), "\t");
  }
  fputcsv($output, array($election['name']), "\t");

  fclose($output);

  echo ob_get_clean();
  die();
} else if(!empty($_GET['csv'])) {
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

  $columns = array();
  for($i = 1; $i <= $numCandidates; $i++) {
    $columns[] = $i;
  }
  fputcsv($output, $columns);

  foreach($voters as $voter) {
    $row = array();
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
              <p class="pull-right"><a href="council-winners.php?election_id=<?php echo $election_id; ?>" class="btn btn-primary btn-lg">View Results</a> <a href="?blt=1&amp;election_id=<?php echo $election_id; ?>" class="btn btn-primary btn-lg">Export to BLT</a> <a href="?csv=1&amp;election_id=<?php echo $election_id; ?>" class="btn btn-primary btn-lg">Export to CSV</a></p>
              <table width="100%">
                <thead>
                  <tr>
                    <?php for($i = 1; $i <= $numCandidates; $i++) { echo '<th>' . $i . '</th>'; } ?>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  foreach($voters as $voter) {
		                echo '<tr>';
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
