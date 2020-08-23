<?php
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";
$msg = $_GET['msg'];
$hidemenus = false;

$err='';
$msg='';
$processed = false;

if (trim($_POST['act']) == 'changepw') {
  if (trim($_POST['password']) !== '') {
    $query = sprintf( "update familymember set password=%s where email=%s" ,
            sqlsafe(trim($_POST['password'])),
            sqlsafe(getsessionemail($sessionid))
             );
    mhexecquery($query, $err);
    $processed = ($err=='');
    if ($processed) {
         $msg = "Password has been updated";
      }
  }
  else {
    $err='No password supplied.';
  }
}


?>
<html>
<head>
  <title>Malthouse Cottage</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhvalidate.js"></script>
  <script>
  <!--

  function validatepassword() {
      if (document.mainform.password != null ) {
        if (document.mainform.password.value == '') {
           alert('Please enter a password.')
           document.mainform.password.select()
           document.mainform.password.focus()
           return false;
        }
        if (document.mainform.password.value != document.mainform.confirmpassword.value) {
           alert('Passwords do not match.')
           document.mainform.password.select()
           document.mainform.password.focus()
           return false;
        }
      }
    return true;
  }

  function loadform() {
    if (document.mainform.password != null) {
      document.mainform.password.focus()
    }
  }
  //-->
  </script>
</head>

<body onload="loadform()">
<form name="mainform" action="changepassword.php" method="post" onsubmit="return validatepassword()">
<input type="hidden" name="act" value="changepw">
<input name="sess" value="<? echo htmlspecialchars($sessionid) ?>" type="hidden">
<?include 'header.php';?>
  <table width="800" cellpadding="5" cellspacing="0" border="0">
    <tr valign="middle">
      <td colspan="2"><img src="image/spacer.gif" border="0" width="1" height="20" ></td>
    </tr>
<?if (!$processed) {?>
    <tr valign="middle">
      <td align="center" colspan="3">Please enter your new password address and click 'Update Password'.</td>
    </tr>
    <tr valign="middle">
      <td colspan="2"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
    </tr>
<?}
 if ($err == 'invalid') { ?>
    <tr valign="middle">
      <td width="200" align="right">&nbsp;</td>
      <td align="left" class="error">Invalid password.</td>
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
      <td width="200" align="right">New Password:</td>
      <td width="600" align="left"><input type="password" maxlength="50" size="30" name="password" tabindex="1" value=""></td>
    </tr>
    <tr valign="middle">
      <td width="200" align="right">Confirm Password:</td>
      <td width="600" align="left"><input type="password" maxlength="50" size="30" name="confirmpassword" tabindex="2" value=""></td>
    </tr>
    <tr valign="middle">
      <td width="200" align="right">&nbsp;</td>
      <td width="600" align="left"><?createinput_submit("Update Password")?>&nbsp;<?createinput_submit("Cancel", false, "window.history.back()")?></td>
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