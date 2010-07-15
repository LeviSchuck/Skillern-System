<?php
session_start();
include("include/connect.php");
$cscript = "";
$secret = "Mr Skillern eats little kids' candy";
class base64salted
{
    public $salt;
    public $keyish;
    function __construct ($string) {
        $this->salt = $string;
        $this->keyish = sha1($this->salt);
    }
    private function safeindex ($array, $index) {
        $index++;
        return ($index % $array == 0 ? $array : $index % $array) - 1;
    }
    public function encode ($array) {
        $key = $this->keyish;
        $output='';
        for ($i=0; $i<strlen($key); $i++) {
            $rotArray[] = $this->safeindex(3,ord($key[$i]));
        }
        $arrayCount = count($rotArray); 
        if (is_array($array)) {
            preg_match_all("#.#",base64_encode(serialize($array)),$outputarr);
        } else {
            preg_match_all("#.#",base64_encode($array),$outputarr);
        }
        foreach ($outputarr[0] as $k => $v) {
            $output .= ( (($k%2) != 1)?(chr(ord($v)+$rotArray[$this->safeindex($arrayCount,$k)])):(chr(ord($v)+$rotArray[$this->safeindex($arrayCount,$k)+1])) );
        }
        return urlencode(strrev($output));
    }
    public function decode ($array) {
        $key = $this->keyish;
        $output='';
        for ($i=0; $i<strlen($key); $i++) {
            $rotArray[] = $this->safeindex(3,ord($key[$i]));
        }
        $arrayCount = count($rotArray);
        $array = strrev(urldecode($array));
        preg_match_all("#.#",$array,$outputarr);
        foreach ($outputarr[0] as $k => $v) {
            $output  .= ( (($k%2) != 1)?(chr(ord($v)-$rotArray[$this->safeindex($arrayCount,$k)])):(chr(ord($v)-$rotArray[$this->safeindex($arrayCount,$k)+1])) );
        }
        if(unserialize(base64_decode($output))) {
            return unserialize(base64_decode($output));
        } else {
            return base64_decode($output);
        }
    }
}
$b64c = new base64salted($secret);
function updatetime() {
        global $_SESSION, $sdb;
        $id = $_SESSION['session'];
        //echo $id;
        $time = time() - (int)$_SESSION['time'];
        $sql = "SELECT timeonline FROM skllern_users WHERE sessionid = '" .
            sqlite_escape_string($id) . "' LIMIT 1";
        $result = sqlite_query($sdb,$sql);
        while ($row = sqlite_fetch_array($result)) {
            $time2 = (int)$row[0];
        }
        $time3 = $time + $time2;
        $sql = "UPDATE skllern_users SET timeonline = '" . $time3 .
            "', lasttime = '" . time() . "' WHERE sessionid = '$id'";
        $result = sqlite_query($sdb,$sql);
        $_SESSION['time'] = time();
    }
function removecrap($text) {
    $text = (string )$text;
    $text = str_replace('’', '\'',$text);
    $text = htmlspecialchars(stripslashes( $text));
    $text = str_replace("&amp;quot;", "&quot;", $text);
    $text = str_replace("&amp;amp;", "&amp;", $text);
    $text = str_replace("", "'", $text);
    $text = str_replace(array('','','?'), "\"", $text);
    return $text;
}
if(isset($_SESSION['skillern'])){
    
    updatetime();
}
function jaccard($base, $compare){
    $m11 = 0;//match
    $m00 = 0;//not match
    $base = explode(" ", $base);//make array of individual words
    $compare = array_unique(explode(" ", $compare));//get rid of duplicate words
    foreach($base as $bword){
            foreach($compare as $cword){
                $lev = levenshtein($cword, $bword,12,32,27);//test for score
                if($lev < 100){//meaning word is close to current, like queen and queens.
                    $m11+=(100-$lev)/100;//add a weighted score.
                }else{
                    $m00++;
                }
            }
    }
    return ($m11+(2*$m00/5))/(($m00 + 1.2*$m11));//my attempt to weight more not right 
}
function getlikeres($current, $data, $data2){
    $others = array();
    $count = count($data2);
    $match = array();
    //get matches between arrays, as one is local and the other(2) is global data, but we will be referring to the global
    foreach($data as $key=> $dat){
        foreach($data2 as $key2 => $dat2){
            if($dat[1] == $dat2[1]){
                $match[$key] = $key2;
                //print_r($dat1);
            }
        }
    }
    //print_r($data2);
    //print_r($match);
    for($x = 0; $x < $count; $x++){
        $others[$x] = trim(strtolower(preg_replace('/(&\w+;|\W)+/i', ' ',$data2[$x][1].' '.$data2[$x][0])));
        //gets rid of html things like &amp; along with other characters that are not alphanumeric
        //remove short words.
        $temp = explode(" ",$others[$x]);
        $county = count($temp);
        for($q = 0; $q < $county; $q++){
            if(strlen($temp[$q]) < 4){
                unset($temp[$q]);
            }
        }
        $others[$x] = implode(" ", $temp);
        unset($temp);
        //end remove short words
    }

    $colt = $others[$match[$current]];//the current statement from the global.
    //echo $colt." <--";
    $scores = array();
    for($x = 0; $x < $count; $x++){
        if($x != $match[$current]){//don't wan't to check ourselves.
            $scores[$x] = jaccard($colt, $others[$x]);

            //echo "<br>".$others[$x].$scores[$x];
        }
    }
    arsort($scores);//sort so it goes like 0.8, 0.7. 0.3 
    //print_r($scores);
    //echo "<br>";
    $start = 0;
    $posibles = array($match[$current]);//the correct answer
    //add incorrect but close answers.
    foreach($scores as $key => $score){
        //if($score < 0.8){
        if($start < 4){//we do not want any more than 3 other results
            $start++;
            $posibles[] = $key;
            //print_r($data2[$key]);
            //print_r($score);
        }else{
            break;
        }
        //}
    }
    shuffle($posibles);
    return $posibles;
}
function fetchatPOS($needle, $haystack){
    $c = count($haystack);
    for($x = 0; $x < $c; $x++){
        if ($haystack[$x]['id'] == $needle){
            return $x;
        break;
        }
    }
}
function fetchatID($needle, $haystack){
    return $haystack[$needle]['id'];
}
function enuml($num){
    $a = array();
    for($x = 0; $x < $num; $x++){
        $a[$x] = $x;
    }
    return $a;
}
function fixTheText($input){
    $input = stripslashes($input);
    $table        = array_flip(get_html_translation_table(HTML_ENTITIES));
    $lastinput = $input;
    $input = strtr($input, $table);//inverse the htmlentities function so that we are at ground zero again.
    while($lastinput != $input){
        $lastinput = $input;
        $input = strtr($input, $table);
    }
    $input = htmlentities($input);
    /*$input = trim(preg_replace('/\w/i', '', $input));
    $count = strlen($input);
    $t = '';
    for($x = 0; $x < $count; $x++){
        $t.= ' '.ord(substr($input,$x, 1));
    }
    return $t;*/
    $input = str_replace(chr(146),'',$input);
    return $input;
}



//we need a variant of the get-like res for True and False

function getlikeresTF($current, $data){
    $others = array();
    $count = count($data);
    $tf = (int)$data[$current][0];

$colt = $data[$current][1];//the current statement from the global.
    for($x = 0; $x < $count; $x++){
        //see if the types are the opposite of the current
        $valid = 0;
        if($data[$x][0] == 0 && $tf == 1){//see if opposite! 
            $valid = 1;
        }else if($data[$x][0] == 1 && $tf == 0){
            $valid = 1;//this could be reduced from 5 lines to 3, but 
        }
        if($valid){
            $others[$x] = trim(strtolower(preg_replace('/(&\w+;|\W)+/i', ' ',$data[$x][1])));
            //gets rid of html things like &amp; along with other characters that are not alphanumeric
            //remove short words.
            $temp = explode(" ",$others[$x]);
            $county = count($temp);
            for($q = 0; $q < $county; $q++){
                if(strlen($temp[$q]) < 4){//get rid of the practically non-existant ones.
                    unset($temp[$q]);
                }
            }
            $others[$x] = implode(" ", $temp);
            unset($temp);
            //end remove short words
        }
    }
    unset($valid);

    
    $scores = array();
    for($x = 0; $x < $count; $x++){
        if(isset($others[$x])){
            
            $scores[$x] = jaccard($colt, $others[$x]);
        }
    }
    arsort($scores);//sort so it goes like 0.8, 0.7. 0.3 
    $start = 0;
    $posibles = array($current);//the correct answer
    //add incorrect but close answers.
    foreach($scores as $key => $score){
        if($start < 3){//we do not want any more than 3 other results
            $start++;
            $posibles[] = $key;
        }else{
            break;
        }
    }
    shuffle($posibles);
    return $posibles;
}
function sum($array = array()){
    $total = 0;
    foreach($array as $ar){
        $total += (real)$ar;
    }
    return $total;
}
function needrights($rightsLevel = 1){
    $r = $rightsLevel;
    if(!rightsSatis($r)){
        echo file_get_contents("notallowed.php");
        die();
    }
}
function rightsSatis($rightsLevel = 1){
    if(isset($_SESSION['rights'])){
        if($_SESSION['rights'] < $rightsLevel){
            return false;
        }else{
            return true;
        }
    }else{
        return false;
    }
}

function defaultcols($qtype){
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
    return $cols;
}

?>