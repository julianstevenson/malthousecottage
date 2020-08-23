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

if ($resid !== '') {  //Get the reservations

  $query = sprintf( "select fm.displayname, fm.admin, fm.email, r.approved, r.id as reservation_id, r.location, r.createdate, r.fullname, r.telephone, r.arrivedate, r.departdate, r.createdate, r.comments from reservation r left outer join familymember fm on r.familymember_id=fm.id where r.id=%s" ,
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
  //-->
  </script>

</head>

<body >
<form name="mainform" action="reservation_view.php" method="post" onsubmit="return validateaddreservation()">
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
            <td width="562" align="left" ><h2>Booking Details</h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>Booking Details</b></td>
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
                   <td align="left" colspan="3"><b>&nbsp;</b>
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
                  <td align="left" width="150"><b>Booked By:</b></td>
                  <td align="left" width="400"><? echo getformvalue('displayname', $row, !$reservation)?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Date Created:</b></td>
                  <td align="left" width="400"><? echo displaytime(getformvalue('createdate', $row, !$reservation))?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Approval Status:</b></td>
                  <td align="left" width="400"><? echo approvalstatus( $row['approved'])?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Property:</b></td>
                  <td align="left" width="250"><?if (getformvalue('location', $row, !$reservation )=='1') echo 'Malthouse Cottage'?>
                    <?if (getformvalue('location', $row, !$reservation )=='2') echo 'Flayosc'?>
                    
                    &nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Name of Guest:</b></td>
                  <td align="left" width="250"><? echo getformvalue('fullname', $row, !$reservation )?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Contact Telephone:</b></td>
                  <td align="left" width="400"><? echo getformvalue('telephone', $row, !$reservation)?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Arrival Date:</b></td>
                  <td align="left" width="400"><? echo  displaydate(getformvalue('arrivedate', $row, !$reservation ))?>&nbsp;
                </td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Departure Date:</b></td>
                  <td align="left" width="400"><? echo displaydate(getformvalue('departdate', $row, !$reservation))?>&nbsp;
                </td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Comments:</b></td>
                  <td align="left" width="400"><? echo getformvalue('comments', $row, !$reservation )?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150">&nbsp;</td>
                  <td align="left" width="400"><? createinput_submit("Back", false, "viewbookings(document.mainform.locationid.value, document.mainform.approved.value)")?></td>
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

