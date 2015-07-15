<?php
$Config['page']['auth']['council'] = 1;
require_once("includes/config.php");

if(!empty($_POST)) {
  $id = $db->real_escape_string($_POST['id']);
  $name = $db->real_escape_string($_POST['name']);
  $start_date = date('Y-m-d H:i:s', strtotime($_POST['start_date']));
  $end_date = date('Y-m-d H:i:s', strtotime($_POST['end_date']));

  if(strtotime($start_date) === false || strtotime($end_date) === false) {
    header("Location: index.php?invalid=1&id=" . $id);
    die();
  }

  if(empty($id)) {
    $db->query("INSERT INTO council_elections SET name = '$name', start_date = '$start_date', end_date = '$end_date'");
  } else {
    $db->query("UPDATE council_elections SET name = '$name', start_date = '$start_date', end_date = '$end_date' WHERE id = $id");
  }

  header("Location: council-setup.php");
  die();
} else if(!empty($_GET['id'])) {
  $election_id = $db->real_escape_string($_GET['id']);
  $election_result = $db->query("SELECT * FROM council_elections WHERE id = $election_id");
  if($election_result->num_rows == 1) {
    $election = $election_result->fetch_assoc();
  }
}

$Config['page']['title'] = 'Create/Edit Council';
require_once("includes/header.php");
?>

      <section id="admin-container" class="container">
        <div class="center">
          <h2><?php echo (empty($election) ? 'Create' : 'Edit'); ?> Council</h2>
          <p>Dates should be GMT+0 and in the following format: YYYY-MM-DD HH:MM:SS</p>
        </div>

        <?php if(!empty($_GET['invalid'])) { ?>
        <div class="row error">
          <div class="col-xs-4 col-xs-offset-4">
            <p class="error">Invalid start and/or end dates. Please try again.</p>
          </div>
        </div>
        <?php } ?>

        <div class="admin-wrapper">
          <div class="row">
            <div class="col-xs-4 col-xs-offset-4">
              <form id="council-form" class="council-form" name="council-form" method="post" action="#">
                <div class="form-group">
                  <label>Election Name</label>
                  <input type="text" name="name" class="form-control" required="required" value="<?php echo $election['name']; ?>" />
                </div>
                <div class="form-group">
                  <label>Start Date/Time</label>
                  <input type="text" name="start_date" class="form-control" required="required" value="<?php echo $election['start_date']; ?>" />
                </div> 
                <div class="form-group">
                  <label>End Date/Time</label>
                  <input type="text" name="end_date" class="form-control" required="required" value="<?php echo $election['end_date']; ?>" />
                </div>                      
                <div class="form-group">
                  <input type="hidden" name="id" value="<?php echo $election['id']; ?>" />
                  <button type="submit" name="submit" class="btn btn-primary btn-lg" required="required">Save</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>

<?php
require_once("includes/footer.php");