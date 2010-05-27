<?php
require("include/base.php");
//we still should be able to depend on our session...
echo '<div class="fancyframe">';
echo '<textarea class="textual"  cols="100" rows="5">';
echo $_SESSION['editor']['qdata'][(int)$_REQUEST['sub']][(int)$_REQUEST['col']];
echo '</textarea>';

?><div class="savefancy noselect">Save</div>
</div><!-- end of fancyframe -->
<script type="text/javascript">
    $(document).ready(function() {
       $('.savefancy').click(function(){
            $('.savefancy').slideUp(300);
            $('.fancyframe').find('.textual').attr('readonly', 'readonly');
            //start ajax send
            $.ajax({
                url: "savechq.php",
                global: false,
                type: "POST",
                data: ({data : $('.fancyframe').find('.textual').val(),
                       sub : <?php echo (int)$_REQUEST['sub'];?>,
                       col : <?php echo (int)$_REQUEST['col'];?>,
                       chqid : <?php echo $_SESSION['editor']['chq']['ID']; ?>,
                       qtype : <?php echo (int)$_SESSION['editor']['qtype']['preset']; ?>}),
                success: function(msg){
                   if(msg == "good"){
                    $('.dragable2').each(function(){
                        if(parseInt($(this).find(".data").find('.location').text()) == <?php echo (int)$_REQUEST['sub'];?>){
                            $(this).find(".col<?php echo chr(ord('a')+ (int)$_REQUEST['col']); ?>").find('.fancy').text($('.fancyframe').find('.textual').val());
                            $.fancybox.close();
                        }
                    });
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