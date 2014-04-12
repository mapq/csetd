<?php
require_once("template.php");
require_once("database.php");
date_default_timezone_set('America/New_York');

$t = preg_replace('/\.php$/', '.tmpl', __FILE__);

$data = array();
$dbh = connect_database();

$stmt = $dbh->query("SELECT count(urn) FROM etd");
// $stmt->setFetchMode(PDO::FETCH_ASSOC);
$r = $stmt->fetch();
$data['count'] = $r['count(urn)'];

// SELECT urn,degree,substr(date,0,5) as year,date FROM etd ORDER by year Desc, degree desc, date;
// Do not sort by degree, show all in order of date
$stmt = $dbh->prepare("SELECT urn,degree,name,title,date,substr(date,0,5) as year 
	FROM etd ORDER by year Desc, date Desc");
// $stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->execute();
$prevyear = "";
foreach ($stmt as $r) {
	if ($prevyear != $r['year']) {
		$r['break'] = true;
		$prevyear = $r['year'];
	}
	else {
		$r['break'] = false;
	}
	$data['etds'][] = $r;
}

echo gen_template($t, $data);
?>
