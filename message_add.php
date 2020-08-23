<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";
$msgid = trim($_POST['msgid']);

if (trim($msgid) == '') {
  $msgid = trim($_GET['msgid']);
}

if ($_POST['Create'] == 'Create') {
      //Create the message
      $query = sprintf( 'insert into message (familymember_id, subject, message) select fm.id, %s, %s from familymember fm where email=%s' ,
          sqlsafe($_POST['subject']),
          sqlsafe($_POST['message']),
          sqlsafe(getsessionemail($sessionid))
          );
      mhexecquery($query, $err);
  	  if ($err == '') { //All ok so assign message to people
         $query = 'select max(id) as message_id from message';
         $message = mhexecquery($query, $err);
         if ($err == '') {
           $row = mysql_fetch_assoc($message);
           $query = '';
           $recipients = $_POST['family_id'];
      	   foreach ($recipients as $family_id) {
              $query = sprintf('insert into messagefamilymember (message_id, familymember_id) values (%s, %s) ', $row['message_id'], $family_id);
              mhexecquery($query, $err);
              if ($err == '') {
                //alert the recipients
                $query = 'select email from familymember where id=' . $family_id;
                $mailrecipient = mhexecquery($query, $err);
                if ($err=='' ) {
                  $rowrecipient = mysql_fetch_assoc($mailrecipient);
                  if ($rowrecipient) {
                   sendemailmsg("New Message from Malthouse Cottage", "You have a new message at www.malthousecottage.com. Please login to check your messages.", $rowrecipient['email']);
                  }
                }
              }
           }
           if ($err == '') {
  	  		  Header("Location: messages.php?msg=" . urlencode("Message Created Successfuly") . "&sess=" . urlencode($sessionid)) ;
  	  	   }

         }



      }

}
elseif ($msgid !== '') {

  $query = sprintf( "select fm.displayname, fm.email, msg.id as message_id, m.createdate, m.subject, m.message from message m left outer join familymember fm on m.familymember_id=fm.id where m.id=%s" ,
          $msgid);
  $message = mhexecquery($query, $err);

}

function getformvalue($fldname, $row, $fromform) {
  if ($fromform) {
    return htmlspecialchars($_POST[$fldname]);
  }
  else {
    return htmlspecialchars($row[$fldname]);
  }


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
  function validateaddfamily( ) {
   if(document.all) {
     if (document.all['family_id[]'].selectedIndex <0) {
       alert('Please select one or more recipients');
       return false;
     }
   }



    if (!checkmandatoryfield(document.mainform.subject, 'Subject')) {
      return false;
    }
    if (!checkmandatoryfield(document.mainform.message, 'Message')) {
      return false;
    }

    return true;
  }
  //-->
  </script>

</head>

<body onload="document.mainform.subject.focus()">
<form name="mainform" action="message_add.php" method="post" onsubmit="return validateaddfamily()">
<input name="sess" value="<? echo htmlspecialchars($sessionid) ?>" type="hidden">
<input name="msgid" value="<? echo htmlspecialchars($msgid) ?>" type="hidden">
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
            <td width="562" align="left" ><h2>New Message</h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>Create a New Message</b></td>
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
						<tr valign="top" colspan="3">
						   <td align="left" colspan="3"><b>Enter a message and select the recipients, then click the 'Create' button.  The message will be displayed to the recipients when they next log in to Malthouse Cottage.(<span class="error">*Mandatory fields</span>).</b>
						   </td>
						</tr>
		<?
		if ($err !== '') {
		?>
						<tr valign="top">
						  <td align="left" colspan="3" class="error"><? echo $err ?></td>
						</tr>
		<?
		}
		if (!$message && $msgid!='')  { ?>
						<tr valign="middle">
						  <td align="left" colspan="6">Invalid message id</td>
						</tr>
		<?}
		else {
			if ($message) {
			  $row = mysql_fetch_assoc($message);
			}
		}
		?>


						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
		<?if ($msgid!='') { ?>
						<tr valign="top">
						  <td align="left" width="150"><b>Created By:</b></td>
						  <td align="left" width="400"><? echo getformvalue('displayname', $row, !$message)?><input type="hidden" value="<? echo getformvalue('displayname', $row, !$message)?>" name="displayname"></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Date Created:</b></td>
						  <td align="left" width="400"><? echo displaytime(getformvalue('createdate', $row, !$message))?><input type="hidden" value="<? echo getformvalue('createdate', $row, !$message)?>" name="createdate"></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
		<?}?>
						<tr valign="top">
						  <td align="left" width="150"><b>Recipients:</b></td>
						  <td align="left" width="400"><? createinput_select_recipient($_POST['family_id'])?>&nbsp;Hold the &lt;Shift&gt; or &lt;Control&gt; key to select multipe.<span class="error">*</span></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Subject:</b></td>
						  <td align="left" width="400"><? createinput_text("subject", $_POST['subject'], "30", "50")?>&nbsp;<span class="error">*</span></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Message:</b></td>
						  <td align="left" width="400"><? createinput_textarea("message", $_POST['message'], "10", "40")?>&nbsp;<span class="error">*</span></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150">&nbsp;</td>
						  <td align="left" width="400"><? createinput_submit("Create"); ?>&nbsp;<? createinput_submit("Cancel", false, "viewmessages()")?></td>
						  <td align="left" width="50">&nbsp;</td>
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
