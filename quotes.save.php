<?php
require("include/base.php");
needrights(7);
$data = sqlite_escape_string(trim($_POST['data']));
$whiches = array(0=>null, 1=>'quo',2=>'bywho');
$which = $whiches[(int)$_POST['which']];
$id = (int)$_POST['qid'];
$sql = "UPDATE squotes set $which = '$data' where ID=$id";
sqlite_query($sdb, $sql);
if(!sqlite_last_error($sdb)){
    echo "good";
}else{
    echo "There seemed to have been a problem... ".sqlite_last_error($sdb)." ".sqlite_error_string(sqlite_last_error($sdb)).' '.$sql;
}

?>