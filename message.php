<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";
$msgid = trim($_POST['msgid']);

if (trim($msgid) == '') {
  $msgid = trim($_GET['msgid']);
}


if ($msgid !== '') {

  $query = sprintf( "select fm.displayname, fm.email, m.id as message_id, m.createdate, m.subject, m.message from message m left outer join familymember fm on m.familymember_id=fm.id where m.id=%s" ,
          $msgid);
  $message = mhexecquery($query, $err);

  $query = sprintf( "select fm.displayname from messagefamilymember m inner join familymember fm on m.familymember_id=fm.id where m.message_id=%s" ,
          $msgid);
  $recipients = mhexecquery($query, $err);

  $row = mysql_fetch_assoc($message);


  $query = 'select id from familymember where email=' . sqlsafe(getsessionemail($sessionid));
  $fmids = mhexecquery($query, $err);

  $fmidrow = mysql_fetch_assoc($fmids);
  $query = sprintf( "update messagefamilymember set messagefamilymember.read=1 where message_id=%s and familymember_id=%s" ,
        $msgid,
        $fmidrow['id']
        );
  mhexecquery($query, $err);


}
else {
 $err = 'No message id provided';

}

?>
<html>
<head>
  <title>Malthouse Cottage - Message</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhvalidate.js"></script>
  <script>
  <!--
  //-->
  </script>

</head>

<body>
<form name="mainform" action="messages.php" method="post">
<input name="sess" value="<? echo htmlspecialchars($sessionid) ?>" type="hidden">
<?include 'header.php';?>
  <table width="800" cellpadding="0" cellspacing="0" border="0">
     <tr valign="top">
	   <td width="600" align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
	   <td width="200" class="mhrightsection"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>

	 </tr>
    <tr valign="top">
      <td>
        <table width="600" cellpadding="0" cellspacing="0" border="0">
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" ><h2><? echo htmlspecialchars($row['subject'])?></h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>Message Details</b></td>
                   <td align="right" class="listboxheadingright">&nbsp;
                   </td>
                 </tr>
               </table>
            </td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="560" align="center" class="listbox">
              <table width="90%" cellpadding="2" cellspacing="0" border="0">
                <tr valign="top">
                  <td colspan="3" align="left"><img src="image/spacer.gif" border="0" width="1" height="20" ></td>
                </tr>
                <tr valign="top">
                  <td align="center">
					  <table width="100%" cellpadding="5" cellspacing="0" border="0">
		<?
		if ($err !== '') {
		?>
						<tr valign="top">
						  <td align="left" colspan="3" class="error"><? echo $err ?></td>
						</tr>
		<?
		}?>


						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
		<?if ($msgid!='') { ?>
						<tr valign="top">
						  <td align="left" width="150"><b>Sent By:</b></td>
						  <td align="left" width="400"><? echo htmlspecialchars($row['displayname'])?></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Date Created:</b></td>
						  <td align="left" width="400"><? echo displaytime($row['createdate'])?></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
		  <?if ($row['email'] == getsessionemail($sessionid)) {?>
						<tr valign="top">
						  <td align="left" width="150"><b>Recipients:</b></td>
						  <td align="left" width="400">
						  <?
						  $rowcount = 0;
						  $comma = "";
						  while ( $recipientsrow = mysql_fetch_assoc($recipients)) {

							 if ($rowcount >0) {
							   $comma = ", ";
							 }
							 echo $comma . htmlspecialchars($recipientsrow['displayname']);
							 $rowcount .= 1;
						  }?>
						  </td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
		  <?}?>

		<?}?>
					  </table>
                <tr valign="top">
                  <td colspan="3" align="left">
					  <table width="100%" cellpadding="5" cellspacing="0" border="0">
						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Message:</b></td>
						  <td align="left" width="450"><? echo str_replace("\n", "<br>", htmlspecialchars($row['message']))?></td>
						</tr>
						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>&nbsp;</b></td>
						  <td align="left"><? createinput_submit("Back to Messages", true, "")?></td>
						</tr>
						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
					  </table>
                  </td>
                </tr>
                  </td>
                </tr>
                <tr valign="top">
                  <td colspan="3" align="left"><img src="image/spacer.gif" border="0" width="1" height="20" ></td>
                </tr>
              </table>
            </td>
            <td width="20">&nbsp;</td>
          </tr>
		  <tr valign="middle">
		    <td><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
		  </tr>
          <tr valign="top">
            <td colspan="3" align="left">
              <img src="image/spacer.gif" border="0" width="1" height="20" >
            </td>
          </tr>

        </table>
      </td>
	   <td width="200" align="center" rowspan="5" class="mhrightsection">
		  <table width="100%" cellpadding="2" cellspacing="0" border="0">
			<tr valign="top">
			  <td align="center"><b>Malthouse Status</b></td>
			</tr>
			<tr valign="top">
			  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
			</tr>
			<tr valign="top">
			  <td align="center">
				<?include 'mhinfo.php';?>
			  </td>
			</tr>
			<tr valign="top">
			  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
			</tr>
		  </table>
	   </td>
    </tr>
  </table>
<?include 'footer.php';?>
</form>



<DIV ID="testdiv1" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
</body>
</html>

<?
//All ok, so update the last login date
	mhlogtofile(trim(strtolower(getsessionuser($sessionid))) . $LAST_LOGIN_DATE, date('h:i:sa',time()) . ' on ' . date('d-M-Y',time()));
?>
