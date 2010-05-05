<?php

/**
 * @author Kloplop321
 * @copyright 2008
 */

session_start();
session_destroy();


?>
<div class="bcontent">
    <div class="centered thicktext">You have been safely logged out!</div>
</div>
<div class="bscript">
<script type="text/javascript">
$(document).ready(function() {
    $('.mcontent').html($('.workingarea').find('.bcontent').html());
    $('.mtitle').slideUp(300).html("You have been logged out.").slideDown(300).delay(1500).fadeOut('slow', function() {
        $('.mainwrapper').fadeOut(600, function(){
            window.onbeforeunload = null;
            location.reload(true);
        });
    });
});
</script>
</div>
