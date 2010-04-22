<?php
/*
$mysqli = new mysqli('bghsdb.fatcowmysql.com', 'skillz', 'skAPUS', 'skillern');

// check connection 
if (mysqli_connect_errno()) {
    printf("Sorry there has been an error. Please try again in a few moments.<br>Connect failed: %s\n", mysqli_connect_error());
    die("");
}
$conn = mysql_connect('bghsdb.fatcowmysql.com', 'skillz', 'skAPUS');

if (!$conn) {
    die( " Unable to connect to DB: " . mysql_error());
    exit;
}
  
if (!mysql_select_db( 'skillern')) {
    die( " Unable to select the DB: " . mysql_error());
    exit;
}
*/
if ($sdb = sqlite_open('skillerndb', 0666, $sqliteerror)) {
} else {
  die ($sqliteerror);
}

?>