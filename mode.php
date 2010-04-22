<?php
require("include/base.php");
$title = "Choose the Mode";
//Determine QType
$qtype = (int)trim($_REQUEST['qt']);
$chq = (int)trim($_REQUEST['chq']);
$c = (int)trim($_REQUEST['c']);
$sql = "SELECT * FROM qtypes";
$qtypes = array();
$resu = sqlite_query($sdb, $sql);
while($rowt = sqlite_fetch_array($resu)){
    $qtypes[(int)$rowt[0]] = $rowt;
}
//determine options
/*
mode -1:
 only one mode for this type so move onto the next mode type or page
 ______________________________
 First kinds of modes
 mode 1:
 question is left, options right
 mode 2:
 question is right, options left
 mode 3:
 question is right, type in left.
 ______________________________
 secondary modes
 mode 1:
 go in order of position
 mode 2:
 shuffle
 ______________________________
 Third modes
 mode 1:
 Do Whole chapter
 mode 2:
 Do only half the chapter
*/
//establish modes array
$modes1 = array(-1);
$modes2 = array(-1);
$modes3 = array(-1);
switch($qtypes[$qtype]['preset']){
    case 1://standard left and right
        $modes1 = array(1,2,3);
        $modes2= array(1,2);
        $modes3 = array(1,2,3);
        break;
    case 2://put things in order
        $modes1= array(-1);
        $modes2 = array(-1);
        $modes3= array(1,2,3);
        break;
    case 3: //multiple choice
        $modes1= array(-1);
        $modes2 = array(1,2);
        $modes3= array(1,2,3);
        break;
    case 6://True False
        $modes1 = array(1,2);//going to be interesting to code for #2
        $modes2 = array(1,2);
        $modes3= array(1,2,3);
        break;
}
//so at this point we have the available modes, along with the qtype and chq location
//get the title for the qtype and then provide the menus.
//So lets make the text easily manageble.
$mode1text = array();
$mode2text = array();
$mode3text = array();

$mode1text[1] ='Description selection';
$mode1text[2] ='Identification selection';
$mode1text[3] ='Type in Identification';

$mode2text[1] ='In Order';
$mode2text[2] ='Shuffle';

$mode3text[1] ='Whole Chapter';
$mode3text[2]='First Half Chapter';
$mode3text[3] ='Second Half Chapter';
?>
<div class="bcontent">
    <?php
    $sql = "SELECT * FROM qtypes WHERE id = ".$qtype;
    $resu = sqlite_query($sdb, $sql);  
    echo '<div class="csecs noselect chtitle">';
    while($rowt = sqlite_fetch_array($resu)){
        echo $rowt['name'];
    }
echo '</div>';//end qtype name
//now to do the options.
//We have the text set up in the modeXtext[][] but we need to determine if they are available.
//Go in order of modes... first mode 1
$step = 0;
if($modes1[0] != -1){
    $step++;
    echo '<div class="panelp modew step'.$step.'"><div class="modet">';
    echo 'What you will do';
    echo '</div>';//the title
    $checked = 'checked';
    foreach($modes1 as $mode){
        echo '<div class="moderow">';
        echo '<div class="radiow"><input class="rad1" type="radio" name="mode1" value="'.$mode.'" '.$checked.' /></div>';
        $checked = '';//so we only have 1 item truely selected
        echo '<div class="modetext">'.$mode1text[$mode].'</div>';
        echo '</div>';//end current mode
    }
    echo '</div>';//the whole mode wrapper
}else{
    echo '<input type="hidden" name="mode1"  value="-1"/>';//so we still have a value to send to the server.
}
//___________________________
if($modes2[0] != -1){
    $step++;
    echo '<div class="panelp modew step'.$step.'"><div class="modet">';
    echo 'How you do it';
    echo '</div>';//the title
    $checked = 'checked';
    foreach($modes2 as $mode){
        echo '<div class="moderow">';
        echo '<div class="radiow"><input class="rad2" type="radio" name="mode2" value="'.$mode.'" '.$checked.' /></div>';
        $checked = '';//so we only have 1 item truely selected
        echo '<div class="modetext">'.$mode2text[$mode].'</div>';
        echo '</div>';//end current mode
    }
    echo '</div>';//the whole mode wrapper
}else{
    echo '<input type="hidden" name="mode2"  value="-1"/>';//so we still have a value to send to the server.
}
//___________________________
if($modes3[0] != -1){
    $step++;
    echo '<div class="panelp modew step'.$step.'"><div class="modet">';
    echo 'What you take';
    echo '</div>';//the title
    $checked = 'checked';
    foreach($modes3 as $mode){
        echo '<div class="moderow">';
        echo '<div class="radiow"><input class="rad3" type="radio" name="mode3" value="'.$mode.'" '.$checked.' /></div>';
        $checked = '';//so we only have 1 item truely selected
        echo '<div class="modetext">'.$mode3text[$mode].'</div>';
        echo '</div>';//end current mode
    }
    echo '</div>';//the whole mode wrapper
}else{
    echo '<input type="hidden" name="mode3"  value="-1"/>';//so we still have a value to send to the server.
}
file_put_contents('eee',serialize($_SESSION));

    ?>
    <div class="gotonext">Go!</div>
    
    
    <div class="goback"><div class="hidden data">apanel.php</div><div class="gbtext">Go Back</div></div>
    </div>
<div class="bscript">
    <script type="text/javascript">
    function onloadedy() {
    $(document).ready(function() {
        $('.mtitle').stop(true, true);
        $('.mtitle').slideUp(300, function(){
                $('.mtitle').html('<?php
                echo $title;
                ?>');
                $('.mtitle').slideDown(300);
        });
        $('.goback').unbind();
        $('.goback').click( function(){
            $.ajax({
                type: "POST",
                url: "chview.php",
                data: "c=<?php echo  $c;?>",
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
        var mode1 = 1;
        var mode2 = 1;
        var mode3 = 1;
        $('.rad1').unbind();
        $('.rad2').unbind();
        $('.rad3').unbind();
        
        $('.rad1').click( function(){
            mode1 = $(this).val();
            });
        $('.rad2').click( function(){
            mode2 = $(this).val();
            });
        $('.rad3').click( function(){
            mode3 = $(this).val();
            });
       
        
        $('.gotonext').unbind();
        $('.gotonext').click( function(){
            $.ajax({
                type: "POST",
                url: "quiz.php",
                data: "chq=<?php echo $chq; ?>&init=1&mode1=" + mode1 + "&mode2=" + mode2 + "&mode3=" + mode3,
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
        
    });//end document ready
    
}

     function checkloadedy(){
        if($('.workingarea').find('.bcontent').html() == ''){
            onloadedy();
        }else{
            setTimeout('checkloadedy()', 50);
        }
     }
     checkloadedy();
    </script>
    </div>