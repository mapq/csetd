<?php

function connect_database()
{
  try {     // SQLite Database
    $DBH = new PDO("sqlite:etd.sqlite");  
    $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // added this as default mode
    $DBH->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $DBH;
  }  
  catch(PDOException $e) {  
    echo "Connection failed: " . $e->getMessage();  
    exit();
  }

}
?>