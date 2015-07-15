<?php
$Config['page']['auth']['Council'] = 1;
require_once("includes/config.php");

$elections_results = $db->query("SELECT * FROM council_elections ORDER BY start_date DESC");
$elections = array();
while($row = $elections_results->fetch_assoc()) {
  $candidates_results = $db->query("SELECT COUNT(*) as count FROM council_elections_candidates WHERE election_id = " . $row['id']);
  $r = $candidates_results->fetch_assoc();
  $row['candidates'] = $r['count'];
  $elections[] = $row;
}

$Config['page']['title'] = 'Council Setup';
require_once("includes/header.php");
?>

      <section id="admin-container" class="container">
        <div class="center">
          <h2>Council Setup</h2>
          <p>Manage council elections</p>
        </div>

        <div class="admin-wrapper">
          <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
              <h2>Elections</h2>

              <table width="100%">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Candidates</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  foreach($elections as $election) {
                    echo '<td><a href="council-edit.php?id=' . $election['id'] . '">' . $election['name'] . '</a></td>';
                    echo '<td>' . $election['start_date'] . '</td>';
                    echo '<td>' . $election['end_date'] . '</td>';
                    echo '<td><a href="council-candidates.php?election_id=' . $election['id'] . '">' . $election['candidates'] . '</a></td>';
                    echo '<td><a href="council-results.php?election_id=' . $election['id'] . '">View Results</a></td>';
                  }
                  ?>
                </tbody>
              </table>

              <p class="pull-right"><a href="council-edit.php" class="btn btn-primary btn-lg">Add Election</a></p>
            </div>
          </div>
        </div>
      </section>

<?php
require_once("includes/footer.php");