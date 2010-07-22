<?php
require("include/base.php");
needrights(1);
$anyotherJS = '';
$waschecked = false;
//$_SESSION['qdata']; //the predetermied data to test with
//$_SESSION['history'];
//$_SESSION['pos']; //the position within the dataset
//$_SESSION['modes'];//the modes, we will mainly use mode1 in this page.
//$_SESSION['qpreset']; //the preset we will base our problems on.
if(isset($_REQUEST['selected']) || isset($_REQUEST['order'])){
    $waschecked = true;
    $b64t = new base64salted($secret.$_SESSION['session'].$_SESSION['pos'].$_SESSION['chq']['chid']);
    
    switch($_SESSION['qpreset']){
        case 1:
        case 3:
        case 5:
        case 6:
            {
                if($_SESSION['qpreset'] == 1 && $_SESSION['modes'][1] == 3){
                    //special case
                    //the user types in their response here, time to check it
                }else{
                    //do the other cases
                    {//do the decoded number test
                        $decode = $b64t->decode(trim($_REQUEST['selected']));
                        if((int)$decode == 1000){
                            //ok, we have the right answer!
                            $_SESSION['pos']++;
                        }else{
                            //ok wrong answer, mark it wrong for them.
                            $_SESSION['history'][$_SESSION['pos']]['wrong'][] = ceil(sqrt((int)$decode-1000))-1;
                        }
                    }
                }
            }
            
            break;
        case 2:
        case 4:
            {//types that are like put-it-in-order
                $sentorder = array();
                $count = count($_REQUEST['order']);
                //time to decode our array
                foreach($_REQUEST['order'] as $item){
                    $sentorder[] = (int)trim($b64t->decode(trim($item)));
                }
                //we can't go and just test for if the result(decoded) is 1000, but rather take in an array and determine if it is in order.
                $_SESSION['history'][$_SESSION['pos']]['order'] = $sentorder;
                $temp = $sentorder;
                sort($temp);
                $correct = false;
                print_r($sentorder);
                if($temp == $sentorder && count($sentorder) > 1){//sortof an area where an exploit can happen; though I'm not worried about it considering we have it encrypted
                    //we have success!
                    $correct = true;
                }else{
                    //we are obviously not correct, so we need to say "YEW ARE TEH WRONGNESS"
                    $_SESSION['history'][$_SESSION['pos']]['wrong'][] = -1;
                    //calculate lev
                    $ta = implode('',$sentorder);
                    $tb = implode('',$temp);
                    $lev = levenshtein($ta,$tb);
                    
                    //the ultimate evar equation
                    $e = 2.71828182845904523536;
                    $x = count($_SESSION['history'][$_SESSION['pos']]['wrong'])-1;
                    $ultimate = 1.0196/(1+.0196*pow($e,(0.66562*$x)));
                    $ultimate = sqrt(sqrt(sqrt($ultimate)));
                    //now we need to find out the order so we can 
                    $phase2 = $ultimate/($x+1);
                    $phase3 = ($count-$lev)/$count;
                    $phase4 = $phase2*$phase3;
                    $_SESSION['history']['s'][] = $phase4;
                    $phase5 = sum($_SESSION['history']['s']);
                    $_SESSION['history']['p'][] = $phase4;
                    $pcount = count($_SESSION['history']['p']);
                    $phase6 = sum(array($_SESSION['history']['p'][$pcount-2],$_SESSION['history']['p'][$pcount-1]));
                    $_SESSION['history']['phase6'] = $phase6;
                    $anyotherJS .= "\n\t\t$('.correctness').progressBar(".(int)($phase3*100).");\n";
                }
                if($_SESSION['qpreset'] == 2){
                    //only 1 loop
                    if($correct){
                        $_SESSION['pos']=1024;
                    }
                    
                }else if($_SESSION['qpreset'] == 4){
                    //1 or more.
                    if($correct){
                        $_SESSION['pos']++;
                    }
                }
                
            }
            break;
    }
    //note: after this point in time, decode is no longer used.
    
    $iscorrect = false;
    if(($_SESSION['pos'] > count($_SESSION['qdata'])-1)){//we are past the last question
        
        if($_SESSION['qpreset'] == 2 ) {
        //ok, see if they are correct. If they are, they should have the $_SESSION['pos'] == 1024
            if($_SESSION['pos'] == 1024){
                $iscorrect = true;
                //ok, we are correctumundo here
                //time to move back to setting our records.
                //I decided not to average it as it is already too complex.
                $sql = "SELECT * FROM sectionrecords WHERE chid =".$_SESSION['chq']['chid']." AND section = " . $_SESSION['chq']['type'] . " AND userid = " . $_SESSION['id'];
               $result = sqlite_query($sdb,$sql);
               $exists = 0;            
                while ($row = sqlite_fetch_array($result)) {
                    $id = $row[0];
                    $records = unserialize($row['record']);
                    $exists = 1;
                }
                $rc =count($_SESSION['qdata']);
                
                    if(isset($_SESSION['history'][0]['wrong'])){
                        $kwrong = count($_SESSION['history'][$k]['wrong']);
                    }else{
                        $kwrong = 0;
                    }
                    //$phase6;
                    if($exists){
                        $nrecords = $records;
                    }else{
                        $nrecords = array();
                    }
                    
                    
                    
                    for($b = $_SESSION['history']['spos']; $b < $rc+$_SESSION['history']['spos']; $b++){
                        $nrecords[$b] = array(floor(100*(1-$_SESSION['history']['phase6'])),floor(100*$_SESSION['history']['phase6'])); 
                    }
                    print_r($nrecords);
                
                $nrecords = serialize($nrecords);
                if(!$exists){
                    $sql = "INSERT INTO sectionrecords (id ,chid ,section ,userid,record) VALUES ((SELECT max(id) FROM sectionrecords) +1,'".$_SESSION['chq']['chid']."', '".$_SESSION['chq']['type']."', '".$_SESSION['id']."', '$nrecords')";
                }else{
                    $sql = "UPDATE sectionrecords SET record = '$nrecords' WHERE chid =".$_SESSION['chq']['chid']." AND section = " . $_SESSION['chq']['type'] . " AND userid = " . $_SESSION['id'];
                }    
                sqlite_query($sdb,$sql);
            }
        }else{
            $iscorrect = true;
            //time to finalize and save, then redirect.
            $sql = "SELECT * FROM sectionrecords WHERE chid =".$_SESSION['chq']['chid']." AND section = " . $_SESSION['chq']['type'] . " AND userid = " . $_SESSION['id'];
               $result = sqlite_query($sdb,$sql);
               $exists = 0;            
                while ($row = sqlite_fetch_array($result)) {
                    $id = $row[0];
                    $records = unserialize($row['record']);
                    $exists = 1;
                }
                if (!$exists) {
                    $records = array();
                    echo "we can't get record";
                }
            $match = array();
            foreach($_SESSION['qdata'] as $key=> $dat){
                foreach($_SESSION['adata'] as $key2 => $dat2){
                    if($dat[1] == $dat2[1]){
                        $match[$key] = $key2;
                    }
                }
            }
            $rcount = max(count($records),$key2);
            $rc = max($rcount,count($_SESSION['qdata']));
            $nrecords = $records;
            for ($k = 0; $k < $rc; $k++) {
                if(isset($_SESSION['history'][$k]['wrong'])){
                    $kwrong = count($_SESSION['history'][$k]['wrong']);
                }else{
                    $kwrong = 0;
                }
                if(isset($records[$match[$k]][0])){
                    $k1 = $records[$match[$k]][0];
                }else{
                    $k1 = 0;
                }
                if(isset($records[$match[$k]][1])){
                    $k2 = $records[$match[$k]][1];
                }else{
                    $k2 = 0;
                }
                $nrecords[$match[$k]] = array(($k1+ 1), ($k2+ $kwrong));
            }
            //$nrecords = implode('|', $nrecords);
           // print_r($nrecords);
            $nrecords = serialize($nrecords);
            if(!$exists){
                $sql = "INSERT INTO sectionrecords (id ,chid ,section ,userid,record) VALUES ((SELECT max(id) FROM sectionrecords) +1,'".$_SESSION['chq']['chid']."', '".$_SESSION['chq']['type']."', '".$_SESSION['id']."', '$nrecords')";
            }else{
                $sql = "UPDATE sectionrecords SET record = '$nrecords' WHERE chid =".$_SESSION['chq']['chid']." AND section = " . $_SESSION['chq']['type'] . " AND userid = " . $_SESSION['id'];
            }
             sqlite_query($sdb,$sql);
        }
        
       
    }
    if($iscorrect){
        //echo sqlite_last_error($sdb);
        ?>
        <div class="qcontent">
            Please wait, Finalizing Results.
            </div>
        <div class="qscript hidden">
<script type="text/javascript">
function onloadedy(){
    $(document).ready(function() {
        $.ajax({
                type: "POST",
                url: "chapter.view.php",
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
    });
}

function checkloadedy3(){
        if($('.qworkingarea').find('.qcontent').html() == ''){
                onloadedy();
                isloadingstill = 0;
        }else{
            setTimeout('checkloadedy3()', 50);
        }
     }
     
     checkloadedy3();
</script>
</div>

        <?php
 die();      
    }
}
?>
<div class="qcontent">
    <?php
    //echo $decode;
     //print_r($nrecords);
    switch($_SESSION['qpreset']){
        case 1:
            //mode detection here is needed. Word->Description, Description->Word
            {//keep the sections separate.
                if(!isset($_SESSION['history'][$_SESSION['pos']]['res'])){
                  $_SESSION['history'][$_SESSION['pos']]['res'] = getlikeres($_SESSION['pos'],$_SESSION['qdata'],$_SESSION['adata']);
                  $_SESSION['history'][$_SESSION['pos']]['wrong'] = array();
                }
                
                
                switch($_SESSION['modes'][1]){
                    case 1:
                        echo '<div class="cola prompt">'.fixTheText($_SESSION['qdata'][$_SESSION['pos']][0]).'</div>';
                        foreach($_SESSION['history'][$_SESSION['pos']]['res'] as $response){
                            echo '<div class="aresponse noselect">';
                            echo '<div class="hidden data">';
                            $b64t = new base64salted($secret.$_SESSION['session'].$_SESSION['pos'].$_SESSION['chq']['chid']);
                            //
                            //foreach($_SESSION['qdata'] as $key=> $dat){
                            foreach($_SESSION['adata'] as $key2 => $dat2){
                                if($_SESSION['qdata'][$_SESSION['pos']][1] == $dat2[1]){
                                    $_SESSION['apos'] = $key2;
                                }
                            }
                            
                            
                            if($response == $_SESSION['apos']){//problem lies here
                               $encode =  $b64t->encode((string)1000);
                               echo $encode;
                            }else{
                                $encode = $b64t->encode((string)(1000+pow((int)$response+1,2)));
                                echo $encode;
                            }
                            echo '</div>';//end of hidden data
                            echo '<div class="rtext ';
                            if(in_array($response,$_SESSION['history'][$_SESSION['pos']]['wrong'])){
                                echo 'rwrong';
                            }else{
                                echo 'roption';
                            }
                            echo '">';//end the start of rtext
                            echo fixTheText($_SESSION['adata'][$response][1]);
                            echo '</div>';//end rtext
                            echo '</div>';//end aresponse
                        }
                        
                        break;
                    case 2:
                        echo '<div class="colb prompt">'.stripslashes($_SESSION['qdata'][$_SESSION['pos']][1]).'</div>';
                        foreach($_SESSION['history'][$_SESSION['pos']]['res'] as $response){
                            echo '<div class="aresponse noselect">';
                            echo '<div class="hidden data">';
                            $b64t = new base64salted($secret.$_SESSION['session'].$_SESSION['pos'].$_SESSION['chq']['chid']);
                            //
                            //foreach($_SESSION['qdata'] as $key=> $dat){
                            foreach($_SESSION['adata'] as $key2 => $dat2){
                                if($_SESSION['qdata'][$_SESSION['pos']][1] == $dat2[1]){
                                    $_SESSION['apos'] = $key2;
                                }
                            }
                            
                            
                            if($response == $_SESSION['apos']){
                               $encode =  $b64t->encode((string)1000);
                               echo $encode;
                            }else{
                                $encode = $b64t->encode((string)(1000+pow((int)$response+1,2)));
                                echo $encode;
                            }
                            echo '</div>';//end of hidden data
                            echo '<div class="rtext ';
                            if(in_array($response,$_SESSION['history'][$_SESSION['pos']]['wrong'])){
                                echo 'rwrong';
                            }else{
                                echo 'roption';
                            }
                            echo '">';//end the start of rtext
                            echo ucfirst(fixTheText($_SESSION['adata'][$response][0]));
                            echo '</div>';//end rtext
                            echo '</div>';//end aresponse
                        }
                        break;
                    case 3:
                        //have yet to determine the typing thing.
                        
                        break;
                }
            }
            break;
        case 2:
            //mode detection not needed because cannot be used. no second column
            {//keep the sections separate: put in order
                echo '<div class="prompt">Put the following in the correct order</div>';
                $b64t = new base64salted($secret.$_SESSION['session'].$_SESSION['pos'].$_SESSION['chq']['chid']);
                $justinited = false;
                if(!isset($_SESSION['history'][$_SESSION['pos']]['order'])){
                    $_SESSION['history'][$_SESSION['pos']]['count'] = count($_SESSION['qdata']);
                    $_SESSION['history'][$_SESSION['pos']]['order'] = array();
                    for($x = 0; $x < $_SESSION['history'][$_SESSION['pos']]['count']; $x++){
                        $_SESSION['history'][$_SESSION['pos']]['order'][] = $x;
                    }
                    shuffle($_SESSION['history'][$_SESSION['pos']]['order']);
                    $justinited = true;
                }
                echo '<div class="correctness"><!--where the progress bar is for how correct they are --></div>';
                echo '<ul class="dragables">';
                $realpos = 0;
                foreach($_SESSION['history'][$_SESSION['pos']]['order'] as $item){
                    echo '<li class="dragable ';
                    //Put the 
                    if($waschecked){
                        //ok, so we have checked at least, this means that we can now give a class like pio_lev# to the divs
                        echo 'pio_lev';
                        echo min(5, abs($item-$realpos));
                    }
                    echo '">';
                    echo '<div class="hidden data">';
                    echo '<div class="location">'.$b64t->encode((string)$item).'</div>';
                    echo '</div>';//end of hidden data
                    //now the decorations
                    echo '<div class="movehook"><!--grab it here--></div>';
                    //now the information
                    echo '<div class="rtext';
                    {//section to mark the wrong ones.
                        if(!$justinited){//we don't want to give any begining hints.
                            //I think I am fulfilling this idea above in the li
                            
                        }
                    }
                    echo '">';
                    echo ucfirst(fixTheText($_SESSION['qdata'][$item][0]));
                    echo '</div>';//end the rtext div
                    
                    echo '</li>';//end of dragable
                    $realpos++;
                }
                echo '</ul>';
                echo '<div class="checkorder">Check the order</div>';
                if(!isset($_REQUEST['order'])){
                    $anyotherJS .= "\n$('.correctness').progressBar(0);\n";
                }
                //and set the JS to make a progress bar that is nil
                $anyotherJS .= file_get_contents("js/reqs/question-progressbarinit.js");
            }
            break;
        case 3:
            //mode detection cannot be used because it just isn't logical...
            //multiple choice
            {
                /*
                 Multiple choice array
                 0: which is the correct answer
                 1:the statement
                 2: the options seperated by \n
                */
                echo '<div class="colb prompt">'.stripslashes($_SESSION['qdata'][$_SESSION['pos']][1]).'</div>';
                if(!isset($_SESSION['qdata'][$_SESSION['pos']]['answers'])){
                    //okay, so we have not set our array yet for our answers, they are still in 'text mode'
                    $temp = trim(preg_replace('/(\n)+/im', '\1', stripslashes($_SESSION['qdata'][$_SESSION['pos']][2])));
                    $_SESSION['qdata'][$_SESSION['pos']]['answers'] = preg_split('/(\n)/im', $temp);
                }
                $position = 0;
                foreach($_SESSION['qdata'][$_SESSION['pos']]['answers'] as $text){
                    $text = trim($text);
                    echo '<div class="aresponse noselect">';
                    echo '<div class="hidden data">';
                    $b64t = new base64salted($secret.$_SESSION['session'].$_SESSION['pos'].$_SESSION['chq']['chid']);                    
                    
                    if($position == (int)$_SESSION['qdata'][$_SESSION['pos']][0]){
                       $encode =  $b64t->encode((string)1000);
                       echo $encode;
                    }else{
                        $encode = $b64t->encode((string)(1000+pow((int)$position+1,2)));
                        echo $encode;
                    }
                    echo '</div>';//end of hidden data
                    echo '<div class="rtext ';
                    if(in_array($position,$_SESSION['history'][$_SESSION['pos']]['wrong'])){
                        echo 'rwrong';
                    }else{
                        echo 'roption';
                    }
                    echo '">';//end the start of rtext
                    echo ucfirst(fixTheText($text));
                    echo '</div>';//end rtext
                    echo '</div>';//end aresponse
                    $position++;//add 1 to our position as we cycle through.
                }
            }
            break;
        case 4:
            //Ideally A leads to B leads to C, but we don't wan't the order to be messed up in the students' head so we will not do another mode
            {//however, we must have a default
                //funny, this is like the putting things in order EXCEPT that it is like sub-versions of it.
            }
            break;
        case 5:
            //I seriously doubt people want to type in a defenition letter for letter as a second mode. so no.
            {//however we want a default
                
            }
            break;
        case 6:
            //this is True and False, first we will have to determine what is true or false, and select a few of the opposite.
            //so mode detection is a go.
            {
                switch($_SESSION['modes'][1]){
                    case 1:
                        {
                            echo '<div class="questmesg">'.$_SESSION['qmesg'].'</div>';
                            
                            echo '<div class="colb prompt">'.stripslashes($_SESSION['qdata'][$_SESSION['pos']][1]).'</div>';
                            //start question
                            //print_r($_SESSION);
                            
                            echo '<div class="aresponse noselect">
                                        <div class="hidden data">';
                                        $b64t = new base64salted($secret.$_SESSION['session'].$_SESSION['pos'].$_SESSION['chq']['chid']);
                            //data relies within qdata[pos][0] for whether true or false...
                            if((int)$_SESSION['qdata'][$_SESSION['pos']][0] == 1){
                                echo $b64t->encode((string)1000);
                            }else{
                                echo $b64t->encode((string)1001);//1001-1000-1 = 0, use 0 in the array detection
                            }
                            echo '</div>';//end of hidden data
                            echo '<div class="rtext ';
                            if(in_array(0,$_SESSION['history'][$_SESSION['pos']]['wrong'])){
                                echo 'rwrong';
                            }else{
                                echo 'roption';
                            }
                            echo '">True</div>';//end rtext
                            echo '</div>';//end aresponse
                            
                            
                            //______________SPLIT TRUE AND FALSE__________
                            
                            echo '<div class="aresponse noselect">
                                        <div class="hidden data">';
                            if((int)$_SESSION['qdata'][$_SESSION['pos']][0] == 0){
                                echo $b64t->encode((string)1000);
                            }else{
                                echo $b64t->encode((string)1002);//1002-1000-1 = 1, use 1 in the array detection
                            }
                                        echo '</div>';//end of hidden data
                            echo '<div class="rtext ';
                            if(in_array(1,$_SESSION['history'][$_SESSION['pos']]['wrong'])){
                                echo 'rwrong';
                            }else{
                                echo 'roption';
                            }
                            echo '">false</div>';//end rtext
                            //print_r($_SESSION['history'][$_SESSION['pos']]['wrong']);
                            echo '</div>';//end aresponse
                        }
                        break;
                    
                    case 2:
                        {
                            echo '<div class="questmesg">'.$_SESSION['qmesg'].'</div>';
                            echo '<div class="cola prompt">Which of the following statements is ';
                            if((int)$_SESSION['qdata'][$_SESSION['pos']][0]){
                                echo 'True';
                                $tf = true;
                            }else{
                                echo 'False';
                                $tf = false;
                            }
                            
                            echo '?</div>';
                            //develope the question options
                            if(!isset($_SESSION['history'][$_SESSION['pos']]['res'])){
                                
                                //and get the alternate answers(wrong)
                                $_SESSION['history'][$_SESSION['pos']]['res'] = getlikeresTF($_SESSION['pos'],$_SESSION['qdata']);
                                
                                $_SESSION['history'][$_SESSION['pos']]['wrong'] = array();
                            }
                            
                            //now present the question options
                            
                            foreach($_SESSION['history'][$_SESSION['pos']]['res'] as $response){
                                echo '<div class="aresponse noselect">';
                                echo '<div class="hidden data">';
                                //Section is copied from the qpreset 1
                                $b64t = new base64salted($secret.$_SESSION['session'].$_SESSION['pos'].$_SESSION['chq']['chid']);
                                foreach($_SESSION['adata'] as $key2 => $dat2){
                                    if($_SESSION['qdata'][$_SESSION['pos']][1] == $dat2[1]){
                                        $_SESSION['apos'] = $key2;
                                    }
                                }
                                
                                
                                if($response == $_SESSION['apos']){//problem lies here
                                   $encode =  $b64t->encode((string)1000);
                                   echo $encode;
                                }else{
                                    $encode = $b64t->encode((string)(1000+pow((int)$response+1,2)));
                                    echo $encode;
                                }
                                echo '</div>';//end of hidden data
                                echo '<div class="rtext ';
                                if(in_array($response,$_SESSION['history'][$_SESSION['pos']]['wrong'])){
                                    echo 'rwrong';
                                }else{
                                    echo 'roption';
                                }
                                echo '">';//end the start of rtext
                                echo fixTheText($_SESSION['adata'][$response][1]);
                                echo '</div>';//end rtext
                                echo '</div>';//end aresponse
                            }

                        }
                        break;
                }
            }
            break;
    }
    ?>
</div>
<div class="qscript hidden">
<script type="text/javascript">

function onloadedy(){
    $(document).ready(function() {
        $('.rwrong').unbind();
        $('.roption').unbind();
        $('.roption').ahover({moveSpeed: 200, hoverEffect: function() {
            $(this)
                .css({opacity: 0.40, "background-color": "#aaa"})
                .animate({opacity: 0.1}, 750)
                .animate({opacity: 0.40}, 750)
                .dequeue();
            $(this).queue(arguments.callee);
        }});
        $('.percentb').progressBar(<?php
        //determine how far we are into the data.
        echo ceil(100*($_SESSION['pos']/count($_SESSION['qdata'])));
        ?>);
        $('.aresponse').unbind();
        $('.aresponse').click(function(){
            $('.aresponse').unbind();
            var localthing = $(this);
            $('.goback').fadeOut(300);
        
            $.ajax({
                type: "POST",
                url: "chapter.question.php",
                data: "selected=" + localthing.find('.data').html(),
                success: function(data){
                    
                    $('.qworkingarea').html(data);
                    $('.questionw').slideUp(400, function(){
                        $('.questionw').html($('.qworkingarea').find('.qcontent').html());
                        $('.qworkingarea').find('.qcontent').html('');
                        $('.questionw').slideDown(600);
                        $('.goback').fadeIn(300);
                    });
                }
            });
        });
        $('.rwrong').each( function(index) {
            $(this).toggleClass("awrong");
            //$(this).parent().unbind();
        });
        $('.dragables').sortable({ revert: true, helper: 'clone'});
        $('.dragables').disableSelection();
        //section where we send the information(like .aresponse but for sortables)
        
        <?php
        
        if(isset($anyotherJS)){
            echo $anyotherJS;
        }
        ?>
    });
}
function checkloadedy3(){
        if($('.qworkingarea').find('.qcontent').html() == ''){
                onloadedy();
                isloadingstill = 0;
        }else{
            setTimeout('checkloadedy3()', 50);
        }
     }
     checkloadedy3();
</script>
</div>
    