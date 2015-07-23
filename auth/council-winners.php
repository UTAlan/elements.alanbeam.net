<?php
$debug = true;
$Config['page']['auth']['council'] = 1;
require_once("includes/config.php");

// Get Election Info
$election_id = $db->real_escape_string($_GET['election_id']);
$election_result = $db->query("SELECT name, seats FROM council_elections WHERE id = $election_id");
$election = $election_result->fetch_assoc();

// Get Candidates
$candidates_result = $db->query("SELECT u.id, u.username FROM council_elections_candidates c LEFT JOIN users u ON u.id = c.user_id WHERE c.election_id = $election_id");
$candidates = array();
$candidate_ids = array();
while($candidate = $candidates_result->fetch_assoc()) {
  $candidates[$candidate['id']] = array('username' => $candidate['username']);
  $candidate_ids[] = $candidate['id'];
}

// Get Voters & Votes
$voter_result = $db->query("SELECT v.user_id FROM council_elections_votes v LEFT JOIN council_elections_candidates c ON c.id = v.candidate_id WHERE c.election_id = $election_id GROUP BY v.user_id");
$num_voters = $voter_result->num_rows;
$votes = array("1"=>array());
while($voter = $voter_result->fetch_assoc()) {
  $votes_result = $db->query("SELECT c.user_id FROM council_elections_votes v LEFT JOIN council_elections_candidates c ON c.id = v.candidate_id WHERE v.user_id = " . $voter['user_id'] . " ORDER BY v.rank ASC");
  $votes[1][$voter['user_id']] = array();
  while($vote = $votes_result->fetch_assoc()) {
    $votes[1][$voter['user_id']][] = array("candidate_id" => $vote['user_id'], "power" => 1.0);
  }
}

// Calculate STV Votes Required via Droop
$votes_required = floor(($num_voters / ($election['seats'] + 1)) + 1);

// Start Counting
$winners = array();
$round = 1;
$done = false;
while(count($winners) < $election['seats']) {
  //show('Round ' . $round, '<p>');
  $w = array(); // Temporary winner array
  $votes[$round+1] = $votes[$round];

  // Reset vote count for candidates
  foreach($candidates as $candidate_id=>$info) {
    $candidates[$candidate_id]['count'] = 0;
    $candidates[$candidate_id]['voters'] = array();
  }

  // Count first place votes per candidate
  foreach($votes[$round] as $voter_id=>$vote) {
    $candidates[$vote[0]['candidate_id']]['count'] += $vote[0]['power'];
    $candidates[$vote[0]['candidate_id']]['voters'][] = $voter_id;
  }

  // Look for candidates with > $votes_required
  foreach($candidates as $candidate_id=>$info) {
    if($info['count'] >= $votes_required) {
      $w[$candidate_id] = $info;
      //show($info['username'] . ' - ' . round($info['count'], 2) . ' votes', '<p>');
    }
  }

  //show($w);

  // Check if we have a tie that puts us beyond # of seats available
  if(count($winners) + count($w) > $election['seats']) {
    // Start tie-breaker with most votes this round
    $sorted = sortByVotes($w);
    //show($sorted);
    $w = array();

    foreach($sorted as $count=>$c) {
      if(count($winners) + count($w) + count($c) <= $election['seats']) {
        // Add these winners
        foreach($c as $winner_id => $winner_info) {
          $w[$winner_id] = $winner_info;
        }
      } else {
        for($i = $round-1; $i > 0; $i--) {
          $prev_votes = array();
          foreach($votes[$i] as $voter_id=>$vote) {
            $candidate_id = $vote[0]['candidate_id'];
            if(array_key_exists($candidate_id, $c)) {
              if(!isset($prev_votes[$candidate_id])) $prev_votes[$candidate_id] = array();
              $prev_votes[$candidate_id] = $candidates[$candidate_id];
            }
          }
          $prev_votes = sortByVotes($prev_votes);

          foreach($prev_votes as $k=>$v) {
            if(count($v) == count($c)) {
              break;
            } else {
              
            }
          }
          show($prev_votes, '<pre>', true);
        }
      }
    }
  } else if(empty($w)) {
    show('No winners, need to implement removing bottom candidates', '<p>', true);
  }

  // Save Marked Candidates as Winners
  foreach($w as $winner_id=>$winner_info) {
    $winners[$winner_id] = $winner_info['username'];

    // Move Surplus Votes to 2nd place candidates
    //show('Candidate: ' . $winner_info['username'], '<h2>');
    $surplus = $winner_info['count'] - $votes_required;
    //show('Votes: ' . $winner_info['count'], '<p>');
    //show('Suplus: ' . $surplus, '<p>');
    if($surplus > 0) {
      $surplus = $surplus / count($winner_info['voters']);
      //show('Suplus per candidate: ' . $surplus, '<p>');
      foreach($winner_info['voters'] as $voter_id) {
        $votes[$round+1][$voter_id][0]['power'] = 0;
        $votes[$round+1][$voter_id][1]['power'] = $votes[$round][$voter_id][1]['power'] + $surplus;
      }
    }

    // Remove winners from $votes array
    foreach($votes[$round+1] as $voter_id=>$vote) {
      foreach($vote as $rank=>$info) {
        if($info['candidate_id'] == $winner_id) {
          unset($candidates[$info['candidate_id']]);
          $power = $votes[$round+1][$voter_id][$rank]["power"];
          if($power > 0) $power -= 1;
          unset($votes[$round+1][$voter_id][$rank]);
          break;
        }
      }
      $votes[$round+1][$voter_id] = array_values($votes[$round+1][$voter_id]);
      $votes[$round+1][$voter_id][0]["power"] += $power;
    }
  }
  //show($winners);

  $round++;
}

sort($winners, SORT_NATURAL | SORT_FLAG_CASE);
//show($winners);

$Config['page']['title'] = 'Council Winners';
require_once("includes/header.php");
?>

      <section id="admin-container" class="container">
        <div class="center">
          <h2>Council Winners</h2>
          <p><?php echo $election['name']; ?></p>
        </div>

        <div class="admin-wrapper">
          <div class="row">
            <div class="col-xs-4 col-xs-offset-4">
              <h3>Winners</h3>

              <ul>
              <?php
              foreach($winners as $winner) {
                echo '<li>' . $winner . '</li>';
              }
              ?>
              </ul>
            </div>
          </div>
      </section>

<?php
require_once("includes/footer.php");



function sortByVotes($arr) {
  $result = array();
  foreach($arr as $id=>$info) {
    $count = $info['count'] . '';
    if(empty($result[$count])) $result[$count] = array();

    $result[$count][$id] = $info;
  }

  krsort($result);

  return $result;
}


function show($msg, $tag = '<pre>', $die = false) {
  global $debug;
  if($debug) {
    echo $tag;
    print_r($msg);
    echo str_replace('<', '</', $tag);

    if($die) die();
  }
}
