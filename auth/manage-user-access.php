<?php
$Config['page']['admin'] = 1;
require_once("includes/config.php");

if(!empty($_GET['add']) && !empty($_GET['type_id']) && !empty($_GET['id'])) {
  $type_id = $db->real_escape_string($_GET['type_id']);
  $user_id = $db->real_escape_string($_GET['id']);
  $db->query("INSERT INTO users_auth SET type_id = '$type_id', user_id = $user_id");
  
  header("Location: manage-user-access.php");
  die();
} else if(!empty($_GET['remove']) && !empty($_GET['type_id']) && !empty($_GET['id'])) {
  $type_id = $db->real_escape_string($_GET['type_id']);
  $user_id = $db->real_escape_string($_GET['id']);
  $db->query("DELETE FROM users_auth WHERE type_id = '$type_id' AND user_id = $user_id");
  
  header("Location: manage-user-access.php");
  die();
}

$auth_results = $db->query("SELECT id, name FROM users_auth_types");
$auth_types = array();
while($row = $auth_results->fetch_assoc()) {
  $auth_types[$row['name']] = array();
  $users_results = $db->query("SELECT u.id, u.username, a.type_id FROM users_auth a LEFT JOIN users u ON u.id = a.user_id WHERE a.type_id = '" . $row['id'] . "'");
  while($r = $users_results->fetch_assoc()) {
    $auth_types[$row['name']][] = $r;
  }
}

$Config['page']['jquery'] = <<<JQUERY

$("#council_add").autocomplete({
  source: "includes/user_search.php",
  minLength: 2,
  select: function(event, ui) {
    window.location = 'manage-user-access.php?add=1&type_id=1&id=' + ui.item.id;
  }
})

JQUERY;

$Config['page']['title'] = 'Manage User Access';
require_once("includes/header.php");
?>

      <section id="admin-container" class="container">
        <div class="center">
          <h2>Manage User Access</h2>
        </div>

        <div class="admin-wrapper">
          <div class="row">
            <?php 
            foreach($auth_types as $type=>$users) {
              echo '<div class="col-xs-4">';
              echo '<h2>' . $type . '</h2>';

              echo '<ul>';
              foreach($users as $user) {
                echo '<li>' . $user['username'] . ' (<a href="?remove=1&amp;type_id=' . $user['type_id'] . '&amp;id=' . $user['id'] . '">remove</a>)</li>';
              }
              echo '</ul>';

              echo '<h3>Add Member</h3>';
              echo '<form>';
              echo '<input type="text" id="council_add" placeholder="Username" />'; // Ajax autocomplete dropdown via db lookup
              echo '</form>';

              echo '</div>';
            }
            ?>
          </div>
        </div>
      </section>

<?php
require_once("includes/footer.php");