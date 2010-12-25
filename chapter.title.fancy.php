<?php
require("include/base.php");
if(!rightsSatis(7)){
    die("You don't belong here.");
}

?>
<div class="fancyframe">
<textarea class="textual"  cols="100" rows="1">
    <?php
    $chapter = (int)trim($_REQUEST['chid']);
    $sql = "SELECT comment FROM chapters WHERE chid = $chapter LIMIT 1";
$resu = sqlite_query($sdb, $sql);
$chtitle = sqlite_fetch_array($resu);
echo ucfirst(strtolower($chtitle[0]));
    ?>
</textarea>
<div class="savefancy noselect">Save</div>
</div><!-- end of fancyframe -->
<script type="text/javascript">
    $(document).ready(function() {
       $('.savefancy').click(function(){
            $('.savefancy').slideUp(300);
            $('.fancyframe').find('.textual').attr('readonly', 'readonly');
            //start ajax send
            $.ajax({
                url: "chapter.title.save.php",
                global: false,
                type: "POST",
                data: ({data : $('.fancyframe').find('.textual').val(),
                       chid : <?php echo $_REQUEST['chid']; ?>}),
                success: function(msg){
                   if(msg == "good"){
                    $('.chtitle').text($('.fancyframe').find('.textual').val());
                    $.fancybox.close();
                   }else{
                    alert("There seemed to have been an error: " + msg);
                    $('.savefancy').slideDown(300);
                    $('.fancyframe').find('.textual').attr('readonly', '');
                   }
                },
                error : function(msg){
                    alert("There seemed to have been an error: " + msg.statusText);
                    $('.savefancy').slideDown(300);
                    $('.fancyframe').find('.textual').attr('readonly', '');
                }
            });
        });
    });
</script>