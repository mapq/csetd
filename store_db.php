<?php
require_once("database.php");
date_default_timezone_set('America/New_York');

#---------------------------------------------------------
function writeToSqlite($record, $connection)
{
  echo "\nProcessing ETD {$record['urn']}\n";

  // see if we have this ETD already
  $stmt = $connection->prepare("SELECT * FROM etd WHERE `urn` = :urn");
  // $stmt->setFetchMode(PDO::FETCH_ASSOC);  
  $stmt->execute(array(':urn'=>$record['urn']));
  $row = $stmt->fetch();

  /* Check the number of rows that match the SELECT statement */
  if ($row) {
    $stmt = $connection->prepare("UPDATE `etd` SET `name` = :name, `degree` = :degree, 
      `title` = :title, `url` = :url, `abstract` = :abstract, `date` = :date, 
      `department` = :department WHERE `urn`= :urn; ");
    $res = $stmt->execute(array(
        ':name'=>$record['dc.contributor.author'],
        ':degree'=>$record['dc.description.degree'],
        ':title'=>$record['dc.title'],
        ':url'=>$record['dc.identifier.uri'],
        ':abstract'=>$record['dc.description.abstract'],
        ':date'=>$record['dc.date.issued'],//date("Y-m-d", $record['dc.date.issued']),
        ':department'=>$record['dc.contributor.department'],
        ':urn'=>$record['urn']));
  }
  else {
    $stmt = $connection->prepare("INSERT INTO etd (`urn`, `name`, `degree`, `title`, `url`, `abstract`,
        `date`, `department`) VALUES (:urn, :name, :degree, :title, :url, :abstract, :date, :department)");
    $res = $stmt->execute(array(
        ':name'=>$record['dc.contributor.author'],
        ':degree'=>$record['dc.description.degree'],
        ':title'=>$record['dc.title'],
        ':url'=>$record['dc.identifier.uri'],
        ':abstract'=>$record['dc.description.abstract'],
        ':date'=>$record['dc.date.issued'], //date("Y-m-d", $record['dc.date.issued']),
        ':department'=>$record['dc.contributor.department'],
        ':urn'=>$record['urn']));
  }
    
  // Now we have added the etd record, lets add the committee members records
  $etd = $record['urn'];

  // if we have a chair...
  if (is_array($record['dc.contributor.committeechair']) && 
    !is_string($record['dc.contributor.committeechair'])) {
    if (count($record['dc.contributor.committeechair']) == 1) {
      $query = "INSERT INTO committee (`name`, `role`, `urn`) VALUES (:name, :role, :urn)";
      $stmt = $connection->prepare($query);
      $stmt->execute(array(
      ':name'=>$record['dc.contributor.committeechair'][0],
      ':role'=>"Chair",
      ':urn'=>$etd));
    }
    else if (count($record['dc.contributor.committeechair']) > 1) {
      foreach($record['dc.contributor.committeechair'] as $chair) {
        $query = "INSERT INTO committee (`name`, `role`, `urn`) VALUES (:name, :role, :urn)";
        $stmt = $connection->prepare($query);
        $stmt->execute(array(
        ':name'=>$chair,
        ':role'=>"Co-Chair",
        ':urn'=>$etd));
      }
    }

  }
  else if (is_string($record['dc.contributor.committeechair']) && 
    (strlen($record['dc.contributor.committeechair']) > 0)) {
    $query = "INSERT INTO committee (`name`, `role`, `urn`) VALUES (:name, :role, :urn)";
    $stmt = $connection->prepare($query);
    $stmt->execute(array(
    ':name'=>$record['dc.contributor.committeechair'],
    ':role'=>"Chair",
    ':urn'=>$etd));
  }


  foreach ($record['dc.contributor.committeemember'] as $faculty) {
    if ((strlen($faculty) > 0)) {
      $query = "INSERT INTO committee (`name`, `role`, `urn`) VALUES (:name, :role, :urn)";
      $stmt = $connection->prepare($query);
      $stmt->execute(array(
      ':name'=>$faculty,
      ':role'=>"Member",
      ':urn'=>$etd));
    }
  }
}

#---------------------------------------------------------
# Main
#---------------------------------------------------------

// Set local to Spanish so that special characters are
// treated correctly in string comparisons
setlocale(LC_COLLATE, "es_ES.ISO8859-1", "es_ES.UTF-8");

  $dbh = connect_database();

  $string = file_get_contents("unused/library.json");
  $data = json_decode($string, true);
  foreach($data['etds'] as $q) {
    writeToSqlite($q, $dbh);
  }

  $string = file_get_contents("unused/vtech.json");
  $data = json_decode($string, true);
  foreach($data['etds'] as $q) {
    writeToSqlite($q, $dbh);
  }

  $string = file_get_contents("unused/missingphd.json");
  $data = json_decode($string, true);
  foreach($data['etds'] as $q) {
    writeToSqlite($q, $dbh);
  }

?> 
