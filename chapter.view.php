<?php

/**
 * @author Kloplop321
 * @copyright 2010
 */

require("include/base.php");
needrights(1);//need to be authenticated a student at least.
$chapter = (int)trim($_REQUEST['c']);
$title = "Welcome to Chapter $chapter ";

//lets kill our content from the questions
if(isset($_REQUEST['killquiz'])){
    unset($_SESSION['history']);
    unset($_SESSION['chq']);
    unset($_SESSION['adata']);
    unset($_SESSION['qdata']);
    //that should be all that matters.
}



?>
<div class="bcontent ">
    <div class="chtitle">
<?php

$sql = "SELECT comment FROM chapters WHERE chid = $chapter LIMIT 1";
$resu = sqlite_query($sdb, $sql);
$chtitle = sqlite_fetch_array($resu);
echo ucfirst(strtolower($chtitle[0]));
if(strlen($chtitle[0])< 1){
    //we need to say something so user can select and edit.
    echo "No Title Available";
}
echo '</div>';

echo '<div class="testtext">Test on</div>';
$sql = "SELECT * FROM qtypes";
$qtypes = array();
$resu = sqlite_query($sdb, $sql);
while($rowt = sqlite_fetch_array($resu)){
    $qtypes[(int)$rowt[0]] = $rowt;
}
$sql = "SELECT * FROM chq WHERE chid = $chapter";
$resu = sqlite_query($sdb, $sql);
echo '<div class="csecs noselect">';
while($rowt = sqlite_fetch_array($resu)){
    echo '<div class="csection"><div class="chid hidden">' . $chapter . '</div>
    <div class="csectype hidden">' . $rowt['type'] . '</div>
    <div class="chq hidden">' . $rowt[0] . '</div>
    <div class="cscettitle">';
    
    echo $qtypes[(int)$rowt['type']]['name'];
    echo '</div>'."\n";
    //start edit section
    //$_SESSION['rights']
    if(rightsSatis(7)){//assistant teacher and above.
        echo '<div class="editicon">';
            echo '<div class="hidden data2">';
                echo base64_encode(serialize(array($rowt[0],$rowt['type'])));
                //no need to encrypt here, but I'll encode it for data reasons.
            echo '</div>';//end of hidden data
        echo '</div>';//end edit icon div
    }
    //end edit section
    $sql = "SELECT * FROM sectionrecords WHERE chid = " . $rowt['chid'] .
           " AND section = " . $rowt['type'] . " AND userid = " . $_SESSION['id']. " LIMIT 1;";
           
    $result = sqlite_query($sdb,$sql);
    if (sqlite_num_rows($result) > 0) {
        while ($row = sqlite_fetch_array($result)) {
            $record = $row['record'];
           
        }
    }
    
    $records = unserialize($record);
    
    $total = 0;
    $right = 0;
    $rc = 0;
    if (!is_array($records)) {
        $records = array();
    }
    foreach ($records as $key => $record) {
        if(is_numeric($key)){
            if ($record[0] + $record[1] > 0) {
                $right += $record[0] / ($record[0] + $record[1]);
            }
            $rc = max($rc, $key);
        }
    }
    
    $total = explode('|',$rowt['cola']);
    $total = count($total);
        
    //echo "RC=$rc right=$right ";
    echo '<div class="csceperc"><div class="hidden data">';
    $percent  = round(($right/max($rc,1,$total))*100);
    echo $percent;
    echo '</div><div class="percentb"><!-- --> </div>
    </div>
    </div>';
    
    unset($record);
}
echo '</div>';

echo '<div class="chpquick noselect"><div class="chid hidden">' . $chapter . '</div><div class="thetext">Quick Work Book</div></div>'."\n";
echo '<div class="chprint noselect"><div class="chid hidden">' . $chapter . '</div><div class="thetext">Print Work Book</div></div>'."\n";
?>

<div class="goback"><div class="hidden data">apanel.php</div><div class="gbtext">Go Back</div></div>
</div>
    <div class="verify">0</div>
<div class="bscript">
<script type="text/javascript">
if(chphook === undefined){
    var chphook = -1;
}

function onloadedy(){
$(document).ready(function() {
    $('.mtitle').stop(true, true);
    $('.mtitle').slideUp(200, function(){
        $('.mtitle').html('<?php
        echo $title;
        ?>');
        $('.mtitle').slideDown(400);
        
    });
    

    $('.csceperc').each(function(){
        $(this).find('.percentb').progressBar(parseInt($(this).find('.data').html()));
    });
    <?php
    if(rightsSatis(7)){//assistant teacher and above.
        //put the edit JS in below.
    ?>
    $('.editicon').unbind();
    $('.editicon').click( function(){
        $('.editicon').unbind();
        $.ajax({
            type: "POST",
            url: "chq.edit.php",
            data: "chq=" + $(this).parent().find('.chq').html() + "&qt=" + $(this).parent().find('.csectype').html() + "&c=<?php echo $chapter; ?>&init=1",
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
    $('.chtitle').unbind();
    $('.chtitle').click(function(){
        $.fancybox.showActivity();
        $.ajax({
                   type     :   "POST",
                   cache    : false,
                   url  :   "chapter.title.fancy.php",
                   data :   "chid=<?php echo $chapter; ?>",
                   success: function(data){
                    $.fancybox(data,{
                                    'titleShow'	: false,
                                    'transitionIn'	: 'elastic',
                                    'transitionOut'	: 'elastic'
                                    
                            });
                   }
                });
        });
    <?php
    }
    ?>
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
            $('.chpquick').unbind();
             $('.chpquick').click( function(){
        var newWindow = window.open('chapter.workbook.php?c=' + $(this).find('.chid').html(), '_blank');
    newWindow.focus();
    });

$('.chprint').unbind();
             $('.chprint').click( function(){
        var newWindow = window.open('pdf.php?c=' + $(this).find('.chid').html(), '_blank');
    newWindow.focus();
    });
             $('.csection .cscettitle').unbind();
             $('.csection .cscettitle').click(function(){
                $('.csection .cscettitle').unbind();
                $.ajax({
                type: "POST",
                url: "mode.php",
                data: "chq=" + $(this).parent().find('.chq').html() + "&qt=" + $(this).parent().find('.csectype').html() + "&c=<?php echo $chapter; ?>",
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
</div>
