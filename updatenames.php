<?php	// updatenames.php
require_once("database.php");
date_default_timezone_set('America/New_York');

  $dbh = connect_database();

  $string = file_get_contents("updatenames.json");
  $data = json_decode($string, true);

  echo "<ul>";
  foreach($data['updates'] as $q) {
    // print_r($q);
  	foreach($q['tofix'] as $name) {
      echo "<li>Updating {$name['name']} with {$q['correct']}</li>\n";
	    $stmt = $dbh->prepare("UPDATE committee 
	    	SET name = :correct WHERE name = :tofix;");
	    $res = $stmt->execute(array(
	        ':tofix'=>$name['name'],
	        ':correct'=>$q['correct']));
    }
  }

  // Fix a special case, we have with middle initials not being
  // terminated with a .

  $stmt = $dbh->query("SELECT distinct name from committee where substr(name,-1) = '.';");
  // $stmt->setFetchMode(PDO::FETCH_ASSOC);
  $results = array();
  foreach($stmt as $n)
    $names[] = $n['name'];

  foreach ($names as $n) {
    $o = substr($n, 0, strlen($n)-1);
    echo "<li>Updating {$o} with {$n}</li>\n";
    $stmt = $dbh->prepare("UPDATE committee SET name = :upd WHERE name = :noperiod;");
    $stmt->execute(array(
        ':upd'=>$n, ':noperiod'=>$o));
  }
  echo "</ul>";
?>