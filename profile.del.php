<?php
require("include/base.php");
needrights(8);
//only teachers+ should be able to see this.
if(isset($_POST['del'])){
    if((int)$_POST['del']){
        //okay, I guess they want to delete this person.
        $decoded = $b64c->decode($_REQUEST['uid']);
        $sql = "DELETE from skllern_users WHERE 'skllern_users'.'ID'=".(real)$decoded;
        sqlite_query($sdb,$sql);
        //now redirect and DIE
        header("location: apanel.php");
        die();
    }
}
?>
<div class="bcontent">
    Are you SURE you want to do this?<br />
    <div class="delbtn">
        <div class="deltext noselect">Yes, Delete this person</div>
    </div>
    <div class="cancelbtn">
        <div class="canceltext noselect">No, Get me out of here</div>
    </div>
</div>
<div class="bscript">
<script type="text/javascript">

function onloadedy() {
    $(document).ready(function() {
        console.info("Okay, it looks like we are about to do some fun stuff here");
        $('.delbtn').unbind();
        $('.delbtn').click( function(){
            $('.delbtn').unbind();
            console.info("Okay, here we are in the delete button clicking");
            $.ajax({
                type: "POST",
                url: "profile.del.php",
                data: "uid=<?php echo $_REQUEST['uid']; ?>&del=1",
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
        console.info("okay, so we should now have a delete button, now to make the cancel work");
        $('.cancelbtn').unbind();
        $('.cancelbtn').click( function(){
            $('.cancelbtn').unbind();
            console.info("We have clicked on the cancel button");
            $.ajax({
                url: "apanel.php",
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