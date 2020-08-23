<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";

$famid = trim($_POST['famid']);

if (trim($famid) == '') {
  $famid = trim($_GET['famid']);
}
if ($famid == '')  {
 $err= 'No family member selected';
}
else{

  $query = sprintf( "select l.*, c.country, f.description, fm.email, fam.createdate, fam.firstname, fam.displayname, fam.family_id, fam.email, fam.password, fam.location_id, fam.workphone, fam.mobilephone, fam.skypename, fam.msnname, fam.workemail, fam.isuser, fam.admin, fam.management, fam.support from familymember fam left outer join familymember fm on fam.familymember_id=fm.id inner join location l on l.id = fam.location_id inner join country c on c.id=l.country_id inner join family f on f.id=fam.family_id where fam.id=%s" ,
            $famid
             );
  $family = mhexecquery($query, $err);
  $row = mysql_fetch_assoc($family);

  $query = sprintf( "select et.description as eventtype, e.description, e.eventday, e.eventmonth from event e inner join eventtype et on et.id=e.eventtype_id inner join eventfamilymember efm on efm.event_id=e.id inner join familymember fm on fm.id=efm.familymember_id where fm.id=%s" ,
            $famid
             );
  $events = mhexecquery($query, $err);

}


?>
<html>
<head>
  <title>Malthouse Cottage - Family Member</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhdate.js"></script>
  <script src="script/mhvalidate.js"></script>
  <script src="script/dateselector.js"></script>
  <script>
  <!--
  //-->
  </script>

</head>

<body>
<form name="mainform" action="family.php" method="post" onsubmit="return validateaddfamily()">
<input name="sess" value="<? echo htmlspecialchars($sessionid) ?>" type="hidden">
<input name="famid" value="<? echo htmlspecialchars($famid) ?>" type="hidden">
<input name="today" value="" type="hidden">
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
            <td width="562" align="left" ><h2>Profile - <? echo htmlspecialchars($row['displayname'])?></h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>Family Member Details</b></td>
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
		}
		if (!$family && $famid!='')  { ?>
						<tr valign="middle">
						  <td align="left" colspan="3">Invalid family id</td>
						</tr>
		<?}
		else {
			//$row = mysql_fetch_assoc($family);
		}
		?>


						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>First Names:</b></td>
						  <td align="left" width="400"><? echo htmlspecialchars($row['firstname'])?></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Family:</b></td>
						  <td align="left" width="400"><? echo htmlspecialchars($row['description'])?></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Address:</b></td>
						  <td align="left" width="400">
						  <?
							 if (trim($row['address1']) != '') {
							   echo htmlspecialchars($row['address1']) . '<br>';
							 }
							 if (trim($row['address2']) != '') {
							   echo htmlspecialchars($row['address2']) . '<br>';
							 }
							 if (trim($row['town']) != '') {
							   echo htmlspecialchars($row['town']) . '<br>';
							 }
							 if (trim($row['state']) != '') {
							   echo htmlspecialchars($row['state']) . '<br>';
							 }
							 if (trim($row['postcode']) != '') {
							   echo htmlspecialchars($row['postcode']) . '<br>';
							 }
							 if (trim($row['country']) != '') {
							   echo htmlspecialchars($row['country']) . '<br>';
							 }

						   ?>
						  </td>
						</tr>

						<tr valign="top">
						  <td align="left" width="150"><b>Email:</b></td>
						  <td align="left" width="400"><? echo htmlspecialchars($row['email'])?>&nbsp;</td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Work Email:</b></td>
						  <td align="left" width="400"><? echo htmlspecialchars($row['workemail'])?>&nbsp;</td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150" nowrap><b>Home Telephone:</b></td>
						  <td align="left" width="400"><? echo htmlspecialchars($row['workphone'])?>&nbsp;</td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Mobile Telephone:</b></td>
						  <td align="left" width="400"><? echo htmlspecialchars($row['mobilephone'])?>&nbsp;</td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Skype Name:</b></td>
						  <td align="left" width="400"><? echo htmlspecialchars($row['skypename'])?>&nbsp;</td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>MSN Name:</b></td>
						  <td align="left" width="400"><? echo htmlspecialchars($row['msnname'])?>&nbsp;</td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150">&nbsp;</td>
						  <td align="left" width="400"><? createinput_submit("Return to Family Members")?>&nbsp;</td>
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


          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>Events</b></td>
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
						<tr valign="middle">
						  <td align="left" class="listboxheading"><b>Date</b></td>
						  <td align="left" class="listboxheading"><b>Event</b></td>
						  <td align="left" class="listboxheading"><b>Description</b></td>
						</tr>
		<?
		$rowcount = 0;
		while ($row = mysql_fetch_assoc($events)) {
									$rowcount .= 1; ?>
						<tr valign="top">
						  <td align="left" class="listitem"><?echo eventdate($row['eventday'], $row['eventmonth'])?></td>
						  <td align="left" class="listitem"><?echo $row['eventtype']?></td>
						  <td align="left" class="listitem"><?echo htmlspecialchars($row['description'])?></td>
						</tr>
		<?}

		if ($rowcount==0) {
		?>
						<tr valign="top">
						  <td align="left" class="error">No events listed </td>
						</tr>
		<?}?>
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
