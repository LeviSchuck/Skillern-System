<?php
require("include/base.php");
//needrights(1);//sorry but no peepers.
//so we need to determine if we are a student or a teacher.
//but we might as well make sure that they are authenticated.
//rightsSatis is the non-fatal test (true false)

if(rightsSatis(2)){
    $viewLevel = 1;
    if(rightsSatis(7)){
        $viewLevel = 2;
        if(rightsSatis(8)){
            $viewLevel = 3;
        } 
    }
}else{
    //we should not be at this point
    $viewLevel = 0;
}
//ok so we need to go and fetch the data on this person to display
$decoded = $b64c->decode($_REQUEST['uid']);
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
                      'type'=>usertypeToString($row['usertype']),
                      'image'=>$row['image'],
                      'lasttime'=>$row['lasttime']);
    $userInfo = array_map('stripslashes',$userInfo);
}
$title = 'View Profile';
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
        <div class="profilePeriod">Type: <?php echo $userInfo['type'];?></div>
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
            echo 'images/noProfileImage.png';//default image location
        }
        ?>" title="Profile of <?php echo $userInfo['firstname'] ;?>" />
    </div><!-- end of profile image -->
    
    <?php
    echo $viewLevel;
    ?>
</div>
<div class="goback margintop16">
        <div class="gbtext">Go Back</div>
    </div>
    <?php
    if($viewLevel >= 2){
    ?>
    <div class="editbtn">
        <div class="edittext noselect">Edit</div>
    </div>
    <?php
    }
    if($viewLevel >=3){
    ?>
    <div class="delbtn">
        <div class="deltext noselect">Delete</div>
    </div>
    <?php
    }
    ?>
    
</div>
<div class="bscript">
<script type="text/javascript">

function onloadedy() {
    $(document).ready(function() {
        console.log('At the profile View page');
        $('.mtitle').stop(true, true);
        $('.mtitle').slideUp(300, function(){
                $('.mtitle').html('<?php
                echo $title;
                ?>');
                $('.mtitle').stop().slideDown(300);
                console.log('Set the title to <?php echo $title; ?>');
        });
        console.log('Time to set the goback button.');
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
        console.log('Reset go back button to apanel.php');
        $('.delbtn').unbind();
        $('.delbtn').click( function(){
            $('.delbtn').unbind();
            $.ajax({
                type: "POST",
                url: "profile.del.php",
                data: "uid=<?php echo $_REQUEST['uid']; ?>",
                success: function(data){
                    $('.workingarea').html(data);
                    $('.mcontent').slideUp(400, function(){
                        $('.mcontent').html($('.workingarea').find('.bcontent').html());
                        $('.workingarea').find('.bcontent').html('');
                        $('.mcontent').slideDown(600);
                    });
                    console.log('Set the data to working area');
                }
            });
        });
        console.log('Reset Edit button');
        $('.editbtn').unbind();
        $('.editbtn').click( function(){
            $('.editbtn').unbind();
            $.ajax({
                type: "POST",
                url: "profile.edit.php",
                data: "uid=<?php echo $_REQUEST['uid']; ?>",
                success: function(data){
                    $('.workingarea').html(data);
                    $('.mcontent').slideUp(400, function(){
                        $('.mcontent').html($('.workingarea').find('.bcontent').html());
                        $('.workingarea').find('.bcontent').html('');
                        $('.mcontent').slideDown(600);
                    });
                    console.log('Set the data to working area');
                }
            });
        });
    });//end document ready
    
}

     function checkloadedy(){
        if($('.workingarea').find('.bcontent').html() == ''){
            console.log('Time to see if the profile view page is loaded.');
            onloadedy();
        }else{
            setTimeout('checkloadedy()', 50);
        }
     }
     
     
checkloadedy();
</script></div>