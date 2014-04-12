<?php
require_once("template.php");
require_once("database.php");
date_default_timezone_set('America/New_York');

if (isset($_GET['q']))
	$q = $_GET['q'];
else
	$q = "Chair";

$parms = array();
if ($q == "Chair") {
	$parms['title'] = $q;
	$parms['showingchair'] = 'class="active"';
	$parms['showingmember'] = "";
}
else {
	$parms['title'] = $q;
	$parms['showingchair'] = "";
	$parms['showingmember'] = 'class="active"';
}

// Do a little clean up
$day = date("d") - 1;
$files = glob(date("Y-m-$day*"));
foreach($files as $f)
	unlink($f);

// Now do the real work
$dbh = connect_database();
$d = date("Y-m-d")."-".$q;
$tmpfname = tempnam("./", $d);
rename($tmpfname, $tmpfname.".json");
$tmpfname = $tmpfname.".json";
$handle = fopen($tmpfname, "w");

$stmt = $dbh->prepare("SELECT name, urn, count(role) as k
	FROM committee WHERE role = :role group by name;");
$stmt->execute(array(":role"=> $q));

$data = array();
$data['name'] = "etds";
$data['children'] = array();
$data['children'][0] = array('name'=> $q);
$t = array();
foreach ($stmt as $r) {
	// if ($r['k'] > 1)
		$t[] = array("name"=>
			substr($r['name'], 0, strpos($r['name'], ",")),
			"fullName"=>$r['name'],
			"size"=>$r['k']);
}
$data['children'][1] = array('children' => $t);

$d = json_encode($data);
fwrite($handle, $d);
fclose($handle);

$f = pathinfo($tmpfname);
$t = preg_replace('/\.php$/', '.tmpl', __FILE__);
$parms['file'] = $f['basename'];
echo gen_template($t, $parms);
?>
