<?php
require_once("template.php");
require_once("database.php");
date_default_timezone_set('America/New_York');
$t = preg_replace('/\.php$/', '.tmpl', __FILE__);

$dbh = connect_database();
$data = array();

$stmt = $dbh->query("SELECT count(urn) FROM etd where upper(degree) = 'PHD'");
// $stmt->setFetchMode(PDO::FETCH_ASSOC);
$r = $stmt->fetch();
$data['phdcount'] = $r['count(urn)'];

$stmt = $dbh->query("SELECT * FROM etd WHERE upper(degree) = 'PHD' ORDER BY date DESC LIMIT 10");
// $stmt->setFetchMode(PDO::FETCH_ASSOC);
foreach($stmt as $r)
	$data['phdetds'][] = $r;



$stmt = $dbh->query("SELECT count(urn) FROM etd WHERE UPPER(`degree`) = 'MS'");
// $stmt->setFetchMode(PDO::FETCH_ASSOC);
$r = $stmt->fetch();
$data['mscount'] = $r['count(urn)'];

$stmt = $dbh->query("SELECT * FROM etd WHERE UPPER(`degree`) = 'MS' ORDER BY date DESC LIMIT 10");
// $stmt->setFetchMode(PDO::FETCH_ASSOC);
foreach ($stmt as $r)
	$data['msetds'][] = $r;




echo gen_template($t, $data);
?>
