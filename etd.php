<?php
require_once("template.php");
require_once("database.php");
date_default_timezone_set('America/New_York');

$t = preg_replace('/\.php$/', '.tmpl', __FILE__);

$q = $_GET['urn'];

$dbh = connect_database();
$stmt = $dbh->prepare("SELECT * FROM etd where `urn` = :urn");
// $stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute(array(':urn'=>$q));
$data = $stmt->fetch();


$stmt = $dbh->prepare("SELECT * FROM committee where `urn` = :urn");
// $stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute(array(':urn'=>$q));
foreach($stmt as $faculty) {
	$data['committee'][] = $faculty;
}

echo gen_template($t, $data);
?>
