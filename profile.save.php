<?php
require("include/base.php");
//error preset 0
$error['email'] = 0;
$error['passblank'] = 0;
$error['verify'] = 0;
$error['user'] = 0;

$username = strtolower(trim($_REQUEST['u']));
$password = trim($_REQUEST['p']);
$verify = trim($_REQUEST['v']);
$email = trim($_REQUEST['e']);
$utype = intval(trim($_REQUEST['t']))-1;
if(rightsSatis(7)){
    $uid = intval(trim($_REQUEST['uid']));
}else{
    $uid = $_SESSION['id'];
}

$sql = "SELECT username FROM skllern_users WHERE ID = '" .  $uid. "' LIMIT 1";
        $result = sqlite_query($sdb,$sql);
        while ($row = sqlite_fetch_array($result)) {
            $curuser = $row[0];
        }

//verify email
if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
$sql = "UPDATE skllern_users SET email = '".sqlite_escape_string($email)."' WHERE ID = ".$uid;
   $res = sqlite_query($sdb,$sql);
   if(sqlite_last_error($sdb) > 0){
      $error['email'] = 1;
      //echo "SQL error email ".sqlite_error_string(sqlite_last_error($sdb)).";";
   }
   
}else{
   $error['email'] = 1;
}
//see if blank password
if($password == ''  && $verify != ''){

   $error['passblank'] = 1;
}
//see if ver not correct
if($password != $verify){
   if(!$error['passblank']){
      $error['verify'] = 1;
   }
}else{
   if(!$error['passblank'] && $password != ''){
      $sql = "UPDATE skllern_users SET password = '".sha1($password)."' WHERE ID = ".$uid;
      $res = sqlite_query($sdb,$sql);
      if(sqlite_last_error($sdb) > 0){
         $error['verify'] = 1;
         //echo "SQL error pass ".sqlite_error_string(sqlite_last_error($sdb)).";";
      }
   }
}
//check if user exists or is blank
if($username == ""){
   $error['user'] = 1;
}
//exits?

if($username != strtolower($curuser)){
    echo $_SESSION['user']."_".$username;
    $sql ="SELECT ID FROM skllern_users WHERE username = '".sqlite_escape_string($username)."'";
    $res = sqlite_query($sdb,$sql);
    $count = 0;
    while($row = sqlite_fetch_array($res)){
       $count++;
    }
    if($count > 0){
       $error['user'] = 1;
    }else{
       $sql = "UPDATE skllern_users SET username = '".sqlite_escape_string($username)."' WHERE ID = ".$uid;
       $res = sqlite_query($sdb,$sql);
       if(sqlite_last_error($sdb) > 0){
          $error['user'] = 1;
          //echo "SQL error User ".sqlite_error_string(sqlite_last_error($sdb)).";";
       }
    }
}
if(rightsSatis(7)){
    //ok, so we may change the user type(but they must be a TA or greater.)
    if($utype > -1){
        if($utype < $_SESSION['rights']){
            $sql = "UPDATE skllern_users SET usertype = $utype WHERE ID = ".$uid;
            $res = sqlite_query($sdb,$sql);
            if(sqlite_last_error($sdb) > 0){
               $error['usertype'] = 1;
            }
        }
        
    }
}


$fail = 0;
if(in_array(1,$error)){
   $fail = 1; 
}
?>
<div class="bscript">
<script type="text/javascript">
   $(document).ready(function(){
      $('.baduser').fadeOut(500);
      $('.noemail').fadeOut(500);
      $('.noblank').fadeOut(500);
      $('.wrongpass').fadeOut(500);
      <?php
      if(!$fail){
         ?>
      $('.csucc').fadeIn(500);
         <?php
      }else{
         ?>
         $('.csucc').fadeOut(500);
         <?php
         //find reasons why failure
         foreach($error as $key => $errory){
            if($errory){
               switch($key){
                  case "user":
                     ?>
      $('.baduser').fadeIn(500);
                     <?php
                     break;
                  case "email":
                     ?>
      $('.noemail').fadeIn(500);
                     <?php
                     break;
                  case "passblank":
                     ?>
      $('.noblank').fadeIn(500);
                     <?php
                     break;
                  case "verify":
                     ?>
      $('.wrongpass').fadeIn(500);
                     <?php
                     break;
                    case "usertype":
                        ?>
      $('.novalue').fadeIn(500);
                     <?php
                        break;
               }
            }
         }
      }
      ?>
   });
   </script>
</div>