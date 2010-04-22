<?php

/**
 * @author Kloplop321
 * @copyright 2010
 */

require("include/base.php");

$chapter = (int)trim($_REQUEST['c']);
$sql = "SELECT comment FROM chapters WHERE chid = $chapter LIMIT 1";
$resu = sqlite_query($sdb, $sql);
$ctitle = sqlite_fetch_array($resu);
$title = strtoupper($ctitle[0]);

$top1 = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> Chapter ';
$top2 = '</title>
<link rel="stylesheet" type="text/css" href="css/workbook.css" media="screen" />
<link rel="stylesheet" type="text/css" href="css/workbook.css" media="print" />
</head>

<body>
<div style="width: 960px; margin: auto;">

<div class="chapterhead">Chapter ';
$top3 = '</div>
';

echo $top1 . $chapter . $top2 . $chapter ."<br>".$title. $top3;
//get the type information
$types = array();
$sql = "select * from qtypes ORDER BY orderi ASC";
$resu = sqlite_query($sdb, $sql);
$types2 = array();
while($row = sqlite_fetch_array($resu)){
    $types[$row[0]] = $row;
    $types2[] = $row[0];
}

foreach($types2 as $typo){
    
    
    //get the info for the sections based on the types.
    $sql = "select * from chq WHERE chid = $chapter AND type = $typo";
    $resu = sqlite_query($sdb, $sql);
    while($row = sqlite_fetch_array($resu)){    
        $cola = explode('|',$row['cola']);
        $colb = explode('|',$row['colb']);
        $colc = explode('|',$row['colc']);
	$cola = array_map('removecrap',$cola);
	$colb = array_map('removecrap',$colb);
	$colc = array_map('removecrap',$colc);
	$countcols = max(count($cola), count($colb));
	if(max(strlen($cola[0]),strlen($colb[0])) > 0){
	    $type = $row['type'];
	    $s = $types[$type]['preset'];
	    $typestr = 'type'.$types[$typo][0];
	    echo '<div class="typename">'.$types[$typo]['name'].'</div>'."\n";//the name
	    
	    for($pos = 0; $pos < $countcols; $pos++){
		echo '<div class="contentrow">';
		echo '<div class="'.$typestr.'">';
		echo '<div class="nbr">';
		echo $pos+1;
		echo '</div>'."\n";//end number
		switch($s){
		    case 1://word: definition
			{
			    echo '<div class="cola">';
			    echo $cola[$pos];
			    echo '</div>';//end cola
			    echo '<div class="colb">';
			    echo $colb[$pos];
			    echo '</div>';//end cola
			}
			break;
		    case 2:
			{
			    echo '<div class="colb">'; //actually column A, but for CSS purposes..
			    echo $cola[$pos];
			    echo '</div>';//end cola
			}
			break;
		    case 3:
			{
			    
			    echo '<div class="colb">';
			    echo $colb[$pos];
			    echo '</div>';//end cola
			    echo '<div class="colc">';
			    $colc[$pos] = explode("\n", $colc[$pos]);
			    $colccount = count($colc[$pos]);
			    $letA = ord("A");
			    for($colcpos = 0; $colcpos < $colccount; $colcpos++){
				if($colcpos == $cola[$pos]){
				    $colccss = ' ccorrect';
				}else{
				    $colccss = '';
				}
				echo '<div class="colcletter'.$colccss.'">';
				echo chr($letA + $colcpos);
				echo '</div>';//end letter
				echo '<div class="colcline'.$colccss.'">';
				echo removecrap($colc[$pos][$colcpos]);
				echo '</div>';//end option.
			    }
			    echo '</div>';//end cola
			}
			break;
		    case 6:
			{
			    echo '<div class="cola">';
			    if((int)$cola[$pos]){
				echo "True";
			    }else{
				echo "False";
			    }
			    echo '</div>';//end cola
			    echo '<div class="colb">';
			    echo $colb[$pos];
			    echo '</div>';//end cola
			}
			break;
		    
		    
		}
		echo '</div>'; //end type class
		echo '</div>';//end content row.
	    }
	}
    
    }
}
$end1 = '</table></div>
</body>
</html>';
echo $end1;

?>