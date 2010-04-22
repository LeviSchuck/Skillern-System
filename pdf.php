<?php

/**
 * @author Kloplop321.com
 * @copyright 2009
 */
include ("themes.php");
include ("connect.php");
$loc = getcwd();
$loc .= "/font/";
define('FPDF_FONTPATH', $loc);
require ('fpdf.php');
class PDF extends FPDF {
    var $chapterID;
    //Simple table
    function BasicTable($data) {
        //Data
        foreach ($data as $row) {
            foreach ($row as $col)
                $this->Cell(40, 6, $col, 0);
            $this->Ln();
        }
    }
    function Header() {
        $this->SetFont('Times', '', 12);
        $this->Cell(0, 10, 'Chapter ' . $this->chapterID.' : Page '.$this->PageNo(), 0, 1, 'R');
        $this->Ln(2);

    }
	function baseHeight($offset = 0){
		return (8+$offset);
	}
	function baseFont($offset = 0){
		return (14 + $offset);
	}
	function newPageAt(){
		return 100;
	}
    function begaend($text, $period = true) {
        $text = trim($text);
        //capitolize first letter
        $text = ucfirst($text);
        $end = substr($text, strlen($text) - 1);
        if ($end != "." && $period) {
            $text .= ".";
        }
        return $text;
    }
    function spacing($text, $len) {
        $text = html_entity_decode($text);
        $words = preg_split('/[\s]/i', $text);
        $count = count($words);
        $final = array();
        for ($x = 0; $x < $count; $x++) {
            $tempstr = "";
            for ($y = 0; $y <= $x; $y++) {
                $tempstr .= $words[$y] . " ";
            }

            if ($this->GetStringWidth($tempstr) > $len) {
                $tempstr = "";
                if ($x > 0) {
                    for ($y = 0; $y < $x; $y++) {
                        $tempstr .= $words[$y] . " ";
                    }
                } else {
                    $tempstr .= $words[0];
                    $x++;
                }

                $final[] = $tempstr;

                $tempstr = "";
                for ($y = $x; $y < $count; $y++) {
                    $tempstr .= $words[$y] . " ";
                }
                $final[] = $tempstr;
                $x = $count;

            } else {
                //echo $this->GetStringWidth($tempstr)." $tempstr\n\n";
            }
        }
        if (count($final) < 1) {
            $final[] = $text;
        }
        return $final;
    }
    //Glossary
    function Gloss($data) {
        //Data
		$col = 42;
        $at = 1;
        $wid = 201-$col;
        $hit = $this->baseHeight();
        foreach ($data as $row) {

            $this->SetFont('Times', 'B', $this->baseFont());
            $ary = $this->spacing(ucfirst($row[0]), $col);
            $this->Cell($col, $hit, trim($ary[0]), 0);
            //echo print_r($ary);
            $this->SetFont('Times', '', $this->baseFont(1));
            $arr = $this->spacing("$at. " . $this->begaend($row[1]), $wid);
            $this->Cell(65, $hit, trim($arr[0]), 0);
			if((isset($arr[1]) || isset($ary[1])) && $itt < 8){
            $this->Ln($this->baseHeight()-2);
			}else{
			$this->Ln($this->baseHeight()+1);
			}
            $itt = 0;
            while ((isset($arr[1]) || isset($ary[1])) && $itt < 8) {
                $itt++;
                $arr = $this->spacing(trim($arr[1]), $wid - 5);
                if (isset($ary[1])) {
                    $this->SetFont('Times', 'B', $this->baseFont());
                    $ary = $this->spacing(trim($ary[1]), $col);
                    $this->Cell($col+5, $hit, $ary[0], 0);
                } else {
                    $this->Cell($col+5, $hit, '', 0);
                }
                $this->SetFont('Times', '', $this->baseFont(1));
                $this->Cell(60, $hit, trim($arr[0]), 0);
                $this->Ln($this->baseHeight()-2);
            }
            $at++;
        }
    }

    //T&F
    function trufal($data) {
        //Data
        $at = 1;
        $wid = 181;
        $hit = $this->baseHeight();
        foreach ($data as $row) {

            $this->SetFont('Times', 'B', $this->baseFont());

            $this->Cell(20, $hit, trim("$at. " . tf($row[0])), 0);
            //echo print_r($ary);
            $this->SetFont('Times', '', $this->baseFont(1));
            $arr = $this->spacing($this->begaend($row[1]), $wid);
            $this->Cell(65, $hit, trim($arr[0]), 0);
            $this->Ln();
            $itt = 0;
            while (isset($arr[1]) && $itt < 5) {
                $itt++;
                $arr = $this->spacing(trim($arr[1]), $wid - 5);
                $this->Cell(25, $hit, '', 0);
                $this->SetFont('Times', '', $this->baseFont(1));
                $this->Cell($wid, $hit, trim($arr[0]), 0);
                $this->Ln();
            }
            $at++;
        }
    }

    //Multiple Choice
    function multicho($data) {
        $howmany = count($data);
        $at = 1;
        $wid = 198;
        $hit = $this->baseHeight(-2);
        foreach ($data as $row) {
            //echo ($x + 1) . ". " . $row[0] . "<br>\n";
            if ($this->GetY() > 250) {
                $this->AddPage();
            }
            $this->SetFont('Times', '', $this->baseFont(0.5));
            $arr = $this->spacing("$at. " . $this->begaend($row[0], false), $wid);
            $this->Cell(65, $hit, trim($arr[0]), 0);
            $this->Ln();
            $itt = 0;
            while (isset($arr[1]) && $itt < 5) {
                $itt++;
                $arr = $this->spacing(trim($arr[1]), $wid - 5);
                $this->Cell($wid, $hit, trim($arr[0]), 0);
                $this->Ln();
            }
            $quest = count($row[2]);

            for ($i = 0; $i < $quest; $i++) {
                $this->Cell(10, $hit, '', 0);
                if ($i == (int)$row[1]) {
                    $this->SetFont('Times', 'B', $this->baseFont(-2));
                    //echo "<b>" . chr(65 + $i) . ". " . $mu[$x][$i] . "</b>\n<br>";
                    $arr = $this->spacing(chr(65 + $i) . ". " . $this->begaend($row[2][$i]), $wid - 10);
                    $this->Cell(65, $hit, trim($arr[0]), 0, 1);
                    $itt = 0;
                    while (isset($arr[1]) && $itt < 5) {
                        $itt++;
                        $this->Cell(10, $hit, '', 0);
                        $arr = $this->spacing(trim($arr[1]), $wid - 10);
                        $this->Cell($wid - 10, $hit, trim($arr[0]), 0, 1);
                    }
                } else {
                    $this->SetFont('Times', '', $this->baseFont(-1));
                    $arr = $this->spacing(chr(65 + $i) . ". " . $this->begaend($row[2][$i]), $wid - 10);
                    $this->Cell(65, $hit, trim($arr[0]), 0, 1);
                    $itt = 0;
                    while (isset($arr[1]) && $itt < 5) {
                        $itt++;
                        $this->Cell(10, $hit, '', 0);
                        $arr = $this->spacing(trim($arr[1]), $wid - 10);
                        $this->Cell($wid - 10, $hit, trim($arr[0]), 0, 1);
                    }
                    //echo chr(65 + $i) . ". " . $mu[$x][$i] . "\n<br>";
                }
                //$this->Ln();
            }

            $at++;
        }
    }

    //Identification
    function Ident($data) {
        $at = 1;
		$col = 40;
        $wid = 200-$col;
        $hit = $this->baseHeight();
        foreach ($data as $row) {

            $this->SetFont('Times', 'B', $this->baseFont());
            $ary = $this->spacing(ucfirst($row[0]), $col);
            $this->Cell($col, $hit, trim($ary[0]), 0);
            //echo print_r($ary);
            $this->SetFont('Times', '', $this->baseFont(1));
            $arr = $this->spacing("$at. " . $this->begaend($row[1]), $wid);
            $this->Cell(65, $hit, trim($arr[0]), 0);
            $this->Ln();
            $itt = 0;
            while ((isset($arr[1]) || isset($ary[1])) && $itt < 8) {
                $itt++;
                $arr = $this->spacing(trim($arr[1]), $wid - 5);
                if (isset($ary[1])) {
                    $this->SetFont('Times', 'B', $this->baseFont());
                    $ary = $this->spacing(trim($ary[1]), 35);
                    $this->Cell($col+5, $hit, $ary[0], 0);
                } else {
                    $this->Cell($col+5, $hit, '', 0);
                }
                $this->SetFont('Times', '', $this->baseFont(1));
                $this->Cell(60, $hit, trim($arr[0]), 0);
                $this->Ln();
            }
            $at++;
        }
    }
    //matching people places and events
    function mppe($data) {
        $at = 1;
		$col = 42;
        $wid = 200-$col;
        $hit = $this->baseHeight();
        foreach ($data as $row) {

            $this->SetFont('Times', 'B', $this->baseFont());
            $ary = $this->spacing(ucfirst($row[0]), $col);
            $this->Cell($col+5, $hit, trim($ary[0]), 0);
            //echo print_r($ary);
            $this->SetFont('Times', '', $this->baseFont(1));
            $arr = $this->spacing("$at. " . $this->begaend($row[1]), $wid);
            $this->Cell(65, $hit, trim($arr[0]), 0);
            $this->Ln();
            $itt = 0;
            while ((isset($arr[1]) || isset($ary[1])) && $itt < 8) {
                $itt++;
                $arr = $this->spacing(trim($arr[1]), $wid - 10);
                if (isset($ary[1])) {
                    $this->SetFont('Times', 'B', $this->baseFont());
                    $ary = $this->spacing(trim($ary[1]), $col+5);
                    $this->Cell($col+10, $hit, $ary[0], 0);
                } else {
                    $this->Cell($col+10, $hit, '', 0);
                }
                $this->SetFont('Times', '', $this->baseFont(1));
                $this->Cell($wid, $hit, trim($arr[0]), 0);
                $this->Ln();
            }
            $at++;
        }
    }
    //Putting Things in Order
    function ptio($data) {
        $at = 1;
        $wid = 168;
        $hit = $this->baseHeight();
        shuffle($data);
        foreach ($data as $row) {

            $this->SetFont('Times', 'B', $this->baseFont());
            $ary = $this->spacing($row[0], 35);
            $this->Cell(15, $hit, trim($ary[0]), 0);
            $this->SetFont('Times', '', $this->baseFont(1));
            $arr = $this->spacing($this->begaend($row[1]), $wid);
            $this->Cell(65, $hit, trim($arr[0]), 0);
            $this->Ln();
            $itt = 0;
            while ((isset($arr[1]) || isset($ary[1])) && $itt < 8) {
                $itt++;
                $arr = $this->spacing(trim($arr[1]), $wid - 5);
                $this->Cell(20, $hit, '', 0);

                $this->SetFont('Times', '', $this->baseFont(1));
                $this->Cell($wid, $hit, trim($arr[0]), 0);
                $this->Ln();
            }
            $at++;
        }
    }
    //Matching Cause and Effect
    function mcae($data) {
        $at = 1;
        $wid = 100;
        $hit = $this->baseHeight();
        $count = count($data);
        foreach ($data as $row) {

            $this->SetFont('Times', 'B', $this->baseFont());
            $ary = $this->spacing(ucfirst($row[0]), 90);
            $this->Cell(90, $hit, trim($ary[0]), 0);
            //echo print_r($ary);
            $this->SetFont('Times', '', $this->baseFont());
            $arr = $this->spacing("$at. " . $this->begaend($row[1]), $wid);
            $this->Cell(65, $hit, trim($arr[0]), 0);
            $this->Ln();
            $itt = 0;
            while ((isset($arr[1]) || isset($ary[1])) && $itt < 8) {
                $itt++;
                $arr = $this->spacing(trim($arr[1]), $wid - 10);
                if (isset($ary[1])) {
                    $this->SetFont('Times', 'B', $this->baseFont());
                    $ary = $this->spacing(trim($ary[1]), 90);
                    $this->Cell(95, $hit, $ary[0], 0);
                } else {
                    $this->Cell(95, $hit, '', 0);
                }
                $this->SetFont('Times', '', $this->baseFont(1));
                $this->Cell($wid, $hit, trim($arr[0]), 0);
                $this->Ln();
            }
            $x = $this->GetX();
            $y = $this->GetY();
            if($at < $count){
            $this->Line($x,$y,$x+190,$y);
            }
            $at++;
        }
    }

}
$id = (int)$_GET['c'];
if ($id == 0) {
    $id = 9;
}
if($id > 42){
	$id = 42;
}
if($id < 1){
	$id = 1;
} //Get title and stuff

$sql = "SELECT ID, tf, mu, ppe, ide, gl, ptio, cae, wblink, enabled, comment FROM chapters WHERE chid = '$id' LIMIT 1";
$res = sqlite_query($sdb,$sql);
$row = sqlite_fetch_array($res);
$id2 = $row[0];
$tf = $row[1];
$mu = $row[2];
$ppe = $row[3];
$ide = $row[4];
$gl = $row[5];
$ptio = $row[6];
$cae = $row[7];
$wblink = $row[8];
$enabled = $row[9];
$title = $row[10];
 //die($sql.' '.print_r($row));
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->SetAutoPageBreak(true, 4);
$pdf->SetTopMargin(2);
$pdf->SetLeftMargin(4);
$pdf->SetRightMargin(4);
$pdf->SetSubject("Chapter ".$id);
$pdf->SetTitle("Chapter ".$id);
$pdf->chapterID = $id;


// Page 1

	$pdf->AddPage();

$pdf->SetFont('Times', 'B', $pdf->baseFont(11));
$pdf->Cell(0, 10, 'Chapter ' . $id, 0, 1, 'C');
$pdf->SetFont('Times', 'B', $pdf->baseFont(7));
$arr = $pdf->spacing($title, 220);
$pdf->Cell(0, 10, $arr[0], 0, 1, 'C');
while (isset($arr[1])) {
    $arr = $pdf->spacing(trim($arr[1]), 220);
    $pdf->Cell(170, 14, trim($arr[0]), 0, 1, 'C');
}
$pdf->Ln(2);

$pdf->Cell(0, 10, "Glossary", 0, 1, 'C');
//Glossary
$sql = "SELECT left , right FROM glossary WHERE chid = '" . $id . "' LIMIT 1";

$result = sqlite_query($sdb,$sql);
while ($row = sqlite_fetch_array($result)) {
    $left = $row[0];
    $right = $row[1];
}
$as = removecrap($left);
$cp = removecrap($right);
$as = explode("|", $as);
$cs = explode("|", $cp);
$howmany = count($as);
$data = array();
for ($x = 0; $x < $howmany; $x++) {
    $data[] = array($as[$x], $cs[$x]);
}
$pdf->Gloss($data);
//Gloss done, time for T & F
if($pdf->GetY() > $pdf->newPageAt()){
	$pdf->AddPage();
}
$pdf->SetFont('Times', 'B', 14);
$pdf->Cell(0, 10, "True & False", 0, 1, 'C');
$pdf->Ln(2);

$sql = "SELECT left,right FROM taf WHERE chid = '" . $id . "' LIMIT 1";
$result = sqlite_query($sdb,$sql);
while ($row = sqlite_fetch_array($result)) {
    $left = $row[0];
    $right = $row[1];
}
$as = removecrap($left);
$cp = removecrap($right);
$as = split("\|", $as);
$cs = split('\|', $cp);
$howmany = count($as);
$data = array();
for ($x = 0; $x < $howmany; $x++) {
    $data[] = array($as[$x], $cs[$x]);
}
$pdf->trufal($data);
//Multiple Choice Time!
if($pdf->GetY() > $pdf->newPageAt()){
	$pdf->AddPage();
}
$pdf->SetFont('Times', 'B', 14);
$pdf->Cell(0, 10, "Multiple Choice", 0, 1, 'C');
$pdf->Ln(2);
$sql = "SELECT answers,questions,choices FROM multc WHERE chid = '" . $id . "' LIMIT 1";
$result = sqlite_query($sdb,$sql);
while ($row = sqlite_fetch_array($result)) {
    $answers = $row[0];
    $questions = $row[1];
    $choices = $row[2];
}
$as = removecrap($questions);
$cp = removecrap($answers);
$mv = removecrap($choices);
$as = split("\|", $as);
$cs = split("\|", $cp);
$mv = split("\|", $mv);
$mu = array();
$len = count($mv);
for ($v = 0; $v < $len; $v++) {
    $temp = split("\r\n", (string )$mv[$v]);
    $mu[] = $temp;
}
$data = array();
$howmany = count($as);
for ($x = 0; $x < $howmany; $x++) {
    $data[] = array($as[$x], $cs[$x], $mu[$x]);
}
$pdf->multicho($data);
//Identification
if($pdf->GetY() > $pdf->newPageAt()){
	$pdf->AddPage();
}
$pdf->SetFont('Times', 'B', 14);
$pdf->Cell(0, 10, "Identification", 0, 1, 'C');
$pdf->Ln(2);
$sql = "SELECT left , right FROM ident WHERE chid = '" . $id . "' LIMIT 1";
$result = sqlite_query($sdb,$sql);
while ($row = sqlite_fetch_array($result)) {
    $left = $row[0];
    $right = $row[1];
}
$as = removecrap($left);
$cp = removecrap($right);
$as = split("\|", $as);
$cs = split('\|', $cp);
$howmany = count($as);
$data = array();
for ($x = 0; $x < $howmany; $x++) {
    $data[] = array($as[$x], $cs[$x]);
}
$pdf->Ident($data);
//MATCHING PEOPLE, PLACES, & EVENTS
if($pdf->GetY() > $pdf->newPageAt()){
	$pdf->AddPage();
}
$pdf->SetFont('Times', 'B', 14);
$pdf->Cell(0, 10, "Matching People, Places, & Events", 0, 1, 'C');
$pdf->Ln(2);
$sql = "SELECT left,right FROM mppe WHERE chid = '" . $id . "' LIMIT 1";
$result = sqlite_query($sdb,$sql);
while ($row = sqlite_fetch_array($result)) {
    $left = $row[0];
    $right = $row[1];
}
$as = removecrap($left);
$cp = removecrap($right);
$as = split("\|", $as);
$cs = split('\|', $cp);
$howmany = count($as);
$data = array();
for ($x = 0; $x < $howmany; $x++) {
    $data[] = array($as[$x], $cs[$x]);
}
$pdf->mppe($data);
//Putting Things in Order
if($pdf->GetY() > $pdf->newPageAt()){
	$pdf->AddPage();
}
$pdf->SetFont('Times', 'B', 14);
$pdf->Cell(0, 10, "Putting Things in Order", 0, 1, 'C');
$pdf->Ln(2);
$sql = "SELECT right FROM ptio WHERE chid = '" . $id . "' LIMIT 1";
$result = sqlite_query($sdb,$sql);
while ($row = sqlite_fetch_array($result)) {
    $right = $row[0];
}
$as = removecrap($right);
$cp = "";
$as = split("\|", $as);
$len = count($as);
$data = array();
for ($x = 0; $x < $len; $x++) {
    $data[] = array(($x + 1), $as[$x]);
}

$pdf->ptio($data);
//Matching Cause and Effect
//Since the PTIO doesn't take a full page, I do not have to add another page
$pdf->SetFont('Times', 'B', 14);
$pdf->Cell(0, 10, "Matching Cause & Effect", 0, 1, 'C');
$sql = "SELECT left,right FROM mcae WHERE chid = '" . $id . "' LIMIT 1";
$result = sqlite_query($sdb,$sql);
while ($row = sqlite_fetch_array($result)) {
    $left = $row[0];
    $right = $row[1];
}
$as = removecrap($left);
$cp = removecrap($right);
$as = split("\|", $as);
$cs = split('\|', $cp);
$howmany = count($as);
$data = array();
for ($x = 0; $x < $howmany; $x++) {
    $data[] = array($as[$x], $cs[$x]);
}
$pdf->mcae($data);
//And send the generated PDF to the browser
$pdf->Output();

?>