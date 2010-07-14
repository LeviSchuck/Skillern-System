<?php
require("include/base.php");
needrights(7);//need to be at least a TA
//this file will be queried for a reply for another CHQ

//get CHQ information
//the CHQID, the type of chq etc.
$chqid = 20;//(int)trim($_POST['chqid']);
$qtype = 1;//(int)trim($_POST['qtype']);

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
$cols = array();
switch($qtype){
    case 1:
        {//Word->Description
            $cols=array('Define','Description');//defaults
        }
        break;
    case 2:
        {//put in order
            $cols=array('Event');
        }
        break;
    case 3:
        {//multiple choice
            $cols=array(0,'Prompt',"Choice A\nChoiceB");
        }
        break;
    case 4:
        {//sub-versions of put in order
            $cols=array();
        }
        break;
    case 5:
        {//type in word only according to definition
            $cols=array('Define','Description');
            
        }
        break;
    case 6:
        {//this is True and False
            $cols=array(0,'Description');
        }
        break;
    
}
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
