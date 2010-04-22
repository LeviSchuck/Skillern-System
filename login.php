<?php
include_once ("themes.php");
include_once ("connect.php");

if ((int)$_REQUEST['sublogin']) {
    $sql = "SELECT ID, password, username FROM skllern_users WHERE username = '" .
        sqlite_escape_string(strtolower($_REQUEST['user'])) . "' LIMIT 1";
    $result = sqlite_query($sdb, $sql);

    if (sqlite_num_rows($result) > 0) {
    	//echo 'right user';
    	file_put_contents("debug.txt","right user");
        while ($row = sqlite_fetch_array($result)) {
            $id = $row[0];
            $pass = $row[1];
	    $usernameish = $row[2];
        }
    }else{
    	file_put_contents("debug.txt","not user... (".isset($sdb).") $sql\n".sqlite_error_string(sqlite_last_error($sdb)));
    }
    //echo "piclkes";
    if ($pass == sha1($_POST['pass'])) {
        $_SESSION['user'] = strtolower($usernameish);
        $_SESSION['session'] = uniqid();
        $_SESSION['time'] = time();
        $_SESSION['id'] = $id;
        $_SESSION['password'] = sha1(sha1($_REQUEST['pass']));
        $sql = "UPDATE skllern_users SET sessionid = '" . $_SESSION['session'] .
            "' WHERE ID = '" . $id . "'  ";
        $result = sqlite_query($sdb,$sql);
        if(sqlite_last_error($sdb) == 1){
        die("problem with updating session info.. check: $sql<br>".sqlite_error_string(sqlite_last_error($sdb)));
        }
        //now to add the IP's they have visited on
        $sql = "SELECT * FROM skllern_users WHERE ID = '" . $id . "' LIMIT 1";
        $result = sqlite_query($sdb,$sql);
        while ($row = sqlite_fetch_array($result)) {
            $ips = $row[10];
            $lasttime = $row[7];
        }
        if (strlen($ips) > 2) {
            $ips .= ";" . $_SERVER['REMOTE_ADDR'] . "," . time() . "," . $lasttime;
        } else {
            $ips .= $_SERVER['REMOTE_ADDR'] . "," . time() . "," . $lasttime;
        }
        $sql = "UPDATE skllern_users SET ips = '" . sqlite_escape_string($ips) . "' WHERE ID = '" . $id . "'";
        $result = sqlite_query($sdb,$sql);
       // terminate();
        //echo "you made it!";
		//header('Location: usercp.php');
		//echo print_r($_SESSION);
		include('usercp.php');
    } else {
       // terminate();
        $_SESSION['message'] = "Sorry wrong username or password";
        //echo $pass;
        //header('Location: index.php');
        include('index.php');
    }
} else {
    terminate();
    header('Location: index.php');
}


?>