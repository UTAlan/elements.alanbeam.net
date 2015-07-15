<?php
$Config['page']['auth'] = 1;
require_once("includes/config.php");

$past_results = $db->query("SELECT id, name, end_date FROM council_elections WHERE end_date < NOW() ORDER BY end_date DESC");
$past = array();
while($row = $past_results->fetch_assoc()) {
  $past[] = $row;
}

$current_results = $db->query("SELECT id, name, end_date FROM council_elections WHERE start_date < NOW() AND end_date > NOW() ORDER BY end_date ASC");
$current = array();
while($row = $current_results->fetch_assoc()) {
  $current[] = $row;
}

$upcoming_results = $db->query("SELECT id, name, start_date FROM council_elections WHERE start_date > NOW() ORDER BY start_date ASC");
$upcoming = array();
while($row = $upcoming_results->fetch_assoc()) {
  $upcoming[] = $row;
}

$Config['page']['title'] = 'Council Elections';
require_once("includes/header.php");
?>

      <section id="admin-container" class="container">
        <div class="center">
          <h2>Council Elections</h2>
        </div>

        <div class="admin-wrapper">
          <div class="row">
            <div class="col-xs-4">
              <h2>Past Elections</h2>

              <table width="100%">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>End Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  foreach($past as $p) {
                    echo '<tr>';
                    echo '<td><a href="council-vote.php?election_id=' . $p['id'] . '">' . $p['name'] . '</a></td>';
                    echo '<td>' . $p['end_date'] . '</td>';
                    echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <div class="col-xs-4">
              <h2>Current Elections</h2>

              <table width="100%">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>End Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  foreach($current as $c) {
                    echo '<tr>';
                    echo '<td><a href="council-vote.php?election_id=' . $c['id'] . '">' . $c['name'] . '</a></td>';
                    echo '<td>' . $c['end_date'] . '</td>';
                    echo '</tr>';
                  }
                  ?>
                </tbody>
              </table>
            </div>
            <div class="col-xs-4">
              <h2>Upcoming Elections</h2>

              <table width="100%">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Start Date</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  foreach($upcoming as $u) {
                    echo '<tr>';
                    echo '<td>' . $u['name'] . '</td>';
                    echo '<td>' . $u['start_date'] . '</td>';
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