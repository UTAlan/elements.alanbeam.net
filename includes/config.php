<?php
require_once("local_settings.php");

session_start();

//error_reporting(E_ALL);
//ini_set("display_errors", "ON");
set_time_limit(0);

$db = new mysqli($Config['db']['hostname'], $Config['db']['username'], $Config['db']['password'], $Config['db']['database']);