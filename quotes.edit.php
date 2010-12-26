<?php
require("include/base.php");
needrights(7);
if(isset($_POST['new'])){
    sqlite_query($sdb,'INSERT into squotes(ID,quo,bywho) VALUES('.time().',\'new Quote\', \'Unknown\');');
}
?>
<div class="bcontent">
    <ul class="dragables" unselectable="on" style="-moz-user-select: none;">
        <?php
        $sql = "SELECT * FROM squotes WHERE quo != ''";
        $result = sqlite_query($sdb,$sql);
       while ($row = sqlite_fetch_array($result)) {
        ?>
        <li class="dragable">
            <div class="rtext2">
                <div class="colb identm">
                    <div class="hidden data">which=1&id=<?php echo $row[0]; ?></div>
                    <a class="fancy w1id<?php echo $row[0]; ?>"><?php echo $row[1]; ?></a>
                </div>
                <div class="cola answer">
                    <div class="hidden data">which=2&id=<?php echo $row[0]; ?></div>
                    By <a class="fancy w2id<?php echo $row[0]; ?>"><?php echo $row[2]; ?></a>
                </div>
            </div>
        </li>
        <?php
        }
        ?>
    </ul>
    <div class="addbtn">
        <div class="addtext noselect">Add</div>
    </div>
    <div class="goback"><div class="hidden data">apanel.php</div><div class="gbtext">Go Back</div></div>
</div>
<div class="bscript">
<script type="text/javascript">
function onloadedy() {
    $(document).ready(function() {
        $('.mtitle').stop(true, true);
        $('.mtitle').slideUp(300, function(){
            $('.mtitle').html('Edit Quotes');
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
        $('.addbtn').unbind();
        $('.addbtn').click( function(){
            $('.addbtn').unbind();
                $.ajax({
                    type: "POST",
                    url: "quotes.edit.php",
                    data: "new=1",
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
        $('.cola, .colb').unbind();
            $('.cola, .colb').click(function(){
                darday = $(this);
                $.fancybox.showActivity();
                if($(darday).find('.hidden').text().length > 0){
                    $.ajax({
                        type     :   "POST",
                        cache    : false,
                        url  :   "quotes.fancy.php",
                        data :   $(darday).find('.hidden').text(),
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