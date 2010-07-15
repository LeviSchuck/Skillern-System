<?php
require("include/base.php");
needrights(7);//need to be at least a TA
//this file will be queried for a reply for another CHQ

//get CHQ information
//the CHQID, the type of chq etc.
$chqid = (int)trim($_POST['chqid']);
$qtype = (int)trim($_POST['qtype']);

//get current data
$sql = "SELECT * FROM chq WHERE id = $chqid";
$result = sqlite_query($sdb,$sql);
$info = array();
while($row = sqlite_fetch_array($result)){
    $info = $row;
}
unset($info[0],$info[1],$info[2],$info[3],$info[4],$info[5],$info[6]);
//put into array


//detect how many columns to use based on chq type
//augment array with 1 row on all columns that count.
$cols = defaultcols($qtype);
$count = count($cols);
for($col = 0; $col < $count; $col++){
    $tempcol = explode("|",$info['col'.chr(ord('a')+$col)]);
    $tempcol[] = $cols[$col];
    $sql = "UPDATE chq SET 'col".(chr(ord('a')+$col))."' = '".sqlite_escape_string(implode("|", $tempcol))."' WHERE id = $chqid";
    $result = sqlite_query($sdb,$sql);
    //echo $sql."<br />\n";
    if(sqlite_last_error($sdb) != 0){
        echo "<h3>There seemed to have been an error: ";
        echo sqlite_error_string(sqlite_last_error($sdb))."<br />";
    }
}



?>
