<?php
require("include/base.php");
//needrights(1);//sorry but no peepers.
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
$viewLevel = 2;
//ok so we need to go and fetch the data on this person to display
$decoded = $b64c->decode($_REQUEST['uid']);
$decoded = 1;
$sql = "SELECT username, firstname, lastname, classperiod, email, timeonline, usertype, image, lasttime FROM skllern_users WHERE 'skllern_users'.'ID'=".(real)$decoded;
$result = sqlite_query($sdb,$sql);
$userInfo = array();
while($row = sqlite_fetch_array($result)){
    $userInfo = array('id'=>(real)$decoded,
                      'username'=>$row['username'],
                      'firstname'=>$row['firstname'],
                      'lastname'=>$row['lastname'],
                      'period'=>$row['classperiod'],
                      'email'=>$row['email'],
                      'timeonline'=>$row['timeonline'],
                      'type'=>$row['usertype'],
                      'image'=>$row['image'],
                      'lasttime'=>$row['lasttime']);
    $userInfo = array_map('stripslashes',$userInfo);
}
?><div class="bcontent">

<div class="profileViewMain">
    <div class="profileText">
        <div class="profileName">
            <div class="profileFirstName"><?php echo $userInfo['firstname'] ;?></div>
            <?php if($viewLevel > 1){?>
            <div class="profileLastName"><?php echo $userInfo['lastname'];?></div>
            <?php }?>
        </div><!-- end of profile name -->
        <div class="profilePeriod">Period: <?php echo $userInfo['period'];?></div>
        <div class="profileStatus">Status: <?php
        if($userInfo['lasttime']  > time()-10*60){
            echo 'Online';
        }else{
            echo 'Offline';
        }
        ?></div><!-- end of profile status-->
    </div><!-- end of profile text -->
    <div class="profileImage">
        <img class="profileImageImg" alt="<?php echo $userInfo['firstname'];?>" src="<?php
        if(strlen($userInfo['image'])> 3){
            echo $userInfo['image'];
        }else{
            echo '';//default image location
        }
        ?>" title="Profile of <?php echo $userInfo['firstname'] ;?>" />
    </div><!-- end of profile image -->
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