<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";

$resid = trim($_POST['resid']);

if (trim($resid) == '') {
  $resid = trim($_GET['id']);
}

$locationid = $_POST['locationid'];
$approved = $_POST['approved'];

if ($locationid == '') {
  $locationid = $_GET['locationid'];
}

if ($approved == '') {
  $approved = $_GET['approved'];
}

if ($_POST['Approve'] == 'Approve') {
      $query = sprintf( 'update reservation set approved=%s, approvecomments=%s, approverejectdate=%s, approverejectby=(select id from familymember where email=%s) where id=%s' ,
          sqlsafe('1'),
          sqlsafe($_POST['approvecomments']),
          sqlsafe(date("Y-m-d H:i:s")),
          sqlsafe(getsessionemail($sessionid)),
          $resid
          );
  	  if (mhexecquery($query, $err)) {

           // sendemailmsg("New Message from Malthouse Cottage", "You have a new message at www.malthousecottage.com. Please login to check your messages.", $rowrecipient['email'])
           $message = "Your booking request for " . $_POST['location'] . ' between ' . $_POST['arrivedate'] . ' and ' . $_POST['departdate'] . ' has been approved.  Message below:\r' . $_POST['approvecomments'];

           if ($_POST['emailreq']=='1') {
               sendapprovalmessage("Malthouse Cottage Booking Request Approved", $message , getsessionemail($sessionid), $_POST['requesterid']);
           }

            Header("Location: reservations.php?locationid=". $locationid ."&approved=". $approved ."&msg=" . urlencode("Reservation has been approved") . "&sess=" . urlencode($sessionid)) ;
      }
}
if ($_POST['Reject'] == 'Reject') {
      $query = sprintf( 'update reservation set approved=%s, approvecomments=%s, approverejectdate=%s, approverejectby=(select id from familymember where email=%s) where id=%s' ,
          sqlsafe('0'),
          sqlsafe($_POST['approvecomments']),
          sqlsafe(date("Y-m-d H:i:s")),
          sqlsafe(getsessionemail($sessionid)),
          $resid
          );
  	  if (mhexecquery($query, $err)) {
  	  		Header("Location: reservations.php?locationid=". $locationid ."&approved=". $approved ."&msg=" . urlencode("Reservation has been rejected") . "&sess=" . urlencode($sessionid)) ;
      }
}
elseif ($resid !== '') {  //Get the reservations

  $query = sprintf( "select fm.displayname, fm.admin, fm.email, r.approvecomments, r.approved, r.id as reservation_id, r.location, r.createdate, r.fullname, r.telephone, r.arrivedate, r.departdate, r.createdate, r.comments, r.familymember_id from reservation r left outer join familymember fm on r.familymember_id=fm.id where r.id=%s" ,
          $resid);
  $reservation = mhexecquery($query, $err);

}

function getformvalue($fldname, $row, $fromform) {
  if ($fromform) {
    return htmlspecialchars($_POST[$fldname]);
  }
  else {
    return htmlspecialchars($row[$fldname]);
  }


}
function approvalstatus($approved) {

 $returnvalue = "Pending Approval";
    if ($approved==true) {
        $returnvalue = '<table cellpadding="0" cellspacing="0" border="0"><tr valign="middle"><td><img align="center" src="image/greentick.png" width="14" height="14" border="0"></td><td>&nbsp;Approved</td></tr></table>' ;
    }
    elseif (is_null($approved))  {
       $returnvalue = '<table cellpadding="0" cellspacing="0" border="0"><tr valign="middle"><td><img align="center" src="image/pending.png" width="16" height="16" border="0"></td><td>&nbsp;Pending Approval</td></tr></table>' ;
     }

    elseif ($approved==false) {
        $returnvalue = '<table cellpadding="0" cellspacing="0" border="0"><tr valign="middle"><td><img align="center" src="image/redcross.png" width="12" height="12" border="0"></td><td>&nbsp;Rejected</td></tr></table>' ;
    }
    return $returnvalue;


}
function sendapprovalmessage($subject, $messagebody, $fromemail, $recipientid) {
    $err='';
      $query = sprintf( 'insert into message (familymember_id, subject, message) select fm.id, %s, %s from familymember fm where email=%s' ,
          sqlsafe($subject),
          sqlsafe($messagebody),
          sqlsafe($fromemail )
          );
      mhexecquery($query, $err);
  	  if ($err == '') { //All ok so assign message to people
         $query = 'select max(id) as message_id from message';
         $message = mhexecquery($query, $err);
         if ($err == '') {
           $row = mysql_fetch_assoc($message);
           $query = '';
              $query = sprintf('insert into messagefamilymember (message_id, familymember_id) values (%s, %s) ', $row['message_id'], $recipientid);
              
              mhexecquery($query, $err);
              if ($err == '') {
                //alert the recipients
                $query = 'select email from familymember where id=' . $family_id;
                $mailrecipient = mhexecquery($query, $err);
                if ($err=='' ) {
                  $rowrecipient = mysql_fetch_assoc($mailrecipient);
                  if ($rowrecipient) {
                   sendemailmsg("Malthouse Cottage booking request update", "Your www.malthousecottage.com booking request has been processed. Please login to check your messages.", $rowrecipient['email']);
                  }
                }
              }

         }



      }


}

?>
<html>
<head>
  <title>Malthouse Cottage - Reservations</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhvalidate.js"></script>
  <script src="script/dateselector.js"></script>
  <script>
  <!--
  function validateaddreservation( ) {

    if (!checkmandatoryfield(document.mainform.fullname, 'Full Name')) {
      return false;
    }

    if (!checkmandatoryfield(document.mainform.arrivedate, 'Arrival Date')) {
      return false;
    }

    if (!checkmandatoryfield(document.mainform.departdate, 'Departure Date')) {
      return false;
    }

    if (!checkdate(document.mainform.arrivedate, 'Arrival Date')) {
      return false;
    }

    if (!checkdate(document.mainform.departdate, 'Departure Date')) {
      return false;
    }
	if (!CheckDateDropDownStartEnd(document.mainform.arrivedate, document.mainform.departdate, 'Arrival Date', 'Departure Date')) {
	  return false;
	}
    return true;
  }
  //-->
  </script>

</head>

<body>
<form name="mainform" action="reservation_approve.php" method="post" onsubmit="return true">
<input name="sess" value="<? echo htmlspecialchars($sessionid) ?>" type="hidden">
<input name="resid" value="<? echo htmlspecialchars($resid) ?>" type="hidden">
<input name="locationid" value="<? echo htmlspecialchars($locationid) ?>" type="hidden">
<input name="approved" value="<? echo htmlspecialchars($approved) ?>" type="hidden">

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
            <td width="562" align="left" ><h2>Review Booking Request</h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>Approve a booking request</b></td>
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
                <?if ($everr !== '' ) {?>
                <tr valign="top">
                  <td colspan="3" align="center" class="error"><? echo $everr ?></td>
                </tr>
                <?}?>
                <tr valign="top">
                  <td align="center">
              <table width="100%" cellpadding="5" cellspacing="0" border="0">
                <tr valign="top" colspan="3">
                   <td align="left" colspan="3"><b>Booking request details below.  Click Approve or Reject, or click Cancel to return to the bookings list.</b>
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
if (!$reservation && $resid!='')  { ?>
                <tr valign="middle">
                  <td align="left" colspan="6">Invalid reservation id</td>
                </tr>
<?}
else {
    if ($reservation) {
	  $row = mysql_fetch_assoc($reservation);
	}
}
?>


                <tr valign="top">
                  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>&nbsp;</b></td>
                  <td align="left" width="250">
                    <h3>
                    <?if (getformvalue('location', $row, !$reservation )=='1') echo 'Malthouse Cottage'?>
                    <?if (getformvalue('location', $row, !$reservation )=='2') echo 'Flayosc'?>
                    </h3>
                    </td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Approval Status:</b></td>
                  <td align="left" width="400"><? echo approvalstatus( $row['approved'])?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Requested By:</b></td>
                  <td align="left" width="400"><? echo getformvalue('displayname', $row, !$reservation)?><input type="hidden" value="<? echo getformvalue('displayname', $row, !$reservation)?>" name="displayname"></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Date Created:</b></td>
                  <td align="left" width="400"><? echo displaytime(getformvalue('createdate', $row, !$reservation))?><input type="hidden" value="<? echo getformvalue('createdate', $row, !$reservation)?>" name="createdate"></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Name of Guest:</b></td>
                  <td align="left" width="250"><? echo getformvalue('fullname', $row, !$reservation )?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Contact Telephone:</b></td>
                  <td align="left" width="400"><? echo getformvalue('telephone', $row, !$reservation)?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Arrival Date:</b></td>
                  <td align="left" width="400"><? echo displaydate(getformvalue('arrivedate', $row, !$reservation ))?>&nbsp;
                     </td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Departure Date:</b></td>
                  <td align="left" width="400"><? echo displaydate(getformvalue('departdate', $row, !$reservation ))?>&nbsp;
                  </td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Comments:</b></td>
                  <td align="left" width="400"><? echo htmlspecialchars(getformvalue('comments', $row, !$reservation ))?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Response:</b></td>
                  <td align="left" width="400"><? createinput_textarea("approvecomments", getformvalue('approvecomments', $row, !$reservation ), "4", "23")?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="middle">
                  <td align="left" width="150"><b>&nbsp;</b></td>
                  <td align="left" width="400"><input style="vertical-align: middle;" type="checkbox" checked name="emailreq" id="emailreq" value="1"><label for="emailreq">Send this message to requester (<? echo getformvalue('displayname', $row, !$reservation)?>)</label></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150">&nbsp;</td>
                  <td align="left" width="400"><? createinput_submit("Approve"); ?>&nbsp;<? createinput_submit("Reject"); ?>&nbsp;<? createinput_submit("Cancel", false, "viewbookings(document.mainform.locationid.value, document.mainform.approved.value)")?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
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
<input name="arrivedate" value="<? echo displaydate(getformvalue('arrivedate', $row, !$reservation )) ?>" type="hidden">
<input name="departdate" value="<? echo displaydate(getformvalue('departdate', $row, !$reservation )) ?>" type="hidden">
<input name="location" value="<? echo ($row['location']=="2")?"Flayosc" : "Malthouse Cottage" ?>" type="hidden">
<input name="requesterid" value="<? echo htmlspecialchars($row['familymember_id']) ?>" type="hidden">
</form>



<DIV ID="testdiv1" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
</body>
</html>

<?
//All ok, so update the last login date
	mhlogtofile(trim(strtolower(getsessionuser($sessionid))) . $LAST_LOGIN_DATE, date('h:i:sa',time()) . ' on ' . date('d-M-Y',time()));
?>

