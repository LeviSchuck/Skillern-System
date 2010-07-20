<?php
require("include/base.php");
needrights(7);//need to be at minimum a TA
//ok, by now we should be authorized

//grab the current chq ID
$chqid = (int)trim($_POST['chqid']);
$qtype = (int)trim($_POST['qtype']);

//grab the array of new positions
//convert to integer, map array with intval
$neworder = array_map('intval',$_POST['no']);
//now make a 'new' array
$newdata = array();
//grab data from database.
$sql = "SELECT * FROM chq WHERE id = $chqid";
$result = sqlite_query($sdb,$sql);
$info = array();
while($row = sqlite_fetch_array($result)){
    $info = $row;
}
unset($info[0],$info[1],$info[2],$info[3],$info[4],$info[5],$info[6]);//make it clean for debugging
//determine column counts. function defaultcols with parameter qtype
$colcount = count(defaultcols($qtype));
//now, make col* to array with expload
$olddata = array();
for($x = 0; $x < $colcount; $x++){
    $olddata[$x]= explode('|',$info['col'.(chr(ord('a')+$x))]);
    //echo 'col'.(chr(ord('a')+$x))." count: ".count($olddata[$x]).'<br />';
}
//count objects in new array
$newcount = count($neworder);
//echo "New Data count: $newcount<br />\n";
//loop through foreach all old objects,
for($col = 0; $col < $colcount; $col++){
    for($x = 0; $x < $newcount; $x++){
        $newdata[$col][$x] = $olddata[$col][$neworder[$x]];
    }
    //implode to a string
    $newdata[$col] = implode('|',$newdata[$col]);
}
//save back to database
for($col = 0; $col < $colcount; $col++){
    $sql = "UPDATE chq SET 'col".(chr(ord('a')+$col))."' = '".sqlite_escape_string($newdata[$col])."' WHERE id = $chqid";
    $result = sqlite_query($sdb,$sql);
    //echo $sql;
    //see if there was an error.........
    if(sqlite_last_error($sdb) != 0){
        echo "<h3>There seemed to have been an error: ";
        echo sqlite_error_string(sqlite_last_error($sdb))."<br />";
        //just a notice, this does not roll back changes. 
    }
}




?>