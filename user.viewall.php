<?php
require("include/base.php");
needrights(7);
//okay, so we are authenticated,
//title should be "Select a Period, or search"

?><div class="bcontent">
    <div class="slidersection">
        <div class="mainbutton">Periods</div>
        <div class="smalldescr">List of Periods</div>
        <div class="slidercontent">
            <?php
            //so we know we have 0 through 6 periods to go through, lets just select limit 1 from each period to see if it exists or not yet.
            for($x = 0; $x < 7; $x++){
                $sql = "SELECT firstname FROM skllern_users WHERE classperiod = $x";
                $result = sqlite_query($sdb,$sql);
                if(sqlite_num_rows($result)> 0){
                    echo '<div class="perioditem"><div class="hidden data">'.$x.'</div>
                    Period '.$x.' with '.sqlite_num_rows($result).' people</div>';
                }
            }
            ?>
        </div>
    </div>
    <div class="slidersection">
        <div class="mainbutton">Search</div>
        <div class="searchform">
            <input class="searchbox" type="text" name="searchbox" value="" />
        </div>
        <div class="searchcontent"></div>
        </div>
    </div>    
</div>
<div class="bscript">
<script type="text/javascript">

function onloadedy(){
    $(document).ready(function() {
        var allgoodtoclick = true;
    }
}
var isloadingstill = 1;
function checkloadedye(){
    if($('.workingarea').find('.bcontent').html() == ''){
        onloadedy();
        isloadingstill = 0;
    }else{
            setTimeout('checkloadedye()', 50);
    }
}
checkloadedye();
</script>