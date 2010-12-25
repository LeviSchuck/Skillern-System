<?php
require("include/base.php");
//print_r($_POST);
rightsSatis(7);
$chapter = (int)trim($_POST['chid']);

$sql = "UPDATE chapters set comment = '".sqlite_escape_string(trim($_POST['data']))."' WHERE chid = $chapter";
sqlite_query($sdb, $sql);
if(!sqlite_last_error($sdb)){
    echo "good";
}else{
    echo "There seemed to have been a problem... ".sqlite_last_error($sdb)." ".sqlite_error_string(sqlite_last_error($sdb)).' '.$sql;
}
?>