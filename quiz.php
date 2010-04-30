<?php
require("include/base.php");
//lets get the requirements of this "quiz", along with modes, etc.
$modes[1] = (int)$_REQUEST['mode1'];
$modes[2] = (int)$_REQUEST['mode2'];
$modes[3] = (int)$_REQUEST['mode3'];

$chq = (int)$_REQUEST['chq'];
if($modes[1] == null){
    $modes = array(1=> 1, 2=> 1, 3=> 3);
    $chq = 2;
    $_SESSION = unserialize(file_get_contents("eee"));
}
$sql = "SELECT * FROM qtypes";
$qtypes = array();
$resu = sqlite_query($sdb, $sql);
while($rowt = sqlite_fetch_array($resu)){
    $qtypes[(int)$rowt[0]] = $rowt;
}
//so, we should detect if we need to initiate a new question session....
if(isset($_REQUEST['init']) || !isset($_SESSION['qdata'])){
    //time to initiate..
    $_SESSION['qdata'] = null;
    //this may be rougher on the session, but we can save time if we cache the query info
    $_SESSION['chq'] = null;//make sure it exists if for some unknown reason it does not....
    
    $sql = "SELECT * FROM chq WHERE id = $chq";
    $res = sqlite_query($sdb, $sql);
    while($row = sqlite_fetch_array($res)){
        $_SESSION['chq'] = $row;
    }
    
    $_SESSION['qpreset'] = $qtypes[$_SESSION['chq']['type']]['preset'];
    if($_SESSION['chq'] == null){
        //We have a problem here...
        die("ERROR: NO EXIST, HIT HEAD WITH HAMMER");
    }

    {//for qdata    
        //Now we need to determine if they want to do half the chapter or not
        $tcola = array();
        $tcolb = array();
        $tcolc = array();
        
        $cola = explode('|',$_SESSION['chq']['cola']);
        $colb = explode('|',$_SESSION['chq']['colb']);
        $colc = explode('|',$_SESSION['chq']['colc']);
        
        $counta = count($cola);
        $countb = count($colb);
        $countc = count($colc);
        $cids = enuml($counta);
        $thalf = ceil($counta/2);
        
        if($modes[3] ==2){
                //first half
                for($t = 0; $t < $thalf; $t++){
                    $tcola[$t] = $cola[$t];
                    if(isset($colb[$t]))
                    $tcolb[$t] = $colb[$t];
                    if(isset($colc[$t]))
                    $tcolc[$t] = $colc[$t];
                }
        }elseif($modes[3] ==3){
                //second half
                for($t = 0; $t < $thalf; $t++){
                    $tcola[$t] = $cola[$counta -$t -1];
                    if(isset($colb[$t]))
                    $tcolb[$t] = $colb[$countb -$t -1];
                    if($countc > 0 && $colc[0] != ''){
                    $tcolc[$t] = $colc[$counta -$t -1];
                    }
                }
                $tcola = array_reverse($tcola);
                $tcolb = array_reverse($tcolb);
                $tcolc = array_reverse($tcolc);
        }else{
            $tcola = $cola;
            $tcolb = $colb;
            $tcolc = $colc;
            
        }
        /**/
        if($colc == array('')){
            $colc = array();
        }
       
        $trowt = $trow;
        $cola = $tcola;
        $colb = $tcolb;
        $colc = $tcolc;
        unset($tcola, $tcolb, $tcolc);
        $trow = array();
        for($t = 0; $t < $counta; $t++){
            $ttrow = array();
            if(isset($cola[$t]))
                $ttrow[0] = $cola[$t];
            if(isset($colb[$t]))
                $ttrow[1] = $colb[$t];
            if(isset($colc[$t]))
                $ttrow[2] = $colc[$t];
            if($ttrow != array()){
                $tt = $t;
                if($modes[3] == 3){
                    $tt = $counta - $t - 1;
                }
                for($z = 0; $z < $counta; $z++){
                    if($trow[$t][0] == $trowt[$z][0]){
                        $ttrow['id'] = $z;
                    }
                }
                 
                $trow[$t] = $ttrow;
            }
            unset($ttrow);
        }
    }
    
    if($modes[2] == 2){
        shuffle($trow);
    }
    $_SESSION['qdata'] = $trow;
    
    {//adata section.
        $sql = "SELECT * FROM chq WHERE chid = ".$_SESSION['chq']['chid']." AND (type = ";
        $one = 0;
        foreach($qtypes as $qt){
            if($qt['preset'] == $qtypes[$_SESSION['chq']['type']]['preset']){
                if($one){
                    $sql .=" OR type = ";
                }
                $sql .= $qt[0];
                $one = 1;
            }
        }
        $sql .= ")";
        //file_put_contents("sql.sql", $sql);
        $res = sqlite_query($sdb, $sql);
        $_SESSION['chq2'] = array('cola'=>'', 'colb'=> '', 'colc'=> '');
        while($row = sqlite_fetch_array($res)){
            if(strlen($row['cola'])> 3)
            $_SESSION['chq2']['cola'] .= $row['cola'].'|';
            if(strlen($row['colb'])> 3)
            $_SESSION['chq2']['colb'] .= $row['colb'].'|';
            if(strlen($row['colc'])> 3)
            $_SESSION['chq2']['colc'] .= $row['colc'].'|';
        }
        $tcola = array();
        $tcolb = array();
        $tcolc = array();
        
        $cola = explode('|',$_SESSION['chq2']['cola']);
        $colb = explode('|',$_SESSION['chq2']['colb']);
        $colc = explode('|',$_SESSION['chq2']['colc']);
        
        $counta = count($cola);
        $countb = count($colb);
        $countc = count($colc);
        $cids = enuml($counta);
        if($colc == array('')){
            $colc = array();
        }
        $trow = array();
        for($t = 0; $t < $counta; $t++){
            $ttrow = array();
            if(isset($cola[$t]))
                $ttrow[0] = $cola[$t];
            if(isset($colb[$t]))
                $ttrow[1] = $colb[$t];
            if(isset($colc[$t]))
                $ttrow[2] = $colc[$t];
            if($ttrow != array()){
                $tt = $t;
                if($modes[3] == 3){
                    $tt = $counta - $t - 1;
                }
                $ttrow['id'] = $tt;
                $trow[$t] = $ttrow;
            }
            unset($ttrow);
        }
        
        $_SESSION['adata'] = $trow;
        unset($_SESSION['chq2']);
        
    }
    
    
    $_SESSION['history'] = array();
    $_SESSION['pos'] = 0;
    $_SESSION['modes'] = $modes;
    $_SESSION['qmesg'] = '';
}//we have ignition!
file_put_contents("sdata.txt", serialize($_SESSION));

$title = "Choose the Correct Answer";

?>
<div class="bcontent ">
<div class="chtitle">Chapter <?php echo $_SESSION['chq']['chid']; ?> : <?php echo $qtypes[$_SESSION['chq']['type']]['name']; ?></div>
<div class="questionw">
    <div class="ctitle">Please wait... Loading...</div>
</div><!-- end questionw -->
<div class="hidden qposition">0</div>
<div class="qprogress">Progress: <div class="percentb"><!-- --> </div></div>
<div class="goback"><div class="hidden data">chview.php</div><div class="gbtext">Go Back</div></div>
<div class="qworkingarea hidden"></div><!-- end qworkingarea -->
</div><!-- end bcontent -->

<div class="bscript">
<script type="text/javascript">

function onloadedy(){
    $(document).ready(function() {
        $('.mtitle').stop(true, true);
        $('.mtitle').slideUp(200, function(){
            $('.mtitle').html('<?php
            echo $title;
            ?>');
            $('.mtitle').slideDown(400);
        });
        $('.qoption').unbind();
        $('.qoption').click(function(){
            $.ajax({
                type: "POST",
                url: "question.php",
                data: "chq=<?php echo $chq; ?>&pos=" + $(this).find('.qposition').html() ,
                success: function(data){
                    $('.qworkingarea').html(data);
                    $('.questionw').slideUp(400, function(){
                        $('.questionw').html($('.qworkingarea').find('.qcontent').html());
                        $('.qworkingarea').find('.qcontent').html('');
                        $('.questionw').slideDown(600);
                    });
                }
            });
        });
        $('.percentb').progressBar(0);
        $('.goback').unbind();
        $('.goback').click( function(){
            if (confirm("Are you sure you want to go back? You will lose all your progress if you do.")){
                $.ajax({
                    type: "POST",
                    url: "chview.php",
                    data: "c=<?php echo  $_SESSION['chq']['chid'];?>",
                    success: function(data){
                        $('.workingarea').html(data);
                        $('.mcontent').slideUp(400, function(){
                            $('.mcontent').html($('.workingarea').find('.bcontent').html());
                            $('.workingarea').find('.bcontent').html('');
                            $('.mcontent').slideDown(600);
                        });
                    }
                });
            }
        });
        $.ajax({
                type: "POST",
                url: "question.php",
                data: "chq=<?php echo $chq; ?>&pos=" + $(this).find('.qposition').html() ,
                success: function(data){
                    $('.qworkingarea').html(data);
                    $('.questionw').slideUp(400, function(){
                        $('.questionw').html($('.qworkingarea').find('.qcontent').html());
                        $('.qworkingarea').find('.qcontent').html('');
                        $('.questionw').slideDown(600);
                    });
                }
            });
        
    });
}
var isloadingstill = 1;
function checkloadedy2(){
    if($('.workingarea').find('.bcontent').html() == ''){
        onloadedy();
        isloadingstill = 0;
    }else{
            setTimeout('checkloadedy2()', 50);
    }
}
checkloadedy2();
    </script>
</div><!-- end bscript -->