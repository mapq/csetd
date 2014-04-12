<?php
require_once("template.php");
require_once("database.php");
date_default_timezone_set('America/New_York');


$data = array();
$dbh = connect_database();
/*
SELECT url, date, etd.name as student, etd.title, substr(etd.date,0,5) as year, committee.name as advisor
	FROM etd, committee 
	where etd.urn = committee.urn and UPPER(`degree`) = 'PHD' and committee.role = 'Chair' 
	ORDER by date desc;
*/
$stmt = $dbh->query("SELECT url, date, etd.name as student, etd.title, substr(etd.date,0,5) as year, committee.name as advisor
	FROM etd
	LEFT JOIN committee ON etd.urn = committee.urn and committee.role = 'Chair'
	WHERE UPPER(`degree`) = 'PHD'
	ORDER by date desc;");
$prevyear = "";
$first = true;
$count = 0;
foreach ($stmt as $r) {
	$r['break'] = ($r['year'] != $prevyear && (!$first));
	$first = false;
	$prevyear = $r['year'];
	$data['etds'][] = $r;
	$count++;
}

$data['count'] = $count;
if (isset($_GET['csv'])) {
	$t = preg_replace('/\.php$/', 'csv.tmpl', __FILE__);
	echo gen_template($t, $data);
}
else {
	$t = preg_replace('/\.php$/', '.tmpl', __FILE__);
	echo gen_template($t, $data);
}

?>