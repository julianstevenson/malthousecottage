<?php

include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';


$msgerr = '';
//MESSAGES
$query = sprintf( "select mfm.read, m.subject, mfm.message_id, mfm.id as messagefamilymember_id, m.createdate, fmfrom.displayname as fromname, fmto.displayname as toname from message m inner join messagefamilymember mfm on mfm.message_id=m.id  inner join familymember fmfrom on m.familymember_id=fmfrom.id inner join familymember fmto on mfm.familymember_id=fmto.id where mfm.deleted=0 and fmto.email=%s order by m.createdate desc" ,
          sqlsafe(getsessionemail($sessionid)));
$messages = mhexecquery($query, $msgerr);



//EVENTS
$everr = '';

$query = 'SELECT e.id AS event_id, e.eventday, e.eventmonth, e.eventyear, fm.displayname as displayname, et.annual, et.description as eventtype FROM event e inner join eventfamilymember efm on efm.event_id=e.id inner join eventtype et on e.eventtype_id=et.id inner join familymember fm on fm.id=efm.familymember_id where TO_DAYS( concat(year(curdate( )), "-" , e.eventmonth, "-", eventday)) - TO_DAYS( curdate( ) ) between 0 and 30 order by concat(year(curdate( )), "-" , e.eventmonth, "-", eventday) asc ';
$events = mhexecquery($query, $everr);




function outputmessagerowsent($row) {
  $sbold = "";
  $ebold = "";
  if ($row['read'] == 0) {
    $sbold = "<b>";
    $ebold = "</b>";
  }

  echo '<tr valign="middle" height="30">' . "\n";
  echo '<td align="left" width="100" class="listitem">' . displaymsgtime($row['createdate'], true) . '</td>' . "\n";
  echo '<td align="left" width="150" class="listitem">' . $row['fromname'] . '</td>' . "\n";
  echo '<td align="left" class="listitem"><a href="javascript:viewmessage(' . $row['message_id'] . ')" title="Show message">' . $sbold . htmlspecialchars($row['subject']) . $ebold . '</a></td>' . "\n";
  echo '</tr>' . "\n";


}

?>

<html>
<head>
  <title>Malthouse Cottage - Welcome</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
</head>

<body>
<form name="mainform" action="index.html" method="post" onsubmit="return validatelogin()">
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
            <td width="562" align="left" ><h2>Welcome <?echo getsessionuser($sessionid) ?></h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>Events in the next 30 days</b></td>
                   <td align="right" class="listboxheadingright"><a href="javascript:viewevents()" title="View up and coming family events">View All Events</a></td>
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
                <?if ($everr !== '' ) {?>
                <tr valign="top">
                  <td colspan="3" align="center" class="error"><? echo $everr ?></td>
                </tr>
                <?}
                else {
		          $rowcount = 0;
		          $eventid = -1;
		          $eventname = "";
                  $numevents = mysql_num_rows($events);
                  $eventdate = '';
                  $eventtype = '';
                  while ($row = mysql_fetch_assoc($events)) {
		            $rowcount .= 1;
		            if ($eventid!=-1 && ( $eventid != $row['event_id'] || $rowcount == $numevents )) {
		              echo '<tr valign="middle">' . "\n";
                      if (trim($eventtype) == 'Birthday') {
		                echo '<td align="center" class="listitem"><img src="image/baby.jpg" width="25" height="23" border="0"></td>' . "\n";
                      }
                      else if (trim($eventtype) == 'Wedding Anniversary') {
		                echo '<td align="center" class="listitem"><img src="image/wedding.jpg" width="25" height="22" border="0"></td>' . "\n";
                      }
                      else  {
		                echo '<td align="center" class="listitem"><img src="image/party.jpg" width="25" height="25" border="0"></td>' . "\n";
                      }
		              echo '<td align="left" class="listitem">' . $eventdate . ' - ' . $eventname  . '\'s ' . $eventtype . '</td>' . "\n";

		              echo '</tr>' . "\n";
		            }
		            $eventdate = eventdate($row['eventday'] , $row['eventmonth'], $row['eventyear'], $row['annual']);
		            $eventtype = $row['eventtype'];

		            if ($eventid == $row['event_id']) {
		              $eventname .= ' & ' . $row['displayname'];
		            }
		            else {
		              $eventname = $row['displayname'];
		            }

		            $eventid = $row['event_id'];
                  }
                  //write the last row
                  if ($eventname != '') {
				    echo '<tr valign="middle">' . "\n";
                      if (trim($eventtype) == 'Birthday') {
		                echo '<td align="center" class="listitem"><img src="image/baby.jpg" width="25" height="23" border="0"></td>' . "\n";
                      }
                      else if (trim($eventtype) == 'Wedding Anniversary') {
		                echo '<td align="center" class="listitem"><img src="image/wedding.jpg" width="25" height="22" border="0"></td>' . "\n";
                      }
                      else  {
		                echo '<td align="center" class="listitem"><img src="image/party.jpg" width="25" height="25" border="0"></td>' . "\n";
                      }
				    echo '<td align="left" class="listitem">' . $eventdate . ' - ' . $eventname  . '\'s ' . $eventtype . '</td>' . "\n";
				    echo '</tr>' . "\n";
				  }
                  if ($rowcount == 0) {?>
                <tr valign="top">
                  <td align="left" class="error">No Events </td>
                </tr>
                  <?
                  }
                }
                ?>
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
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>Messages Received</b></td>
                   <td align="right" class="listboxheadingright"><a href="javascript:viewmessages()" title="View all messages or create a new message">View All Messages</a></td>
                 </tr>
               </table>
            </td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="560" align="center" class="listbox">
              <table width="90%" cellpadding="2" cellspacing="0" border="0">
                <tr valign="middle">
                  <td colspan="3" align="left"><img src="image/spacer.gif" border="0" width="1" height="20" ></td>
                </tr>
                <tr valign="middle">
                  <td align="left" width="100" class="listboxheading"><b>Date</b></td>
                  <td align="left" width="150" class="listboxheading"><b>From</b></td>
                  <td align="left" class="listboxheading"><b>Message</b></td>
                </tr>

<? if (!$messages)  { ?>
                <tr valign="middle">
                  <td align="left" colspan="3">You have no messages</td>
                </tr>
<? }
else if ($msgerr != '') {?>
                <tr valign="middle">
                  <td align="left" colspan="3" class="error"><?echo $msgerr?></td>
                </tr>

<?}
else {
		$rowcount = 0;
        while ($row = mysql_fetch_assoc($messages)) {
		  $rowcount .= 1;
          outputmessagerowsent($row);
        }
        if ($rowcount == 0) {?>

                <tr valign="middle">
                  <td align="left" colspan="3" class="error">No messages</td>
                </tr>

<?	    }
}

?>

                <tr valign="top">
                  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="20" ></td>
                </tr>
              </table>
            </td>
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
</body>
</html>
<?
//All ok, so update the last login date
	mhlogtofile(trim(strtolower(getsessionuser($sessionid))) . $LAST_LOGIN_DATE, date('h:i:sa',time()) . ' on ' . date('d-M-Y',time()));
?>