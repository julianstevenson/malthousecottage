<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";
$eventid = trim($_POST['eventid']);

if (trim($eventid) == '') {
  $eventid = trim($_GET['eventid']);
}

if ($_POST['Create'] == 'Create') {
      //Create the event
      $query = sprintf( 'insert into event (familymember_id, eventtype_id, eventday, eventmonth, eventyear, description) select fm.id, %s, %s, %s, %s, %s from familymember fm where email=%s' ,
          $_POST['eventtype_id'],
          $_POST['eventday'],
          $_POST['eventmonth'],
          sqlsafe($_POST['eventyear']),
          sqlsafe($_POST['description']),
          sqlsafe(getsessionemail($sessionid))
          );
      mhexecquery($query, $err);
  	  if ($err == '') { //All ok so assign event to people
         $query = 'select max(id) as event_id from event';
         $event = mhexecquery($query, $err);
         if ($err == '') {
           $row = mysql_fetch_assoc($event);
           $query = '';
           $recipients = $_POST['family_id'];
      	   foreach ($recipients as $family_id) {
              $query = sprintf('insert into eventfamilymember (event_id, familymember_id) values( %s, %s) ', $row['event_id'], $family_id);
              mhexecquery($query, $err);
           }
           if ($err == '') {
  	  		  Header("Location: events.php?msg=" . urlencode("Event Created Successfuly") . "&sess=" . urlencode($sessionid)) ;
  	  	   }

         }

      }

}
elseif ($_POST['Update'] == 'Update') {
      $query = sprintf( 'update event set eventtype_id=%s, eventday=%s, eventmonth=%s, eventyear=%s, description=%s where id=%s ' ,
          $_POST['eventtype_id'],
          $_POST['eventday'],
          $_POST['eventmonth'],
          sqlsafe($_POST['eventyear']),
          sqlsafe($_POST['description']),
          $_POST['eventid']          );
      mhexecquery($query, $err);
  	  if ($err == '') { //All ok so assign event to people
         $query = 'delete from eventfamilymember where event_id=' . $eventid;
         mhexecquery($query, $err);
         if ($err == '') {
           $query = '';
           $recipients = $_POST['family_id'];
      	   foreach ($recipients as $family_id) {
              $query = sprintf('insert into eventfamilymember (event_id, familymember_id) values (%s, %s) ', $eventid, $family_id);
              mhexecquery($query, $err);
           }
           if ($err == '') {
  	  		  Header("Location: events.php?msg=" . urlencode("Event Created Successfuly") . "&sess=" . urlencode($sessionid)) ;
  	  	   }

         }
  	  }

}
elseif ($eventid !== '') {

  $query = sprintf( "select fm.displayname, fm.email, e.id as event_id, e.createdate, e.description, e.eventday, e.eventmonth, e.eventyear, e.eventtype_id, et.annual from event e inner join eventtype et on e.eventtype_id=et.id left outer join familymember fm on e.familymember_id=fm.id where e.id=%s" ,
          $eventid);
  $event = mhexecquery($query, $err);

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
  <title>Malthouse Cottage - Event</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhvalidate.js"></script>
  <script>
  <!--
  function validateaddevent( ) {

    if (!checkmandatoryfield(document.mainform.description, 'Description', true)) {
      return false;
    }
    if (!checkmandatoryfield(document.mainform.eventtype_id, 'Event Type', true)) {
      return false;
    }
    if (!checkmandatoryfield(document.mainform.eventday, 'Event Day', true)) {
      return false;
    }
    if (!checkmandatoryfield(document.mainform.eventmonth, 'Event Month', true)) {
      return false;
    }
    if (document.mainform.eventyear != null && document.all['eventyearspan'].style.display=='inline') {
      if (!checkmandatoryfield(document.mainform.eventyear, 'Event Year', true)) {
        return false;
      }
    }
    if(document.all) {
     if (document.all['family_id[]'].selectedIndex <0) {
       alert('Please select one or more family members');
       return false;
     }
   }

    return true;
  }

  function loadform() {
     var i;
     for (i=0; i<document.mainform.eventday.options.length; i++) {
       if (document.mainform.eventday.options[i].value == document.mainform.eventdayhidden.value) {
         document.mainform.eventday.selectedIndex = i;
         break;
       }
     }
     for (i=0; i<document.mainform.eventmonth.options.length; i++) {
       if (document.mainform.eventmonth.options[i].value == document.mainform.eventmonthhidden.value) {
         document.mainform.eventmonth.selectedIndex = i;
         break;
       }
     }
     seteventyear();
     document.mainform.description.focus()
  }


    var eventtypes = new Array();
    var annual = new Array();
    <?
          $query = 'select id, annual from eventtype';
	      $eventtypes = mhexecquery($query, $err);
	      $etcounter = 0;
	      while($row = mysql_fetch_assoc($eventtypes)) {
	        echo 'eventtypes[' . $etcounter . '] = "' . $row['id'] . '"' . "\n" . ';';
	        echo 'annual[' . $etcounter . '] = "' . $row['annual'] . '"' . "\n" . ';';
            $etcounter += 1;
	      }


    ?>

  function seteventyear( ) {
  // if(document.all) {
     eventtype = document.mainform.eventtype_id.options[document.mainform.eventtype_id.selectedIndex].value;
     for (i=0; i<eventtypes.length; i++) {
       if (eventtypes[i] == eventtype) {
         document.mainform.annual.value = annual[i];
       }
     }
     if (document.mainform.annual.value == "1") {
       if (document.all) {
           document.all['annualspan'].style.display='inline';
           document.all['eventyearspan'].style.display='none';
       }
       else {
           document.getElementById('annualspan').style.display='inline';
           document.getElementById('eventyearspan').style.display='none';
       }
     }
     else {
       if (document.all) {
           document.all['annualspan'].style.display='none';
           document.all['eventyearspan'].style.display='inline';
       }
       else {
           document.getElementById('annualspan').style.display='none';
           document.getElementById('eventyearspan').style.display='inline';
       }
     }
//  }


  }
  //-->
  </script>

</head>
<body onload="loadform()">
<form name="mainform" action="event_add.php" method="post" onsubmit="return validateaddevent()">
<input name="sess" value="<? echo htmlspecialchars($sessionid) ?>" type="hidden">
<input name="eventid" value="<? echo htmlspecialchars($eventid) ?>" type="hidden">
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
            <td width="562" align="left" ><h2>New Event</h2></td>
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
						<tr valign="top" colspan="3">
						   <td align="left" colspan="3"><b>Enter the event details, then click the 'Create' button. Select the Family Members who this event applies to (<span class="error">*Mandatory fields</span>).</b>
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
		if (!$event && $eventid!='')  { ?>
						<tr valign="middle">
						  <td align="left" colspan="6">Invalid event id</td>
						</tr>
		<?}
		else {
			if ($event) {
			  $row = mysql_fetch_assoc($event);
			}
		}
		?>


						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
		<?if ($eventid!='') { ?>
						<tr valign="top">
						  <td align="left" width="150"><b>Created By:</b></td>
						  <td align="left" width="400"><? echo getformvalue('displayname', $row, !$event)?><input type="hidden" value="<? echo getformvalue('displayname', $row, !$event)?>" name="displayname"></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Date Created:</b></td>
						  <td align="left" width="400"><? echo displaytime(getformvalue('createdate', $row, !$event))?><input type="hidden" value="<? echo getformvalue('createdate', $row, !$event)?>" name="createdate"></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
		<?}?>
						<tr valign="top">
						  <td align="left" width="150"><b>Description:</b></td>
						  <td align="left" width="400"><? createinput_text("description", getformvalue('description', $row, !$event ), "30", "50")?>&nbsp;<span class="error">*</span></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Event Type:</b></td>
						  <td align="left" width="400">
							<? createinput_select("EventType", "description", getformvalue('eventtype_id', $row, !$event ), "seteventyear( )")?>&nbsp;<span class="error">*</span>
							<input name="annual" value="<? echo getformvalue('annual', $row, !$event ) ?>" type="hidden">
						  </td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Date:</b></td>
						  <td align="left" width="400">
							<input type="hidden" name="eventdayhidden" value="<? echo getformvalue('eventday', $row, !$event )?>">
							<select name="eventday">
							  <option value=""></option>
							  <option value="1">1</option>
							  <option value="2">2</option>
							  <option value="3">3</option>
							  <option value="4">4</option>
							  <option value="5">5</option>
							  <option value="6">6</option>
							  <option value="7">7</option>
							  <option value="8">8</option>
							  <option value="9">9</option>
							  <option value="10">10</option>
							  <option value="11">11</option>
							  <option value="12">12</option>
							  <option value="13">13</option>
							  <option value="14">14</option>
							  <option value="15">15</option>
							  <option value="16">16</option>
							  <option value="17">17</option>
							  <option value="18">18</option>
							  <option value="19">19</option>
							  <option value="20">20</option>
							  <option value="21">21</option>
							  <option value="22">22</option>
							  <option value="23">23</option>
							  <option value="24">24</option>
							  <option value="25">25</option>
							  <option value="26">26</option>
							  <option value="27">27</option>
							  <option value="28">28</option>
							  <option value="29">29</option>
							  <option value="30">30</option>
							  <option value="31">31</option>
							</select>
							<input type="hidden" name="eventmonthhidden" value="<? echo getformvalue('eventmonth', $row, !$event )?>">
							<select name="eventmonth">
							  <option value=""></option>
							  <option value="1">Jan</option>
							  <option value="2">Feb</option>
							  <option value="3">Mar</option>
							  <option value="4">Apr</option>
							  <option value="5">May</option>
							  <option value="6">Jun</option>
							  <option value="7">Jul</option>
							  <option value="8">Aug</option>
							  <option value="9">Sep</option>
							  <option value="10">Oct</option>
							  <option value="11">Nov</option>
							  <option value="12">Dec</option>
							</select>

						  <span class="error">*</span></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Year:</b></td>
						  <td align="left" width="400">
						   <span id="eventyearspan" style="display:none">
							<select name="eventyear">
							  <option value=""></option>
							  <?
							  $timepieces        = getdate(mktime());
							  $startyear = intval($timepieces["year"]);
							  $i=0;
							  while ($i<5) {
							   if (getformvalue('eventyear', $row, !$event ) == ($startyear+$i)) {
								 echo '<option value="' . ($startyear+$i) . '" selected>' . ($startyear+$i) . '</option>';
							   }
							   else {
								 echo '<option value="' . ($startyear+$i) . '">' . ($startyear+$i) . '</option>';
							   }
							   $i = $i + 1;
							  }

							  ?>

							</select>
						   </span>
						   <span id="annualspan" style="display:none">
						   Annual Event

						   </span>
						   &nbsp;
						  </td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Family Members:</b></td>
						  <td align="left" width="400"><? createinput_select_recipient($_POST['family_id'], 0)?><br>&nbsp;Hold the &lt;Shift&gt; or &lt;Control&gt; key to select multiple.<span class="error">*</span></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150">&nbsp;</td>
						  <td align="left" width="400"><? if ($eventid=='') createinput_submit("Create"); else createinput_submit("Update");?>&nbsp;<? createinput_submit("Cancel", false, "viewevents()")?></td>
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
