<?php
require_once("template.php");
require_once("database.php");
date_default_timezone_set('America/New_York');

$dbh = connect_database();
$q = "%".$_POST['q']."%";
$data = array();

$stmt = $dbh->query("SELECT count(urn) FROM etd where name like ? or title like ? ");
// $stmt->setFetchMode(PDO::FETCH_ASSOC);
$stmt->bindParam(1, $q);
$stmt->bindParam(2, $q);
$stmt->execute();
$r = $stmt->fetch();
$data['count'] = $r['count(urn)'];


$sql = "SELECT * FROM etd WHERE name like ? or title like ? ORDER BY name";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(1, $q);
$stmt->bindParam(2, $q);
$stmt->execute();
// $stmt->setFetchMode(PDO::FETCH_ASSOC);
$data['q'] = $_POST['q'];
$pattern = "/(".$data['q'].")/i";
$replace1 = "<span class='label label-primary'>$1</span>";
$replace2 = "<span class='label label-info'>$1</span>";
foreach($stmt as $r) {
	$r['name'] = preg_replace($pattern, $replace1, $r['name']);
	$r['title'] = preg_replace($pattern, $replace2, $r['title']);
	$data['etds'][] = $r;
}
$t = preg_replace('/\.php$/', '.tmpl', __FILE__);
echo gen_template($t, $data);

?>
