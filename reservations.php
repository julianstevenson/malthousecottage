<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";
$msg = $_GET['msg'];


$locationid = $_POST['locationid'];
$approved = $_POST['approved'];
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
    $query = sprintf( "delete from reservation where id = %s" ,
          $_POST['rowid']);

    if (!mhexecquery($query, $err)) {
       //
    }
    else {
      $msg = "Reservation deleted";
    }
  }

}

if ($locationid == '') {
  $locationid = $_GET['locationid'];
}

if ($approved == '') {
  $approved = $_GET['approved'];
}

if ($fromdate == '') {
  $fromdate = $_GET['fromdate'];
}

if ($todate == '') {
  $todate = $_GET['todate'];
}

if ($selfromdate == '') {
  $selfromdate = $_GET['selfromdate'];
}

if ($seltodate == '') {
  $seltodate = $_GET['seltodate'];
}


if ($months == '') {
  $months = $_GET['months'];
}

if ($months == '') {
  $months = "12";
}



if ($locationid == '' ) {
  $locationid = '0';
}

if ($fromdate == '') {
  $fromdate = date('Y-m-d');
}

if ($todate == '') {
  $todate = dateadd('m', 12, strtotime($fromdate));
}


$querylocpart = '';
if ($locationid!='0') {
	$querylocpart = ' and r.location=' . $locationid;
}

 switch($approved) {

   case '1':
	$querylocpart .= ' and r.approved=1';
      break;
   case '2':
	$querylocpart .= ' and r.approved=0';
      break;
   case '3':
	$querylocpart .= ' and r.approved is null';
      break;
    default:
      break;
   }


$query = sprintf( "select fm.displayname, %s as admin, fm.email, r.id as reservation_id, r.location, r.fullname, r.telephone, r.arrivedate, r.departdate, r.createdate, r.comments, r.approved, r.approverejectby, fm2.displayname from reservation r left outer join familymember fm on r.familymember_id=fm.id left outer join familymember fm2 on r.approverejectby=fm2.id where (r.arrivedate between %s and %s or departdate between %s and %s) %s order by arrivedate asc" ,
          getsessionadmin($sessionid),
          sqlsafe($fromdate),
          sqlsafe($todate),
          sqlsafe($fromdate),
          sqlsafe($todate),
          $querylocpart);

$reservations = mhexecquery($query, $err);

function reservationmonths($months) {
   $htmlstr = '';
   $basenolink = '<b>Next %s Months</b>';
   $baselink = '<a href="javascript:getreservations(%s)" title="View reservations for the next %s months">Next %s months</a>';

   switch($months) {

    case '24':
      $htmlstr = sprintf($baselink . " | " . $basenolink, "12", "12", "12", "24") ;
      break;
    default:
      $htmlstr = sprintf($basenolink . " | " . $baselink, "12", "24", "24", "24") ;
      break;
   }

   //add the selec dates link
   $htmlstr = sprintf('%s | %s', '<a href="javascript:showdaterange()" title="Select a date range">Select Dates</a>', $htmlstr) ;
   return $htmlstr;
}

function showlink($linkaction, $admin, $rowemail, $sessionemail, $rowid, $bookee) {
  $htmlstr = "&nbsp;";
  if ($rowemail == $sessionemail || $admin==1) {
    if ($linkaction=='delete') {
       $htmlstr = '<a href="javascript:cancelreservation(' . $rowid . ', \'' . $bookee . '\')" title="Cancel reservation"><img src="image/remove.gif" width="11" height="11" border="0"></a>';
    }
    elseif ($linkaction=='edit') {
       $htmlstr = '<a href="javascript:editreservation(' . $rowid . ')" title="Update reservation details"><img src="image/edit.gif" width="18" height="15" border="0"></a>';
    }
  }
  if ($linkaction=='view') {
       $htmlstr = '<a href="javascript:viewreservation(' . $rowid . ')" title="View reservation details"><img src="image/view.png" width="20" height="20" border="0"></a>';
  }

  return $htmlstr;
}

function approverejectinfo($approved, $displayname, $approverejectdate) {
    $htmlstr = '';
    if ($approved==true) {
        $htmlstr = "Approved by " .$displayname ;
    }
    elseif (is_null($approved))  {
        $htmlstr = "Booking request awaiting approval";
    }

    elseif ($approved==false) {
        $htmlstr = "Request declined by " .$displayname;
    }

    return $htmlstr;
}

function getapproveimage($approved) {
    $htmlstr = '';
    if ($approved==true) {
        $htmlstr = '<img align="center" src="image/greentick.png" width="14" height="14" border="0">' ;
    }
    elseif (is_null($approved))  {
       $htmlstr = '<img align="center" src="image/pending.png" width="16" height="16" border="0">' ;
     }

    elseif ($approved==false) {
        $htmlstr = '<img align="center" src="image/redcross.png" width="12" height="12" border="0">' ;
    }

    return $htmlstr;

}

function getapprovelink($approvedrow, $admin) {
        $htmlstr = '';

    if ($admin==1) {
       $htmlstr = sprintf('href="javascript:approvebooking(\'%d\' )" title="Approve or Reject this request"',
            $approvedrow['reservation_id']
            );
    }
    else  {
       $htmlstr = sprintf('href="javascript:alert(\'%s\')" title="%s"',
            approverejectinfo( $approvedrow['approved'], $approvedrow['displayname'], $approvedrow['approverejectdate'] ),
            htmlspecialchars(approverejectinfo( $approvedrow['approved'], $approvedrow['displayname'], $row['approverejectdate'] ))
            );
     }


    return $htmlstr;

}

?>


<html>
<head>
  <title>Malthouse Cottage - Reservations</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhdate.js"></script>
  <script src="script/mhvalidate.js"></script>
  <script src="script/dateselector.js"></script>
  <script>
  <!--
  function getreservations( months) {
    if (months == 0) {
      if (validatedates()) {
        var day  = document.mainform.selfromdate.value.substring(0, 2)
        var month = document.mainform.selfromdate.value.substring(3, 5)
        var year  = document.mainform.selfromdate.value.substring(6, 10)
        document.mainform.fromdate.value = year + '-' + month + '-' + day;
        day  = document.mainform.seltodate.value.substring(0, 2)
        month = document.mainform.seltodate.value.substring(3, 5)
        year  = document.mainform.seltodate.value.substring(6, 10)
        document.mainform.todate.value = year + '-' + month + '-' + day;
      }
      else {
        return false;
      }
    }
    else {
      var fromdate = new Date();
      document.mainform.fromdate.value = formatDate(fromdate, 'yyyy-MM-d');
      document.mainform.todate.value = formatDate(dateAdd('m', months, fromdate), 'yyyy-MM-d');
      document.mainform.months.value = months;
    }
      document.mainform.submit();
  }

  function validatedates() {
    if (!checkdate(document.mainform.selfromdate, 'Start Date')) {
      return false;
    }

    if (!checkdate(document.mainform.seltodate, 'End Date')) {
      return false;
    }
	if (!CheckDateDropDownStartEnd(document.mainform.selfromdate, document.mainform.seltodate, 'Start Date', 'End Date')) {
	  return false;
	}
    return true;
  }

  function editreservation(rsvid) {
   document.location.href='reservation_add.php?resid=' + rsvid + '&sess=' + escape(getsession()) + '&locationid=' + document.mainform.locationid.value + '&approved=' + document.mainform.approved.value;

 }

  function viewreservation(rsvid) {
   document.location.href='reservation_view.php?resid=' + rsvid + '&sess=' + escape(getsession()) + '&locationid=' + document.mainform.locationid.value + '&approved=' + document.mainform.approved.value;

 }
  function cancelreservation(rsvid, bookee) {
    if (confirm('Are you sure you would like to delete reservation for ' + bookee + '?') ) {
      document.mainform.rowid.value = rsvid;
      document.mainform.act.value = 'del';
      document.mainform.submit();
    }
  }

  function showdaterange() {

    if (document.all) {
      if (document.all['selectdates'].style.display == 'none') {
        document.all['selectdates'].style.display = "inline";
        document.all['noselectdates'].style.display= "none";
      }
      else {
		document.mainform.selfromdate.value='';
		document.mainform.seltodate.value='';
        document.all['selectdates'].style.display= "none";
        document.all['noselectdates'].style.display= "inline";
      }
    }
    else {
       if (document.getElementById("selectdates").style.display == 'none') {
        document.getElementById('selectdates').style.display = "inline";
        document.getElementById('noselectdates').style.display= "none";
       }
       else {
		document.mainform.selfromdate.value='';
		document.mainform.seltodate.value='';
        document.getElementById('selectdates').style.display= "none";
        document.getElementById('noselectdates').style.display= "inline";
      }
    
    }
  }

function displayselectors() {
		if (document.mainform.selfromdate != null && document.mainform.selfromdate.value!='') {
          document.all['selectdates'].style.display = "inline";
          document.all['noselectdates'].style.display= "none";
		}
}
//-->
  </script>

</head>
<body onload="displayselectors()">
<form name="mainform" action="reservations.php" method="post">
<input name="sess" value="<? echo htmlspecialchars($sessionid) ?>" type="hidden">
<input name="fromdate" value="<? echo htmlspecialchars($fromdate) ?>" type="hidden">
<input name="todate" value="<? echo htmlspecialchars($todate) ?>" type="hidden">
<input name="months" value="<? echo htmlspecialchars($months) ?>" type="hidden">
<input name="locationid" value="<? echo htmlspecialchars($locationid) ?>" type="hidden">
<input name="approved" value="<? echo htmlspecialchars($approved) ?>" type="hidden">
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
            <td width="562" align="left" ><h2>Bookings</h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>Bookings</b></td>
                   <td align="right" class="listboxheadingright">
                     <div id="noselectdates" style="display:inline" ><?echo reservationmonths($months)?></div>
                     <div id="selectdates" style="display:none" >
                       <? createinput_text("selfromdate", $selfromdate, "10", "10")?>
                         <script LANGUAGE="JavaScript" ID="jscalfrom">
                           var jscalfrom = new CalendarPopup("testdiv1");
                           jscalfrom.showNavigationDropdowns();
                         </script>
                         <a href="#" onClick="jscalfrom.select(document.forms[0].selfromdate,'anchorfromdate','dd/MM/yyyy'); return false;" TITLE="Select a start date" NAME="anchorfromdate" ID="anchorfromdate"><img src="image/calendar.jpg" border="0"></A>
                     to
                       <? createinput_text("seltodate", $seltodate, "10", "10")?>
                         <script LANGUAGE="JavaScript" ID="jscalto">
                           var jscalto = new CalendarPopup("testdiv1");
                           jscalto.showNavigationDropdowns();
                         </script>
                         <a href="#" onClick="jscalto.select(document.forms[0].seltodate,'anchortodate','dd/MM/yyyy'); return false;" TITLE="Select an end date" NAME="anchortodate" ID="anchortodate"><img src="image/calendar.jpg" border="0"></A>
					     <? createinput_submit('Go', false, 'getreservations(0)')?>&nbsp;
					     <? createinput_submit('Cancel', false, 'showdaterange()')?>
                     </div>

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
                  <td colspan="4" align="left"><img src="image/spacer.gif" border="0" width="1" height="20" ></td>
                </tr>
                <?if ($everr !== '' ) {?>
                <tr valign="top">
                  <td colspan="4" align="center" class="error"><? echo $everr ?></td>
                </tr>
                <?}?>
                <tr valign="top">
                  <td align="left">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tr valign="middle">
                        <td align="left">
							<select name="locfilter" onchange="viewbookings(this.value, document.mainform.appfilter.value)">
							   <option value="0">Filter by Property...</option>
							   <option value="2" <?if ($locationid=='2') echo 'selected'?>>Flayosc</option>
							</select>
                            &nbsp;
							<select name="appfilter" onchange="viewbookings(document.mainform.locfilter.value, this.value)">
							   <option value="">Filter by status...</option>
							   <option value="1" <?if ($approved=='1') echo 'selected'?>>Approved</option>
							   <option value="2"<?if ($approved=='2') echo 'selected'?>>Rejected</option>
							   <option value="3"<?if ($approved=='3') echo 'selected'?>>Pending Approval</option>
							</select>
                        </td>
                        <td align="right"><a href="javascript:createreservation()">New Booking</a></td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr valign="top">
                  <td align="center">
					  <table width="100%" cellpadding="5" cellspacing="0" border="0">
		<?if ($msg != '')  {?>
						<tr valign="middle">
						  <td align="left" colspan="7" class="message"><? echo $msg?></td>
						</tr>
        <?}
        if (!$reservations)  { ?>
						<tr valign="middle">
						  <td align="left" colspan="7" class="error">There are no bookings during the period selected</td>
						</tr>
		<? }
		else if ($err == '') {
		?>
						<tr valign="middle">
						  <td align="left" width="100" class="listboxheading"><b>Location</b></td>
						  <td align="left" width="170" class="listboxheading"><b>Dates</b></td>
						  <td align="left" width="125" class="listboxheading"><b>Who</b></td>
						  <td align="center" class="listboxheading">&nbsp;</td>
						  <td align="center" class="listboxheading">&nbsp;</td>
<?if (getsessionadmin($sessionid)==1) { ?>
						  <td align="center" class="listboxheading"><b>Change</b></td>
						  <td align="center" class="listboxheading"><b>Remove</b></td>
<?}
else {?>
						  <td align="center" class="listboxheading"><b>View</b></td>
<?}?>

						</tr>
		<?
				$rowcount = 0;
				while ($row = mysql_fetch_assoc($reservations)) {
						$rowcount .= 1;

		?>
						<tr valign="middle">
						  <td align="left" class="listitem"><? if ($row['location']=='2') {?>
						  										Flayosc&nbsp;
						  									  <?  } else { ?>
						  										Malthouse&nbsp;
						  									  <? } ?>
						  </td>
						  <td align="left" width="170" class="listitem" nowrap width="125" title="<? echo tooltipdate($row['arrivedate'])?>"><? echo displaydate($row['arrivedate'], false)?> to <? echo displaydate($row['departdate'], false)?>&nbsp;</td>
						  <td align="left" class="listitem"  width="125"><? echo htmlspecialchars($row['fullname'])?>&nbsp;</td>
						  <td align="center" class="listitem"><?if (trim($row['comments']) !== '') {?><a href='javascript:alert(<?echo sqlsafe($row['comments'])?>)' title="<? echo htmlspecialchars($row['comments'])?>"><img align="center" src="image/comment.gif" width="19" height="20" border="0"></a><?}?>&nbsp;</td>
						  <td align="center" class="listitem"><a <?echo getapprovelink($row, getsessionadmin($sessionid));?>><? echo  getapproveimage($row['approved']);?></a>&nbsp;</td>
<?if (getsessionadmin($sessionid)==1) { ?>
                          <td align="center" class="listitem" ><? echo showlink('edit', getsessionadmin($sessionid), $row[email], getsessionemail($sessionid), $row['reservation_id'], $row['fullname'])?>&nbsp;</td>
						  <td align="center" class="listitem" ><? echo showlink('delete', getsessionadmin($sessionid), $row[email], getsessionemail($sessionid), $row['reservation_id'], $row['fullname'])?>&nbsp;</td>
<?} 
else {?>
                          <td align="center" class="listitem" ><? echo showlink('view', getsessionadmin($sessionid), $row[email], getsessionemail($sessionid), $row['reservation_id'], $row['fullname'])?>&nbsp;</td>
<?}?>
                        </tr>


		<?		}
		   if ($rowcount == 0) {?>

						<tr valign="middle">
						  <td align="left" colspan="6" class="error">There are no bookings during the period selected</td>
						</tr>

		<?	}
		}

		?>
						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
					  </table>
                  </td>
                </tr>
                <tr valign="top">
                  <td colspan="4" align="left"><img src="image/spacer.gif" border="0" width="1" height="20" ></td>
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
			  <td align="center"><b>Current Status</b></td>
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
