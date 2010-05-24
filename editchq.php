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
        $_SESSION['editor']['chq'] = array_map('removecrap',$row);
    }
    $_SESSION['editor']['chapter'] = $_SESSION['editor']['chq']['chid'];
    {//set up our cola-cs
        $tcola = array();
        $tcolb = array();
        $tcolc = array();
        
        $cola = explode('|',$_SESSION['editor']['chq']['cola']);
        $colb = explode('|',$_SESSION['editor']['chq']['colb']);
        $colc = explode('|',$_SESSION['editor']['chq']['colc']);
        $_SESSION['editor']['qtype'] = $qtypes[(int)$_SESSION['editor']['chq']['type']];
        $counta = count($cola);
        $countb = count($colb);
        $countc = count($colc);
    }
    {//prepare trow for qdata
        $trow = array();
        for($t = 0; $t < $counta; $t++){
            $ttrow = array();
            if(isset($cola[$t]))
                $ttrow[0] = ucfirst($cola[$t]);
            if(isset($colb[$t]))
                $ttrow[1] = ucfirst($colb[$t]);
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
    <div class="questionslistw">
        <ul class="dragables">
            <?php
            $count = 0;
            foreach($_SESSION['editor']['qdata'] as $question){
            ?>
            <li class="dragable<?php
            switch((int)$_SESSION['editor']['qtype']['preset']){
                        case 1:
                        case 5:
                        case 6:
                            {
                                echo 2;
                            }
                            break;
            }
            
            ?>">
                <div class="hidden data">
                    <div class="location">
                        <?php
                        echo $count;
                        ?>
                    </div>
                </div>
                <div class="movehook paddown16"><!--grab it here--></div>
                <div class="rtext2">
                    
                    <?php
                    switch((int)$_SESSION['editor']['qtype']['preset']){
                        case 1:
                            {//Word->Description
                    ?>
                    <fieldset class="editorfieldset">
                    <legend class="cola ident">
                        <a href="fancychq.php?&chq=<?php echo $question['id'];?>&sub=<?php echo $count; ?>&col=0" class="fancy">
                        <?php
                        echo $question[0];
                        ?>
                        </a>
                        </legend>
                    <div class="colb descr">
                        <a href="fancychq.php?&chq=<?php echo $question['id'];?>&sub=<?php echo $count; ?>&col=1" class="fancy">
                        <?php
                        echo $question[1];
                        ?>
                        </a>
                        </div>
                    </fieldset>
                    <?php
                            }
                            break;
                        case 2:
                            {//put in order
                                ?>
                    <div class="cola descr">
                        <?php
                        echo $question[0];
                        ?>
                        </div>
                                <?php
                            }
                            break;
                        case 3:
                            {//multiple choice
                                ?>
                    <div class="colb identm">
                        <?php
                        echo $question[1];
                        ?>
                        </div>
                    <div class="colc descr">
                        <?php
                        //stuff here
                        $questions = explode("\n",$question[2]);
                        //do a loop
                        $qtpos = 0;
                        foreach($questions as $que){
                            echo '<div class="multc ';
                            if($qtpos == (int)$question[0]){
                                echo 'multr';
                            }
                            echo '">';
                            echo '<div class="editmultletter">'.chr(ord("A")+$qtpos).'</div>';
                            //just a notice here that I should in the future implement something so it knows to do stuff like "AA, AB, etc."
                            echo fixTheText($que);
                            echo '</div>';//end of the 
                            $qtpos += 1;
                        }
                        ?>
                        </div>
                                <?php
                            }
                            break;
                        case 4:
                            {//sub-versions of put in order
                                
                            }
                            break;
                        case 5:
                            {//type in word only according to definition
                                
                            }
                            break;
                        case 6:
                            {//this is True and False
                                
                            }
                            break;
                        
                    }
                    
                    ?>
                    <!-- Col A -->
                    
                    <!-- Col B -->
                    
                    <!-- Col C -->
                </div>
            </li>
            <?php
                $count++;
            }
            ?>
            
        </ul>
    </div><!-- End questions list wrapper -->
    
    
    
    <div class="goback">
        <div class="gbtext">Go Back</div>
    </div>
    <div class="savebtn">
        <div class="svtext">Save</div>
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
        //set up the dragging
        $('.dragables').sortable({ revert: true,
                                 helper: 'clone',
                                 handle : '.movehook',
                                 containment: '.mcontent'
                                 });
        $('.dragables').disableSelection();
        if(!$('.fancy').hasClass('fancybox')){
        $('.fancy').fancybox({
				'titleShow'	: false,
				'transitionIn'	: 'elastic',
				'transitionOut'	: 'elastic'
			});
        }
        
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