<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";

$resid = trim($_POST['resid']);

if (trim($resid) == '') {
  $resid = trim($_GET['resid']);
}

$locationid = $_POST['locationid'];
$approved = $_POST['approved'];

if ($locationid == '') {
  $locationid = $_GET['locationid'];
}

if ($approved == '') {
  $approved = $_GET['approved'];
}

if ($_POST['Request'] == 'Request' || $_POST['Book'] == 'Book') {

      $approved = mysql_escape_string("NULL") ;
      if ($_POST['Book'] == 'Book') {
          $approved = sqlsafe(1);
      }
      //check for booking at this time
      $query = sprintf( 'select * from reservation where location=%s and ((datediff(%s, arrivedate)>=0 and datediff(%s, departdate) <= 0) or (datediff(%s, arrivedate)>=0 and datediff(%s, departdate) <= 0) or (datediff(%s, arrivedate)<0 and datediff(%s, departdate) > 0))',
          sqlsafe($_POST['location']),
          sqlsafe(safedate($_POST['arrivedate'])),
          sqlsafe(safedate($_POST['arrivedate'])),
          sqlsafe(safedate($_POST['departdate'])),
          sqlsafe(safedate($_POST['departdate'])),
          sqlsafe(safedate($_POST['arrivedate'])),
          sqlsafe(safedate($_POST['departdate']))
	);
      $bookingsalready = mhexecquery($query, $err);
	 while ($bookingalready = mysql_fetch_assoc($bookingsalready)) {
	$err .= htmlspecialchars($bookingalready['fullname']) . ' already have a booking between ' . 		displaymsgtime($bookingalready['arrivedate'], true) . ' and ' . displaymsgtime($bookingalready['departdate'], true) . '<br>';
      }


if ($err == '') {
      $query = sprintf( 'insert into reservation (familymember_id, fullname, telephone, arrivedate, departdate, comments, location, approved) select fm.id, %s, %s, %s, %s, %s, %s, %s from familymember fm where email=%s' ,
          sqlsafe($_POST['fullname']),
          sqlsafe($_POST['telephone']),
          sqlsafe(safedate($_POST['arrivedate'])),
          sqlsafe(safedate($_POST['departdate'])),
          sqlsafe($_POST['comments']),
          sqlsafe($_POST['location']),
          $approved,
          sqlsafe(getsessionemail($sessionid))
          );
  	  if (mhexecquery($query, $err)) {

        //Email those who have requested notification
         $query = 'select email from familymember where bookingalerts=1';
         $recipients = mhexecquery($query, $err);
         if ($_POST['Request'] == 'Request') {
             if ($err == '') {
               while ($recipient = mysql_fetch_assoc($recipients)) {
                    //alert the recipients
                       sendemailmsg("New Booking Request for " . $_POST['location'], $_POST['fullname'] . " has requested a booking for " . $_POST['location'] . ". Log onto http://www.malthousecottage.com for more details", $recipient['email']);
               }
               if ($err == '') {
                  Header("Location: messages.php?msg=" . urlencode("Message Created Successfuly") . "&sess=" . urlencode($sessionid)) ;
               }

             }
         }


        Header("Location: reservations.php?msg=" . urlencode("Reservation Added Successfuly"). '&fromdate=' . urlencode(safedate($_POST['arrivedate'])). '&todate=' . urlencode(safedate($_POST['departdate'])). '&selfromdate=' . urlencode($_POST['arrivedate']). '&seltodate=' . urlencode($_POST['departdate']) . "&sess=" . urlencode($sessionid)) ;

      }
}
}
elseif ($_POST['Update'] == 'Update') {
      //check for booking at this time
      $query = sprintf( 'select * from reservation where id<>%s and location=%s and ((datediff(%s, arrivedate)>=0 and datediff(%s, departdate) <= 0) or (datediff(%s, arrivedate)>=0 and datediff(%s, departdate) <= 0) or (datediff(%s, arrivedate)<0 and datediff(%s, departdate) > 0))',
          $resid,
          sqlsafe($_POST['location']),
          sqlsafe(safedate($_POST['arrivedate'])),
          sqlsafe(safedate($_POST['arrivedate'])),
          sqlsafe(safedate($_POST['departdate'])),
          sqlsafe(safedate($_POST['departdate'])),
          sqlsafe(safedate($_POST['arrivedate'])),
          sqlsafe(safedate($_POST['departdate']))
	);
      $bookingsalready = mhexecquery($query, $err);
	 while ($bookingalready = mysql_fetch_assoc($bookingsalready)) {
	$err .= htmlspecialchars($bookingalready['fullname']) . ' already have a booking between ' . 		displaymsgtime($bookingalready['arrivedate'], true) . ' and ' . displaymsgtime($bookingalready['departdate'], true) . '<br>';
      }


if ($err == '') {
      $query = sprintf( 'update reservation set fullname=%s, telephone=%s, arrivedate=%s, departdate=%s, comments=%s, location=%s where id=%s' ,
          sqlsafe($_POST['fullname']),
          sqlsafe($_POST['telephone']),
          sqlsafe(safedate($_POST['arrivedate'])),
          sqlsafe(safedate($_POST['departdate'])),
          sqlsafe($_POST['comments']),
          sqlsafe($_POST['location']),
          $resid
          );
  	  if (mhexecquery($query, $err)) {
  	  		Header("Location: reservations.php?msg=" . urlencode("Reservation Updated Successfuly"). '&fromdate=' . urlencode(safedate($_POST['arrivedate'])). '&todate=' . urlencode(safedate($_POST['departdate'])). '&selfromdate=' . urlencode($_POST['arrivedate']). '&seltodate=' . urlencode($_POST['departdate']) . "&sess=" . urlencode($sessionid)) ;
      }
}
}
elseif ($resid !== '') {  //Get the reservations

  $query = sprintf( "select fm.displayname, fm.admin, fm.email, r.id as reservation_id, r.location, r.createdate, r.fullname, r.telephone, r.arrivedate, r.departdate, r.createdate, r.comments from reservation r left outer join familymember fm on r.familymember_id=fm.id where r.id=%s" ,
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

<body onload="document.mainform.fullname.focus()">
<form name="mainform" action="reservation_add.php" method="post" onsubmit="return validateaddreservation()">
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
            <td width="562" align="left" ><h2><? if ($resid=='') echo "New Booking"; else echo "Update Booking";?></h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b><? if ($resid=='') echo "Create a new booking"; else echo "Update Booking";?></b></td>
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
                   <td align="left" colspan="3"><b>Enter the reservation details and click the 'Book' button.  This will complete your reservation request and notify the management (<span class="error">*Mandatory fields</span>).</b>
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
if (!$reservation && $resid!='' && $err=='')  { ?>
                <tr valign="middle">
                  <td align="left" colspan="6">Invalid reservation id (<? echo $resid ?>)</td>
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
<?if ($resid!='') { ?>
                <tr valign="top">
                  <td align="left" width="150"><b>Booked By:</b></td>
                  <td align="left" width="400"><? echo getformvalue('displayname', $row, !$reservation)?><input type="hidden" value="<? echo getformvalue('displayname', $row, !$reservation)?>" name="displayname"></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Date Created:</b></td>
                  <td align="left" width="400"><? echo displaytime(getformvalue('createdate', $row, !$reservation))?><input type="hidden" value="<? echo getformvalue('createdate', $row, !$reservation)?>" name="createdate"></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
<?}?>
                <tr valign="top">
                  <td align="left" width="150"><b>Property:</b></td>
                  <td align="left" width="250"><select name="location">

                    <option value="2" <?if (getformvalue('location', $row, !$reservation )=='2') echo 'selected'?>>Flayosc</option>
                    </select>
                    &nbsp;<span class="error">*</span></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Name of Guest:</b></td>
                  <td align="left" width="250"><? createinput_text("fullname", getformvalue('fullname', $row, !$reservation ), "30", "200")?>&nbsp;<span class="error">*</span></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Contact Telephone:</b></td>
                  <td align="left" width="400"><? createinput_text("telephone", getformvalue('telephone', $row, !$reservation), "30", "20")?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Arrival Date:</b></td>
                  <td align="left" width="400"><? createinput_text("arrivedate", inputdate(getformvalue('arrivedate', $row, !$reservation )), "10", "11")?>&nbsp;
                     <SCRIPT LANGUAGE="JavaScript" ID="jscalarrive">
                       var jscalarrive = new CalendarPopup("testdiv1");
                       jscalarrive.showNavigationDropdowns();
                     </SCRIPT>
                     <A HREF="#" onClick="jscalarrive.select(document.forms[0].arrivedate,'anchorarrivedate','dd/MM/yyyy'); return false;" TITLE="Select an arrival date" NAME="anchorarrivedate" ID="anchorarrivedate"><img src="image/calendar.jpg" border="0"></A>
                     &nbsp;<span class="error">*</span></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Departure Date:</b></td>
                  <td align="left" width="400"><? createinput_text("departdate", inputdate(getformvalue('departdate', $row, !$reservation )), "10", "11")?>&nbsp;
                     <SCRIPT LANGUAGE="JavaScript" ID="jscaldepart">
                       var jscaldepart = new CalendarPopup("testdiv1");
                       jscaldepart.showNavigationDropdowns();
                     </SCRIPT>
                     <A HREF="#" onClick="jscaldepart.select(document.forms[0].departdate,'anchordepartdate','dd/MM/yyyy'); return false;" TITLE="Select a departure date" NAME="anchordepartdate" ID="anchordepartdate"><img src="image/calendar.jpg" border="0"></A>
                     &nbsp;<span class="error">*</span></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Comments:</b></td>
                  <td align="left" width="400"><? createinput_textarea("comments", getformvalue('comments', $row, !$reservation ), "4", "23")?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150">&nbsp;</td>
<?if (getsessionadmin($sessionid)==1) { ?>
                  <td align="left" width="400"><? if ($resid=='') { createinput_submit("Request");?>&nbsp;<? createinput_submit("Book"); } else createinput_submit("Update");?>&nbsp;<? createinput_submit("Cancel", false, "viewbookings(document.mainform.locationid.value, document.mainform.approved.value)")?></td>
<? } else { ?>
                  <td align="left" width="400"><? if ($resid=='') createinput_submit("Request"); else createinput_submit("Update");?>&nbsp;<? createinput_submit("Cancel", false, "viewbookings(document.mainform.locationid.value, document.mainform.approved.value)")?></td>

<?}?>
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
</form>



<DIV ID="testdiv1" STYLE="position:absolute;visibility:hidden;background-color:white;layer-background-color:white;"></DIV>
</body>
</html>

<?
//All ok, so update the last login date
	mhlogtofile(trim(strtolower(getsessionuser($sessionid))) . $LAST_LOGIN_DATE, date('h:i:sa',time()) . ' on ' . date('d-M-Y',time()));
?>

