<?php
require_once("template.php");
require_once("database.php");
date_default_timezone_set('America/New_York');

$data = array();
$dbh = connect_database();

$t = preg_replace('/\.php$/', '.tmpl', __FILE__);
echo gen_template($t, $data);

?>
