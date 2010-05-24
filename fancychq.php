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
            $('.dragable2').each(function(){
                if($(this).find(".data").find('.location').text() == "<?php echo (int)$_REQUEST['sub'];?>"){
                    alert('found');
                    $(this).find(".col<?php echo chr(ord('a')+ (int)$_REQUEST['col']); ?>").find('.fancy').text($('.fancyframe').find('.textual').text());
                    //$.fancybox.close();
                }
                });
       });
    });
</script>