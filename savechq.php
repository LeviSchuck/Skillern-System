<?php
require("include/base.php");
needrights(7);//need to be at least a TA
$data = stripslashes($_POST['data']);
$sub = (int)trim($_POST['sub']);
$col = (int)trim($_POST['col']);
$chqid = (int)trim($_POST['chqid']);
//query for the data
$sql = "SELECT * FROM chq WHERE id = $chqid";
$result = sqlite_query($sdb,$sql);
$info = array();
while($row = sqlite_fetch_array($result)){
    $info = $row;
}
//determine the column to be edited
$inputcol = $info['col'.(chr(ord('a')+$col))];
//put determined column into array
$inputcol = explode("|",$inputcol);
//change the array and put it back into imploded form
$inputcol[$sub] = $data;

//inject into sql
$sql = "UPDATE chq SET 'col".(chr(ord('a')+$col))."' = '".sqlite_escape_string(implode("|",$inputcol))."' WHERE id = $chqid";
$result = sqlite_query($sdb,$sql);
if(sqlite_last_error($sdb) != 0){
    echo sqlite_error_string(sqlite_last_error($sdb));
    //echo "\n$sql";
}else{
    echo "good";//!
    $_SESSION['editor']['qdata'][$sub][$col] = $data;
}

?>