<?php
require("include/base.php");
//$_SESSION['qdata']; //the predetermied data to test with
//$_SESSION['history'];
//$_SESSION['pos']; //the position within the dataset
//$_SESSION['modes'];//the modes, we will mainly use mode1 in this page.
//$_SESSION['qpreset']; //the preset we will base our problems on.
if(isset($_REQUEST['selected'])){
    switch($_SESSION['qpreset']){
        case 1:
            
            break;
    }
    $b64t = new base64salted($secret.$_SESSION['session'].$_SESSION['pos'].$_SESSION['chq']['chid']);
    $decode = $b64t->decode(trim($_REQUEST['selected']));
    if((int)$decode == 1000){
        //ok, we have the right answer!
        $_SESSION['pos']++;
    }else{
        //ok wrong answer, mark it wrong for them.
        $_SESSION['history'][$_SESSION['pos']]['wrong'][] = ceil(sqrt((int)$decode-1000))-1;
    }
    if($_SESSION['pos'] > count($_SESSION['qdata'])-1){//we are past the last question
        //time to finalize and save, then redirect.
        $sql = "SELECT * FROM sectionrecords WHERE chid =".$_SESSION['chq']['chid']." AND section = " . $_SESSION['chq']['type'] . " AND userid = " . $_SESSION['id'];
           echo $sql;
           $result = sqlite_query($sdb,$sql);
           $exists = 0;
            
                while ($row = sqlite_fetch_array($result)) {
                    $id = $row[0];
                    $records = unserialize($row['record']);
                    print_r($records);
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
        print_r($nrecords);
        $nrecords = serialize($nrecords);
        if(!$exists){
            $sql = "INSERT INTO sectionrecords (id ,chid ,section ,userid,record) VALUES ((SELECT max(id) FROM sectionrecords) +1,'".$_SESSION['chq']['chid']."', '".$_SESSION['chq']['type']."', '".$_SESSION['id']."', '$nrecords')";
        }else{
            $sql = "UPDATE sectionrecords SET record = '$nrecords' WHERE chid =".$_SESSION['chq']['chid']." AND section = " . $_SESSION['chq']['type'] . " AND userid = " . $_SESSION['id'];
        }
        
        sqlite_query($sdb,$sql);
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
                        
                        break;
                }
            }
            break;
        case 2:
            //mode detection not needed because cannot be used. no second column
            {//keep the sections separate: put in order
                
            }
            break;
        case 3:
            //mode detection cannot be used because it just isn't logical...
            //multiple choice
            {
                
            }
            break;
        case 4:
            //Ideally A leads to B leads to C, but we don't wan't the order to be messed up in the students' head so we will not do another mode
            {//however, we must have a default
                
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
            {//not working yet
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
                                //develope false statements
                                if(!isset($_SESSION['fdata'])){
                                   $_SESSION['fdata'] = array();
                                    foreach($_SESSION['qdata'] as $tfa){
                                        if($tfa[0] == 0){
                                            $_SESSION['fdata'][] = $tfa;
                                        }
                                    }
                                }
                                //develope true statements
                                if(!isset($_SESSION['tdata'])){
                                    $_SESSION['tdata'] = array();
                                    foreach($_SESSION['qdata'] as $tfa){
                                        if($tfa[0] == 1){
                                            $_SESSION['tdata'][] = $tfa;
                                        }
                                    }
                                }
                                //see if pos is different
                                if($_SESSION['pos'] != $_SESSION['tempd'][0]){
                                    $_SESSION['tempd'][0] = $_SESSION['pos'];
                                    $_SESSION['tempd'][1] = $_SESSION['qdata'][$_SESSION['qdata']];
                                    if($tf){
                                        
                                        $_SESSION['tempd'][1] = array_merge($_SESSION['tdata'],$_SESSION['tdata']);
                                    }else{
                                        $_SESSION['tempd'][1] = array_merge($_SESSION['tdata'],$_SESSION['fdata']);
                                    }
                                }
                                //and get the alternate answers(wrong)
                                $_SESSION['history'][$_SESSION['pos']]['res'] = getlikeresTF($_SESSION['pos'],$_SESSION['qdata'],$_SESSION['tempd'][1]);
                                
                                $_SESSION['history'][$_SESSION['pos']]['wrong'] = array();
                            }
                            
                            //now present the question options
                            echo $_SESSION['qdata'][$_SESSION['pos']][1];
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
                url: "question.php",
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
    