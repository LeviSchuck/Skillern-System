<?php
require("include/base.php");
needrights(7);//need to be at minimum a TA
//ok, by now we should be authorized
//Lets gather our information.

if(isset($_REQUEST['init'])){
    
    //first of all, lets set a forward standard, USE a subsection of session,
    //so we do not mix with our quiz system!
    $_SESSION['editor'] = array();
    $chq = (int)$_REQUEST['chq'];
    
    $sql = "SELECT * FROM qtypes";
    $qtypes = array();
    $resu = sqlite_query($sdb, $sql);
    while($rowt = sqlite_fetch_array($resu)){
        $qtypes[(int)$rowt[0]] = $rowt;
    }
    $_SESSION['editor']['qdata'] = null;
    $_SESSION['editor']['chq'] = null;
    $sql = "SELECT * FROM chq WHERE id = $chq";
    $res = sqlite_query($sdb, $sql);
    while($row = sqlite_fetch_array($res)){
        $_SESSION['editor']['chq'] = $row;
    }
    $_SESSION['editor']['chapter'] = $_SESSION['editor']['chq']['chid'];
    {//set up our cola-cs
        $tcola = array();
        $tcolb = array();
        $tcolc = array();
        
        $cola = explode('|',$_SESSION['editor']['chq']['cola']);
        $colb = explode('|',$_SESSION['editor']['chq']['colb']);
        $colc = explode('|',$_SESSION['editor']['chq']['colc']);
        
        $counta = count($cola);
        $countb = count($colb);
        $countc = count($colc);
    }
    {//prepare trow for qdata
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
    $_SESSION['editor']['qdata'] = $trow;
}
//okay, now for the UI(I guess)
?><div class="bcontent">
    <div class="chtitle">
        <?php
        
        $sql = "SELECT comment FROM chapters WHERE chid = ".$_SESSION['editor']['chapter']." LIMIT 1";
        
        $resu = sqlite_query($sdb, $sql);
        $chtitle = sqlite_fetch_array($resu);
        echo ucfirst(strtolower($chtitle[0]));
        unset($resu,$sql, $chtitle);
       
        
        ?>
        
    </div><!--end of chtitle-->
    <div class="goback">
        <div class="gbtext">Go Back</div>
    </div>
</div>
<div class="bscript">
<script type="text/javascript">

function onloadedy(){
    $(document).ready(function() {
        //do what ever in here
        $('.goback').unbind();
        $('.goback').click( function(){
            $('.goback').unbind();
            $.ajax({
                type: "POST",
                url: "chview.php",
                data: "c=<?php
                echo $_SESSION['editor']['chapter'];
                ?>",
                success: function(data){
                    $('.workingarea').html(data);
                    $('.mcontent').slideUp(400, function(){
                        $('.mcontent').html($('.workingarea').find('.bcontent').html());
                        $('.workingarea').find('.bcontent').html('');
                        $('.mcontent').slideDown(600);
                    });
                }
            });
        });
    });
}
var isloadingstill = 1;
function checkloadedye(){
    if($('.workingarea').find('.bcontent').html() == ''){
        onloadedy();
        isloadingstill = 0;
    }else{
            setTimeout('checkloadedye()', 50);
    }
}
checkloadedye();
    </script>
</div><!-- end bscript -->