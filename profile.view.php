<?php
require("include/base.php");
needrights(1);//sorry but no peepers.
//so we need to determine if we are a student or a teacher.
//but we might as well make sure that they are authenticated.
//rightsSatis is the non-fatal test (true false)

if(rightsSatis(2)){
    $viewLevel = 1;    
}else if(rightsSatis(7)){
    $viewLevel = 2;
}else{
    //we should not be at this point
    $viewLevel = 0;
}

?><div class="bcontent">

<div class="profileViewMain">
    <div class="profileImage"><img class="profileImageImg" alt="<?php ?>" src="<?php ?>" title="Profile of <?php ?>" /></div>
    <div class="profileFirstName"><?php ?></div>
    <?php ?><div class="profileLastName"><?php ?></div><?php ?>
    
</div>

</div>
<div class="bscript">
<script type="text/javascript">

function onloadedy() {
    $(document).ready(function() {
        $('.mtitle').stop(true, true);
        $('.mtitle').slideUp(300, function(){
                $('.mtitle').html('<?php
                echo $title;
                ?>');
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
    });//end document ready
    
}

     function checkloadedy(){
        if($('.workingarea').find('.bcontent').html() == ''){
            onloadedy();
        }else{
            setTimeout('checkloadedy()', 50);
        }
     }
     
     
checkloadedy();
</script></div>