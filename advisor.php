<?php
require_once("template.php");
require_once("database.php");
date_default_timezone_set('America/New_York');

$data = array();
$dbh = connect_database();

// select distinct name from committee where role = "Chair" order by name;

if (isset($_GET['q'])) {
	$q = $_GET['q'];
	$data['any'] = true;
	$stmt = $dbh->prepare("select * from committee, etd where committee.role = 'Chair' and committee.name = ? and committee.urn = etd.urn order by date desc;");
	// $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$stmt->bindParam(1, $q);
	$stmt->execute();
	$data['advisor'] = $q;
	foreach ($stmt as $r)
		$data['etds'][] = $r;

	$stmt = $dbh->prepare("select * from committee, etd where committee.role = 'Member' and committee.name = ? and committee.urn = etd.urn order by date desc;");
	// $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$stmt->bindParam(1, $q);
	$stmt->execute();
	foreach ($stmt as $r)
		$data['member'][] = $r;

	$t = preg_replace('/\.php$/', '.tmpl', __FILE__);
	echo gen_template($t, $data);
}
else {
	// This counts number of chair
	// SELECT count(name) from (select name FROM committee where role = 'Chair' group by name);
	$stmt = $dbh->query("SELECT count(name) from (select name FROM committee where role = 'Chair' group by name);");
	// $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$r = $stmt->fetch();
	$data['count'] = $r['count(name)'];

	$stmt = $dbh->query("select distinct name from committee where role = 'Chair' order by name;");
	// $stmt->setFetchMode(PDO::FETCH_ASSOC);
	$i = 1;
	$colsize = 1+$data['count']/3;
	foreach ($stmt as $r) {
		if ($i++ % $colsize == 0)
			$r['colbreak'] = true;
		else
			$r['colbreak'] = false;
		$data['chairs'][] = $r;
	}
	$t = preg_replace('/\.php$/', '.tmpl', __FILE__);
	echo gen_template($t, $data);
}
?>
