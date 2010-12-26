<?php
require("include/base.php");
needrights(7);

?>
<div class="fancyframe">
<textarea class="textual"  cols="100" rows="1"><?php
    $qid = (int)trim($_REQUEST['id']);
    $sql = "SELECT * FROM squotes WHERE ID= $qid LIMIT 1";
$resu = sqlite_query($sdb, $sql);
$chtitle = sqlite_fetch_array($resu);
echo ucfirst(strtolower($chtitle[(int)$_POST['which']]));
    ?></textarea>
<div class="savefancy noselect">Save</div>
</div><!-- end of fancyframe -->
<script type="text/javascript">

    $(document).ready(function() {

       $('.savefancy').click(function(){
            $('.savefancy').slideUp(300);
            $('.fancyframe').find('.textual').attr('readonly', 'readonly');
            //start ajax send
            $.ajax({
                url: "quotes.save.php",
                global: false,
                type: "POST",
                data: ({data : $('.fancyframe').find('.textual').val(),
                       qid : <?php echo $qid; ?>,
                       which: <?php echo (int)$_POST['which']; ?>}),
                success: function(msg){
                   if(msg == "good"){
                    $('.w<?php echo (int)$_POST['which']; ?>id<?php echo $qid; ?>').text($('.fancyframe').find('.textual').val());
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