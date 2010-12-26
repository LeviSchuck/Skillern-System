<?php
require("include/base.php");
needrights(1);//at least a student.
$title = "Welcome and study well";
?>
<div class="bcontent">
    <div class="lcolw">
    <!-- start options panel -->
    <div class="poptions panelp">
        <div class="ptitle noselect">Your options</div>
        <div class="logout noselect">Log Out</div>
        <div class="eprofl noselect">Edit your profile</div>
        <div class="pcornell noselect">Print Cornell notes</div>
    </div>
    <!-- end options panel -->
        
    <!-- start random quote panel -->
    <div class="prandomq panelp">
        <div class="ptitle noselect">Random Quote</div>
        <?php
        
        $sql = "SELECT * FROM squotes WHERE quo != '' ORDER BY Random() LIMIT 0,1";
        $result = sqlite_query($sdb,$sql);
        $rows = array();
        while ($row = sqlite_fetch_array($result)) {
                if(strlen($row[1])>0){
             echo  $row[1].' -- '.$row[2];
             //print_r($row);
            }
        }
        ?>
    </div>
    <!-- end random quote panel -->
    </div>
    <!-- start chapters panel -->
    <div class="pchaptersw panelp">
        
        <div class="ptitle noselect">Book Chapters</div>
        <div class="pchapters">
            <div class="pcurchap">
                <div class="pchaptext noselect">Current Chapter <?php
            $sql = "SELECT data FROM trdata WHERE ID = 1 LIMIT 1";
            $result = sqlite_query($sdb,$sql);
            while ($row = sqlite_fetch_array($result)) {
                $currentc = (int)$row[0] - 1;
            }
            echo $currentc;
            ?></div>
        <div class="pchapid">
            <?php
            echo $currentc;
            ?>
            </div>
            </div>
        <?php
        //there are 42 chapters. Not likely to change.
        //oh and 42 is the answer to everything
        $beg = '<div class="pchap">
        <div class="pchaptext noselect">Chapter ';
        $mid = '</div>
        <div class="pchapid">';
        $end = '</div>
        </div>';
        for($x = 1; $x <= 42; $x++){
            echo $beg.$x.$mid.$x.$end;
        }
        ?>
    </div>
        
    </div>
    <!-- end chapters panel -->
    <div class="rcolw">
    <!-- start who is online panel -->
    <div class="ponline panelp">
        <div class="ptitle noselect">Who's online now?</div>
        <?php
        $sql = "SELECT * FROM skllern_users WHERE lasttime > ".(time()-8*60)." ORDER BY lasttime DESC";
        $result = sqlite_query($sdb, $sql);
        $users = array();
        $usery = array();
        while ($row = sqlite_fetch_array($result)) {
            $users[] = $row[3]." ". $row[4];
            $usery[] = $row[0];
        }
        $ucount = count($users);
        for($x = 0; $x < $ucount ; $x++){
                echo '<div class="puseri">';
                echo '<div class="puserd">';
                echo $b64c->encode( $usery[$x]);
                echo '</div>';
                echo '<div class="pusert noselect">';
                echo $users[$x];
                echo '</div>';
                echo '</div>'."\n";
        }
        ?>
    </div>
    <!-- end who is online panel -->
    <?php
    //Administration section.
    if($_SESSION['rights'] > 5){
        ?>
        <!-- start admin options panel -->
    <div class="padmin panelp">
        <div class="ptitle noselect">Administration</div>
        <div class="cuser noselect">Create User</div>
        <div class="vusers noselect">View Users</div>
        <div class="smail noselect">Send Mass Email</div>
        <div class="rlrec noselect">Reset Log in records</div>
        <div class="caled noselect">Edit Calendar</div>
        <div class="equot noselect">Edit Quotes</div>
    </div>
    <!-- end admin options panel -->
        
        <?php
    }
    ?>
    </div>

    <?php
    
    //now the wonderful calendar.
    //first we must get the data to see if we even have anything to display...
    //since skillern is negligant on that standard unfortunately.
    
    $now = mktime(0,0,1,date("n"),date("j"),date("Y"))+3*24*60*60;
    $monday = $now-4*24*60*60;//t == num days in month
    $friday = $now+4*24*60*60;
    $sql = "SELECT * FROM calendar WHERE time > " . ($monday - 60) .
        " AND time < " . ($friday + 60) ;
    $result = sqlite_query($sdb,$sql);
    $datas = array();
    $hasSomething = false;
    while ($row = sqlite_fetch_array($result)) {
        $datet = stripslashes(trim(preg_replace('/Today is the .?[\d]\/\-.?[\d]?.?[\d]/i', '',$row[1])));
        //if($datet != ''){
            $hasSomething = true;//}
        $datas[] = array($row[2], $datet, $row[0]);
    }
    if($hasSomething){
        ?>
        <!-- start calendar panel -->
        <div class="pcalend panelp">
            <div class="ptitle noselect">Weekly Calendar</div>
            <div class="calcontent noselect panelp">
            <?php
            //echo blank dates for left padding
            $week = array("Monday", "Tuesday","Wednesday" ,"Thursday", "Friday", "Saturday", "Sunday");
            if (date("N", (int)$datas[0][0]) > 1) {
                for ($dayo = 1; $dayo < date("N", (int)$datas[0][0]); $dayo++) {
                    echo "\t\t\t\t<div class=\"calday calno\"><div class=\"weekt\">".$week[$dayo-1]."</div></div>\n";
                }
                $dayo++;
            }
            //now we put out the actual calendar content.
            for ($x = 0; $x < count($datas); $x++) {
                $curday = (int)date("N", (int)$datas[$x][0]);
                if ($curday == 1) {
                    $noleft = "noleft";
                    $dayo = 0;
                }
                if(date("j",(int)$datas[$x][0]) == date("j")){
                    $noleft = 'ctoday';
                }
                echo "\t\t\t\t<div class=\"calday $ctoday $noleft\"><div class=\"weekt\">".date("l",(int)$datas[$x][0])."</div>\n".$datas[$x][1]."</div>\n";
                $noleft = null;
                $ctoday = null;
            }
            //time to pad to right now.
            for ($dayo = $curday; $dayo < 7; $dayo++) {
                echo "\t\t\t\t<div class=\"calday calno\"><div class=\"weekt\">".$week[$dayo]."</div></div>\n";
            }
            ?>
            </div>
        </div>
        <!-- end calendar panel -->
        <?php
    }
    
    
    ?>
</div>
<div class="bscript">
<script type="text/javascript">
if(panhook === undefined){
    var panhook = -1;
}
    function onloadedy() {
        $(document).ready(function() {
    $('.mtitle').stop(true, true);
    $('.mtitle').slideUp(300, function(){
        $('.mtitle').html('<?php
        echo $title;
        ?>');
        $('.mtitle').slideDown(300);
        
    });

    /* DO NOT USE LIVE */
    $('.logout').click( function(){
        $('.logout').unbind();
            $.ajax({
                type: "POST",
                url: "logout.php",
                data: "sub=1",
                success: function(data){
                    $('.logout').expire();
                    $('.workingarea').html(data);
                    
                }
            });
        
    });
/* DO NOT USE LIVE */
    $('.pchaptext').click( function(){
        $('.pchaptext').unbind();
            $.ajax({
                type: "POST",
                url: "chapter.view.php",
                data: "c=" + $(this).parent().find('.pchapid').html(),
                success: function(data){
                    $('.workingarea').html(data);
                    $('.mcontent').slideUp(400, function(){
                        $('.mcontent').html($('.workingarea').find('.bcontent').html());
                        $('.mcontent').slideDown(600);
                        $('.workingarea').find('.bcontent').html('');
                       
                    });
                }
            });
        
    });
    $('.caled').unbind();
    $('.caled').click( function(){
        $('.caled').unbind();
        $.ajax({
            type: "POST",
            url: "calendar.edit.php",
            data: "",
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
    $('.equot').unbind();
     $('.equot').click( function(){
        $('.equot').unbind();
        $.ajax({
            type: "POST",
            url: "quotes.edit.php",
            data: "",
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
    $('.vusers').click( function(){
        $('.vusers').unbind();
            $.ajax({
                type: "POST",
                url: "user.viewall.php",
                data: "",
                success: function(data){
                    $('.workingarea').html(data);
                    $('.mcontent').slideUp(400, function(){
                        $('.mcontent').html($('.workingarea').find('.bcontent').html());
                        $('.mcontent').slideDown(600);
                        $('.workingarea').find('.bcontent').html('');
                       
                    });
                }
            });
        
    });

    });
        $('.eprofl').unbind();
        $('.eprofl').click(function(){
            $('.eprofl').unbind();
            $.ajax({
                url: "profile.edit.php",
                success: function(data){
                    $('.workingarea').html('');
                    $('.workingarea').html(data);
                    $('.mcontent').slideUp(400, function(){
                        $('.mcontent').html($('.workingarea').find('.bcontent').html());
                        $('.mcontent').slideDown(600);
                        $('.workingarea').find('.bcontent').html('');
                    });
                }
            });
        });
        $('.pcornell').unbind();
        $('.pcornell').click(function(){
             var newWindow = window.open('notes.pdf', '_blank');
    newWindow.focus();
        });
        
        
        
        
    }//end the onleadedy
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