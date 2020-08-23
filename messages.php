<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";
$msg = $_GET['msg'];


$fromdate = $_POST['fromdate'];
$todate = $_POST['todate'];
$months = $_POST['months'];
$selfromdate = $_POST['selfromdate'];
$seltodate = $_POST['seltodate'];

if ($_POST['act'] == 'del') {
  if ($_POST['rowid'] == '') {
    $err = "No row id supplied";
  }
  else {
    $query = sprintf( "update messagefamilymember set deleted=1 where id = %s" ,
          $_POST['rowid']);

    if (!mhexecquery($query, $err)) {
       //
    }
    else {
      $msg = "Message deleted";
    }
  }

}
elseif ($_POST['act'] == 'delperm') {
  if ($_POST['rowid'] == '') {
    $err = "No row id supplied";
  }
  else {
    $query = sprintf( "delete from messagefamilymember where message_id = %s" ,
          $_POST['rowid']);

    if (!mhexecquery($query, $err)) {
       //
    }
    else {
      $msg = "Message deleted";
    }
  }

}


$query = sprintf( "select distinct  m.subject,  m.id as message_id, m.id as messagefamilymember_id, m.createdate from message m inner join familymember fmfrom on m.familymember_id=fmfrom.id inner join messagefamilymember mfm on mfm.message_id=m.id  where fmfrom.email=%s order by m.createdate desc" ,
          sqlsafe(getsessionemail($sessionid)));
$messagesfrom = mhexecquery($query, $err);

$query = sprintf( "select mfm.read, m.subject, fmfrom.displayname as fmfromname, mfm.message_id, mfm.id as messagefamilymember_id, m.createdate, fmto.displayname as toname from message m inner join messagefamilymember mfm on mfm.message_id=m.id inner join familymember fmto on mfm.familymember_id=fmto.id inner join familymember fmfrom on fmfrom.id=m.familymember_id where  mfm.deleted=0 and fmto.email=%s order by m.createdate desc" ,
          sqlsafe(getsessionemail($sessionid)));
$messagesto = mhexecquery($query, $err);

function outputmessagerowreceived($row, $highlightunread) {
  $sbold = "";
  $ebold = "";
  if ($row['read'] == 0 && $highlightunread) {
    $sbold = "<b>";
    $ebold = "</b>";
  }
  echo '<tr valign="middle">' . "\n";
  echo '<td align="left" class="listitem" width="250">' . $sbold . displaymsgtime($row['createdate']) . $ebold  . '&nbsp;</td>' . "\n";
  echo '<td align="left" class="listitem"  width="250">' . $sbold  . '<a href="javascript:viewmessage(' . $row['message_id'] . ')" title="Read message">' . $row['subject'] . $ebold  . '&nbsp;</td>' . "\n";
  echo '<td align="left" class="listitem"  width="100">' . $sbold  . htmlspecialchars($row['fmfromname']) . $ebold  . '&nbsp;</td>' . "\n";
  echo '<td align="center" class="listitem" ><a href="javascript:softdeletemessage(' . $row['messagefamilymember_id'] . ')" title="Remove message"><img src="image/remove.gif" width="11" height="11" border="0"></a></td>' . "\n";

  echo '</tr>' . "\n";


}

function outputmessagerowsent($row, $highlightunread) {
  $sbold = "";
  $ebold = "";
  if ($row['read'] == 0 && $highlightunread) {
    $sbold = "<b>";
    $ebold = "</b>";
  }
  echo '<tr valign="middle">' . "\n";
  echo '<td align="left" class="listitem" width="250">' . $sbold . displaymsgtime($row['createdate']) . $ebold  . '&nbsp;</td>' . "\n";
  echo '<td align="left" class="listitem"  colspan="2">' . $sbold  . '<a href="javascript:viewmessage(' . $row['message_id'] . ')" title="Read message">' . htmlspecialchars($row['subject']) . $ebold  . '&nbsp;</td>' . "\n";
  echo '<td align="center" class="listitem" ><a href="javascript:harddeletemessage(' . $row['messagefamilymember_id'] . ')" title="Remove message"><img src="image/remove.gif" width="11" height="11" border="0"></a></td>' . "\n";

  echo '</tr>' . "\n";


}
?>


<html>
<head>
  <title>Malthouse Cottage - Messages</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhdate.js"></script>
  <script src="script/mhvalidate.js"></script>
  <script src="script/dateselector.js"></script>
  <script>
  <!--

  function softdeletemessage(id) {
	if (confirm('Are you sure you would like to delete this message?') ) {
	  document.mainform.rowid.value = id;
	  document.mainform.act.value = 'del';
	  document.mainform.submit();
	}
  }

  function harddeletemessage(id) {
	if (confirm('Deleting this message will remove the message from all user\'s message box. Continue?') ) {
	  document.mainform.rowid.value = id;
	  document.mainform.act.value = 'delperm';
	  document.mainform.submit();
	}
  }

//-->
  </script>

</head>
<body>
<form name="mainform" action="messages.php" method="post">
<input name="sess" value="<? echo htmlspecialchars($sessionid) ?>" type="hidden">
<input name="rowid" value="" type="hidden">
<input name="act" value="" type="hidden">
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
            <td width="562" align="left" ><h2>Messages</h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>Messages</b></td>
                   <td align="right" class="listboxheadingright"><a href="javascript:createmessage()" title="Create message">Create a Message</a>&nbsp;

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
			<?
			if ($err !== '') {
			?>
				</tr>
				  <td align="left" class="error"><? echo $err?></td>
				</tr>
			<?}?>
			<?
			if ($msg !== '') {
			?>
				</tr>
				  <td align="left" class="message"><? echo $msg?></td>
				</tr>
			<?}?>
                <?if ($everr !== '' ) {?>
                <tr valign="top">
                  <td colspan="3" align="center" class="error"><? echo $everr ?></td>
                </tr>
                <?}?>
                <tr valign="top">
                  <td align="left">
					  <table width="100%" cellpadding="5" cellspacing="0" border="0">
						<tr valign="top" height="25">
						   <td align="left" colspan="2">
							<h3>Messages you have received</h3>
						   </td>
						   <td align="right" colspan="2">
							&nbsp;
						   </td>
						</tr>
						<tr valign="top">
						   <td align="left" colspan="2">
							(Bold indicates message has not been read)
						   </td>
						</tr>
						<tr valign="middle">
						  <td align="left" width="250" class="listboxheading"><b>Message Date</b></td>
						  <td align="left" width="250" class="listboxheading"><b>Subject</b></td>
						  <td align="left" width="150" class="listboxheading"><b>From</b></td>
						  <td align="center" class="listboxheading"><b>Remove</b></td>
						</tr>
		<? if (!$messagesto)  { ?>
						<tr valign="middle">
						  <td align="left" colspan="6">You have received no messages</td>
						</tr>
		<? }
		else if ($err == '') {
				$rowcount = 0;
				while ($row = mysql_fetch_assoc($messagesto)) {
				  $rowcount .= 1;
				  outputmessagerowreceived($row, true);
				}
				if ($rowcount == 0) {?>

						<tr valign="middle">
						  <td align="left" colspan="6" class="error">No messages</td>
						</tr>

		<?	    }
		}

		?>
						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
						<tr valign="top" height="25">
						   <td align="left" colspan="2">
							<h3>Messages you have sent</h3>
						   </td>
						   <td align="right" colspan="2">
							&nbsp;
						   </td>
						</tr>
						<tr valign="middle">
						  <td align="left" width="250" class="listboxheading"><b>Message Date</b></td>
						  <td align="left" width="250" class="listboxheading"><b>Subject</b></td>
						  <td align="left" width="150" class="listboxheading">&nbsp;</td>
						  <td align="center" class="listboxheading"><b>Remove</b></td>
						</tr>
		<? if (!$messagesfrom)  { ?>
						<tr valign="middle">
						  <td align="left" colspan="6">You have sent no messages</td>
						</tr>
		<? }
		else if ($err == '') {
				$rowcount = 0;
				while ($row = mysql_fetch_assoc($messagesfrom)) {
				  $rowcount .= 1;
				  outputmessagerowsent($row, false);
				}
				if ($rowcount == 0) {?>

						<tr valign="middle">
						  <td align="left" colspan="6" class="error">No messages</td>
						</tr>

		<?	    }
		}

		?>
						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
					  </table>
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
