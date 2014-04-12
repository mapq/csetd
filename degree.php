<?php
require_once("template.php");
require_once("database.php");
date_default_timezone_set('America/New_York');

$t = preg_replace('/\.php$/', '.tmpl', __FILE__);

$q = strtoupper($_GET['q']);
$data = array();
$dbh = connect_database();

$stmt = $dbh->query("SELECT count(urn) FROM etd where UPPER(`degree`) = :degree");
// $stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute(array(':degree'=>$q));
$r = $stmt->fetch();
$data['count'] = $r['count(urn)'];


$stmt = $dbh->prepare("SELECT * FROM etd where UPPER(`degree`) = :degree ORDER by date desc");
// $stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute(array(':degree'=>$q));
foreach ($stmt as $r) {
	// $r['date'] = date("Y-m-d", $r['date']);
	$data['etds'][] = $r;
}

echo gen_template($t, $data);
?>