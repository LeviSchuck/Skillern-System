<?php
require("include/base.php");
needrights(7);
//okay, so we are authenticated,
//title should be "Select a Period, or search"

?><div class="bcontent">
    <div class="slidersection periods">
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
                    <div class="period">Period</div>
                    <div class="periodnum">'.$x.'</div>
                    <div class="periodwith">with</div>
                    <div class="periodnum">'.sqlite_num_rows($result).'</div>
                    <div class="periodpeople">people</div>
                    </div>';
                }
            }
            ?>
        </div>
    </div>
    <div class="slidersection searching">
        <div class="mainbutton">Search</div>
        <div class="searchform textf">
            <input class="searchbox" type="text" name="searchbox" value="" />
        </div>
        <div class="searchcontent slidercontent"></div>
    </div>
    <div class="goback margintop16">
        <div class="gbtext">Go Back</div>
    </div>
    <div class="addbtn">
        <div class="addtext noselect">Add</div>
    </div>
</div>
<div class="bscript">
<script type="text/javascript">

function onloadedy(){
    $(document).ready(function() {
        var allgoodtoclick = true;
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
        var activatedTab = "periods";
        $('.searching .mainbutton').click(function(){
            if(activatedTab != "searching"){
                $('.periods .slidercontent').slideUp(400);
                $('.searching .slidercontent').slideDown(400);
                activatedTab = "searching";
            }
        });
        $('.periods .mainbutton').click(function(){
            if(activatedTab != "periods"){
                $('.periods .slidercontent').slideDown(400);
                $('.searching .slidercontent').slideUp(400);
                activatedTab = "periods";
            }
        });
        $('.searchbox').change(function(){
            if(activatedTab != "searching"){
                $('.periods .slidercontent').slideUp(400);
                activatedTab = "searching";
            }
            //okay, now to send the form to the user.search.php or something.
            $.ajax({
                type: "POST",
                url: "user.search.php",
                data: ({searchTerms: $('.searchbox').val()}),
                success: function(data){
                    $('.searching .searchcontent').html(data);
                    $('.searching .slidercontent').slideDown(400);
                }
            });
        });
        $('.perioditem').click(function(){
            periodnumber = $(this).find('.data').text();
            $('.searchcontent').html(' ');
            $('.searchbox').val('period:'+periodnumber);
            $('.searchbox').trigger('change');
        });
        $('.searchResult').die('click');
        $('.searchResult').live('click',function(){
            //console.log('clicked');
            $(this).slideUp(400);
            if($(this).find('.userID').length != 0){
                //console.log('we should get something');
                //we should be good to search and view this user.
                $.ajax({
                    type: "POST",
                    url: "profile.view.php",
                    data: {'uid' : $(this).find('.userID').text()},
                    success: function(data){
                        $('.workingarea').html(data);
                        $('.mcontent').slideUp(400, function(){
                            $('.mcontent').html($('.workingarea').find('.bcontent').html());
                            $('.workingarea').find('.bcontent').html('');
                            $('.mcontent').slideDown(600);
                        });
                    }
                });
            }else {
                //console.log('don\'t click');
            }
        });
    });
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
</div>
