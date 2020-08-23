<?
	require ('encrypt.inc');



if (!$loggingin) {

	$sessionid = $_POST['sess'];
	if ($sessionid == '') {
	  $sessionid = $_GET['sess'];
	}

	if ($sessionid == '') {
		Header("Location: index.php?err=sess");
	}
    $sessionid = decryptsession($sessionid);
	if (!checksessiontimeout($sessionid)) {
		Header("Location: index.php?err=sess");
}

}

function checksessiontimeout(&$sessionid) {
    list($email, $user, $logintime, $admin) =  split('[|]', $sessionid);
	if ($logintime == "" || $user=='') {
		Header("Location: index.php?err=sess");
	}
	$timediff = datediff(n, $logintime, date('Y-m-d H:i:s',time()));
	if ($timediff > 60) {
		Header("Location: index.php?err=sess&timdeiff=" . urlencode($timediff));
	}

	//update the session
	$sessionid = $email . "|" . $user . "|" . date('Y-m-d H:i:s',time()) . "|" . $admin;

 	return true;
}



function loginuser() {
	$err = "";
    $thisname = "";
    $admin = 0;
	if ($_POST['fn'] == 'login' && $err=='') {

 	  //Get the list of valid users

      $query = sprintf("SELECT fm.displayname, fm.admin as isadmin, fm.email, fm.lastlogindate FROM familymember fm where fm.email=%s and fm.password=%s and isuser=1",
                    sqlsafe(($_POST['email'])),
                    sqlsafe($_POST['password']));
      // Perform Query
      $result = mhexecquery($query, $err);

      // Check result
      if ($err == "") {
        while ($row = mysql_fetch_assoc($result)) {
          $thisname = $row['displayname'];
          $thisemail = $row['email'];
          $admin = $row['isadmin'];
        }

        if ($thisname == "") {
          $err = "invalid";
        }
        else {

          $session = encryptsession($thisemail . '|' . $thisname . '|' . date('Y-m-d H:i:s',time()) . '|' . $admin);
          setcookie('mhemail', $_POST['email'], time()+60*60*24*365 );
		  Header("Location: welcome.php?sess=" . urlencode($session));
  	    }

      }

	}

	return $err;
}

function loginuserfile() {
	$err = "";

	if ($_POST['fn'] == 'login' && $err=='') {
	  $email = trim($_POST['email']);
	  $password = trim($_POST['password']);

 	  global $USER_FILE;
 	  global $LAST_LOGIN_DATE;
 	  //Get the list of valid users
	  $users = mhreadfilearray($USER_FILE, $err);

	  // check whether email and password is valid.
	  foreach ($users as $user_num => $user) {

	    if ($user_num > 0 ) { //first line is file header
          list($thisemail, $thispassword, $thisname) =  split('[,]', $user);
	      if (strcasecmp($email, trim($thisemail)) == 0 && strcmp($password, trim($thispassword)) == 0) {
		    $session = encryptsession($thisname . '|' . date('Y-m-d H:i:s',time()));
	        setcookie('mhemail', $email, time()+60*60*24*365 );
	        //Log the last login time
		    Header("Location: welcome.php?sess=" . urlencode($session));
		    break;
	      }
	      else {
		    $err = "invalid";
	      }
	    }
	  }
	}

	return $err;
}



?>