<?php
$hidemenus = true;
$loggingin = true;
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';
if (trim($_GET['err']) !== '') {
	 $err = $_GET['err'];
}
else {
	$err = loginuser();
}

if ($_POST['fn'] !== 'login') {
	  $email = $_COOKIE['mhemail'];
}



$query = "select title, news from news where archive=0" ;
$news = mhexecquery($query, $err);


?>
<html>
<head>
  <title>Malthouse Cottage</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhvalidate.js"></script>
</head>

<body onload="document.mainform.email.focus()">
<form name="mainform" action="index.php" method="post" onsubmit="return validatelogin()">
<input type="hidden" name="fn" value="login">
<?include 'header.php';?>
  <table width="800" cellpadding="0" cellspacing="0" border="0">
    <tr valign="middle">
      <td colspan="2">
        <table width="800" cellpadding="0" cellspacing="0" border="0">
          <tr valign="top">
	          <td width="200" rowspan="2" align="center"><img src="image/mh2.gif" border="0" width="200" height="267"><br>
				 <img src="image/spacer.gif" border="0" width="1" height="1"><br>
				 <?
				 $infomsg = '<b>Malthouse Cottage</b>'
				          . '<p>Malthouse Cottage is an old place located in Fareham, England and is believed to be the home of a number of ghosts.</p>'
				          . '<p><a href="javascript:alert(\'Waiting for more information from the Management\')">To read more about the history of Malthouse Cottage, click here.</a></p>';

				 echo writeHTMLinfobox($infomsg, '140');
				 ?>

	          </td>
			  <td width="400"><img src="image/spacer.gif" border="0" width="1" height="10"></td>
			  <td width="200" rowspan="2" class="mhrightsection">
				 <table width="180" cellpadding="5" cellspacing="0" border="0">
					<tr valign="middle">
					  <td align="center" colspan="2">
					    <img src="image/spacer.gif" border="0" width="1" height="10">
				        <center><b>Latest News</b></center>
<?
$rowcount = 0;
while ($row = mysql_fetch_assoc($news)) {
    $rowcount .= 1;
?>                       <p>
				 <?
				 $infomsg = '<center><b>' . $row['title'] . '</b></center>'
				          . '<p>' . $row['news'] . '</p>';

				 echo writeHTMLinfobox($infomsg, '140');
				 ?>

				        </p>
<?
}
if ($rowcount == 0) {
?>
				        <p>There is no news at the moment.</p>
<?}?>
					  </td>
					</tr>
				 </table>
			  </td>
		  </tr>
          <tr valign="top">
			  <td width="400" align="center" class="mhleftsection">
				 <table width="300" cellpadding="5" cellspacing="0" border="0">
					<tr valign="middle">
					  <td align="left" colspan="2">
				        <h2>Welcome to...</h2>
				        <p>... The Malthouse Cottage (V2) and Stevenson, Mackee, Offord and associated families website.</p>
   				        <p>If you have a login id and password, please login to access the site.</p>
					  </td>
					</tr>
					<tr valign="middle">
					  <td align="left" colspan="2">
					    <p>&nbsp;</p>
				        <h3>Malthouse Users</h3>
					  </td>
					</tr>
					<?php 	if ($err == 'invalid') { ?>
					<tr valign="middle">
					  <td align="right">&nbsp;</td>
					  <td align="left" class="error">Invalid login details.</td>
					</tr>
					<?php 	}
					elseif ($err == 'sess') { ?>
					<tr valign="middle">
					  <td align="right">&nbsp;</td>
					  <td align="left" class="error">Session timed out.  Please login again.</td>
					</tr>
					<?php 	}
					elseif ($err !== '') { ?>
					<tr valign="middle">
					  <td align="right">&nbsp;</td>
					  <td align="left" class="error"><?php echo $err ?></td>
					</tr>
					<?php 	} ?>
					<tr valign="middle">
					  <td align="right">Email</td>
					  <td align="left"><input tabindex="1" type="text" maxlength="100" size="30" name="email" tabindex="1" value="<?php echo htmlspecialchars($email)?>"></td>
					</tr>
					<tr valign="middle">
					  <td align="right">Password</td>
					  <td align="left"><input tabindex="2" type="password" maxlength="100" size="30" name="password" tabindex="2" value=""></td>
					</tr>
					<tr valign="middle">
					  <td align="right">&nbsp;</td>
					  <td align="left"><input type="submit" name="Log On" value="Log On" tabindex="3" class="mhsubmit"></td>
					</tr>
					<tr valign="middle">
					  <td align="right">&nbsp;</td>
					  <td align="left">If you have forgotten your password, please <a tabindex="4" href="forgotpassword.php"title="Forgotten Password" tabindex="5">click here</a></td>
					</tr>
					<tr valign="middle">
					  <td align="right">&nbsp;</td>
					  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="20"></td>
					</tr>
				 </table>

				 </p>
			  </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <?include 'footer.php';?>
</form>
</body>
</html>
