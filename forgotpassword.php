<?php
$loggingin = true;
include 'dbutils.php';
include 'utils.php';
$hidemenus = true;

$err='';
$msg='';
$processed = false;

if (trim($_POST['act']) == 'getpw') {
  if (trim($_POST['email']) !== '') {
    $query = sprintf( "select fm.password, fm.email, fm.displayname from familymember fm where fm.email=%s and fm.isuser=1" ,
            sqlsafe(trim($_POST['email'])));
    $passwords = mhexecquery($query, $err);
    $processed = ($err=='');
    if ($processed) {
      if (mysql_num_rows($passwords) >0) {
         $row = mysql_fetch_assoc($passwords);
         $emailmsg = "Hello " . $row['displayname'] . ",\n" . 'Your registered password for Malthouse Cottage is :' . $row['password'];
         sendemailmsg("Malthouse Cottage Details", $emailmsg, $row['email']);
         $msg = 'Your password has been emailed to your registered address.';
      }
      else {
         $err = 'invalid';
         $processed = false;
      }
    }
  }
  else {
    $err='No email address supplied.';
  }
}


?>
<html>
<head>
  <title>Malthouse Cottage</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhvalidate.js"></script>
  <script>
  <!--
  function validatefp() {
    if (document.mainform.email.value=='') {
       alert('Please enter an email address')
       document.mainform.email.focus()
       return false;
    }
    if (document.mainform.email.value.indexOf('@')<0) {
       alert('Please enter a valid email address')
       document.mainform.email.focus()
       return false;
    }

    return true;
  }


  function loadform() {
    if (document.mainform.email != null) {
      document.mainform.email.focus()
    }
  }
  //-->
  </script>
</head>

<body onload="loadform()">
<form name="mainform" action="forgotpassword.php" method="post" onsubmit="return validatefp()">
<input type="hidden" name="act" value="getpw">
<?include 'header.php';?>
  <table width="800" cellpadding="5" cellspacing="0" border="0">
    <tr valign="middle">
      <td colspan="2"><img src="image/spacer.gif" border="0" width="1" height="20" ></td>
    </tr>
<?if (!$processed) {?>
    <tr valign="middle">
      <td align="center" colspan="3">Please enter your email address and click 'Get Password'.  Your password will be emailed to your registered address.</td>
    </tr>
    <tr valign="middle">
      <td colspan="2"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
    </tr>
<?}
 if ($err == 'invalid') { ?>
    <tr valign="middle">
      <td width="200" align="right">&nbsp;</td>
      <td align="left" class="error">Invalid email address.  This email address is not registered as a user of Malthouse Cottage.</td>
    </tr>
<?php 	}
	elseif ($err !== '') { ?>
    <tr valign="middle">
      <td width="200" align="right">&nbsp;</td>
      <td align="left" class="error"><?php echo $err ?></td>
    </tr>
<?php 	}
if ($msg !== '') { ?>
    <tr valign="middle">
      <td colspan="3" align="center" class="message"><?echo $msg ?></td>
    </tr>
<?php 	}
if (!$processed) {
?>
    <tr valign="middle">
      <td width="200" align="right">Email</td>
      <td width="600" align="left"><input type="text" maxlength="100" size="30" name="email" tabindex="1" value="<?php echo htmlspecialchars(trim($_POST['email']))?>"></td>
    </tr>
    <tr valign="middle">
      <td width="200" align="right">&nbsp;</td>
      <td width="600" align="left"><?createinput_submit("Get Password")?>&nbsp;<?createinput_submit("Cancel", false, "window.history.back()")?></td>
    </tr>
<?}?>
    <tr valign="middle">
      <td colspan="2"><img src="image/spacer.gif" border="0" width="1" height="30" ></td>
    </tr>
  </table>
<?include 'footer.php';?>
</form>
</body>
</html>