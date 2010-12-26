<?php
require("include/base.php");
/*
$sql = "Delete from calendar where 1";
sqlite_query($sdb, $sql);
$total = 0;
for($x = -300; $x < 365*7; $x++){
    $date = mktime(0,0,0,date('m'),$x,date('Y'));
    echo $date.' ';
    $data = 'Today is the '.date('j/-z',$date);
    $sql = "INSERT into calendar(ID,data,time) values($total,'$data',$date)";
    sqlite_query($sdb, $sql);
    
    $total++;
}*///Enter data into the database for calendars for about 7 years


needrights(7);
if(!isset($_POST['month'])){
    $month = (int)date('n');
}else{
    $month = (int)$_POST['month'];
}
if(!isset($_POST['year'])){
    $year = (int)date('Y');
}else{
    $year = (int)$_POST['year'];
}
$first = mktime(0,0,0,$month,1,$year);
$last = mktime(0,0,-1,$month+1,1,$year);
$sql = "SELECT * FROM 'calendar' WHERE time >= $first AND time <= $last order by time asc";
$days = array('Sunday','Monday', 'Tuesday', 'Wednesday','Thursday','Friday', 'Saturday');


?>
<div class="bcontent">
    <div class="goprev"><div class="hidden data">month=<?php echo $month-1; ?></div> Previous Month</div>
    <div class="gonext"><div class="hidden data">month=<?php echo $month+1; ?></div> Next Month</div>
    <div class="chtitle"><?php echo date('F Y',$first) ?></div>
    <div class="pcalend panelp">
        <?php
        $result = sqlite_query($sdb, $sql);
        $gotfirst = false;
        while ($row = sqlite_fetch_array($result)) {
            $today = $row['time'];
            //echo date('w',$today);
            for($x = 0; $x < date('w',$today) && !$gotfirst; $x++){
                echo '<div class="calday calno"><div class="weekt">'.$days[$x].'</div></div>';
            }
            echo '          <div class="calday';
            if(date('Y-m-d')==date('Y-m-d',$today)){
                echo ' ctoday';
            }
            echo '"><div class="weekt">'.date('l',$today).'</div>';
            echo "\n\t\t<div class=\"hidden c$today\">$today</div>";
            echo "\n\t\t<div class=\"textish\">$row[1]</div>";
            echo "</div>\n";
            $gotfirst= true;
        }
        for($x = date('w',$today)+1; $x < 7; $x++){
                echo '<div class="calday calno"><div class="weekt">'.$days[$x].'</div></div>';
            }
        ?>
    </div>
    <div class="goback"><div class="hidden data">apanel.php</div><div class="gbtext">Go Back</div></div>
    <div class="goprev"><div class="hidden data">month=<?php echo $month-1; ?></div> Previous Month</div>
    <div class="gonext"><div class="hidden data">month=<?php echo $month+1; ?></div> Next Month</div>
</div>
<div class="bscript">
    <script type="text/javascript">
    function onloadedy() {
        $(document).ready(function() {
            $('.mtitle').stop(true, true);
            $('.mtitle').slideUp(300, function(){
                $('.mtitle').html('Edit the Calendar');
                $('.mtitle').slideDown(300);
            });
            $('.goback').unbind();
            $('.goback').click( function(){
                $('.goback').unbind();
                $.ajax({
                    type: "POST",
                    url: "apanel.php",
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
            $('.gonext,.goprev').unbind();
            $('.gonext,.goprev').click( function(){
                $('.gonext,.goprev').unbind();
                thisdata = $(this).find('.data').text();
                $.ajax({
                    type: "POST",
                    url: "calendar.edit.php",
                    data: thisdata,
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
            
            $('.calday').unbind();
            $('.calday').click(function(){
                darday = $(this);
                $.fancybox.showActivity();
                if($(darday).find('.hidden').text().length > 0){
                    $.ajax({
                        type     :   "POST",
                        cache    : false,
                        url  :   "calendar.fancy.php",
                        data :   "time="+$(darday).find('.hidden').text(),
                        success: function(data){
                            $.fancybox(data,{
                                'titleShow'	: false,
                                'transitionIn'	: 'elastic',
                                'transitionOut'	: 'elastic'   
                            });
                        }
                    });
                }
            });
        });
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