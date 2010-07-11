<?php
require("include/base.php");
needrights(7);//need to be at least a TA
//this file will be queried for a reply for another CHQ

//get CHQ information
//the CHQID, the type of chq etc.
$chqid = 22;//(int)trim($_POST['chqid']);
$qtype = 3;//(int)trim($_POST['qtype']);

//get current data
$sql = "SELECT * FROM chq WHERE id = $chqid";
$result = sqlite_query($sdb,$sql);
$info = array();
while($row = sqlite_fetch_array($result)){
    $info = $row;
}

//put into array
print_r($info);

//detect how many columns to use based on chq type
//augment array with 1 row on all columns that count.

//save back to database

//provide new data row xhtml to the browser
?>