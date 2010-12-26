<?php
require("include/base.php");
//print_r($_POST);
rightsSatis(7);
$time = (int)trim($_POST['time']);

$sql = "UPDATE calendar set data = '".sqlite_escape_string(trim($_POST['data']))."' WHERE time = $time";
sqlite_query($sdb, $sql);
if(!sqlite_last_error($sdb)){
    echo "good";
}else{
    echo "There seemed to have been a problem... ".sqlite_last_error($sdb)." ".sqlite_error_string(sqlite_last_error($sdb)).' '.$sql;
}
?>