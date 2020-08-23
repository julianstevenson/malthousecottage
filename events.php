<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";
$msg = $_GET['msg'];


$month  = array( "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December" );

if ($_POST['act'] == 'del') {
  if ($_POST['rowid'] == '') {
    $err = "No row id supplied";
  }
  else {
    $query = sprintf( "delete from eventfamilymember where id = %s" ,
          $_POST['rowid']);

    if (!mhexecquery($query, $err)) {
       //
    }
    else {
      $msg = "Event deleted";
    }
  }

}


$family_idsql = '';
if (trim($_POST['family_id']) !== '') {
  $family_idsql = ' where fm.family_id=' . trim($_POST['family_id']);
}
if (trim($_POST['familymember_id']) !== '') {
  $family_idsql .= ' and fm.familymember_id=' . trim($_POST['familymember_id']);
}

$sortbysql='order by eventmonth desc, eventday desc';

if ($_POST['sortby'] == 'who') {
  $sortbysql = " order by fm.displayname asc";
}
if ($_POST['sortby'] == 'date') {
  $sortbysql = " order by e.eventmonth, e.eventday asc";
}
if ($_POST['sortby'] == 'event') {
  $sortbysql = " order by et.description asc";
}

$query = sprintf( "select fm.displayname, %s as admin, fm.email, e.id as event_id, efm.id as eventfamilymember_id, e.description, e.eventday, e.eventmonth, e.eventyear, et.annual, et.description as eventtypedesc from event e inner join eventtype et on e.eventtype_id=et.id inner join eventfamilymember efm on e.id=efm.event_id inner join familymember fm on efm.familymember_id=fm.id %s %s" ,
          getsessionadmin($sessionid),
          $family_idsql,
          $sortbysql);

$events = mhexecquery($query, $err);

function eventmonths($months) {
   $htmlstr = '';
   $basenolink = '<b>Next %s Months</b>';
   $baselink = '<a href="javascript:getevents(%s)" title="View events for the next %s months">Next %s months</a>';

   switch($months) {

    case '6':
      $htmlstr = sprintf($baselink . " | " . $basenolink . " | " . $baselink, "3", "3", "3", "6", "12", "12", "12") ;
      break;
    case '12':
      $htmlstr = sprintf($baselink . " | " . $baselink . " | " . $basenolink, "3", "3", "3", "6", "6", "6", "12") ;
      break;
    default:
      $htmlstr = sprintf($basenolink . " | " . $baselink . " | " . $baselink, "3", "6", "6", "6", "12", "12", "12") ;
      break;
   }

   //add the selec dates link
   $htmlstr = sprintf('%s | %s', '<a href="javascript:showdaterange()" title="Select a date range">Select Dates</a>', $htmlstr) ;
   return $htmlstr;
}

function showlink($linkaction, $admin, $rowemail, $sessionemail, $rowid) {
  $htmlstr = "&nbsp;";
  if ($rowemail == $sessionemail || $admin==1) {
    if ($linkaction=='delete') {
       $htmlstr = '<a href="javascript:deleteevent(' . $rowid . ')" title="Delete event"><img src="image/remove.gif" width="11" height="11" border="0"></a>';
    }
    elseif ($linkaction=='edit') {
       $htmlstr = '<a href="javascript:editevent(' . $rowid . ')" title="Update event details"><img src="image/edit.gif" width="18" height="15" border="0"></a>';
    }
  }
  return $htmlstr;
}



?>


<html>
<head>
  <title>Malthouse Cottage - Events</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhdate.js"></script>
  <script src="script/mhvalidate.js"></script>
  <script src="script/dateselector.js"></script>
  <script>
  <!--

  function sortby(sortfield) {
      document.mainform.sortby.value = sortfield;
      getevents(1);
  }

  function getevents( months) {
      document.mainform.submit();
  }

  function editevent(eventid) {
  	document.location.href='event_add.php?eventid=' + eventid + '&sess=' + escape(getsession());
  }

  function deleteevent(eventid) {
    if (confirm('Are you sure you would like to delete this event?') ) {
      document.mainform.rowid.value = eventid;
      document.mainform.act.value = 'del';
      document.mainform.submit();
    }
  }


//-->
  </script>

</head>
<body>
<form name="mainform" action="events.php" method="post">
<input name="sess" value="<? echo htmlspecialchars($sessionid) ?>" type="hidden">
<input name="rowid" value="" type="hidden">
<input name="act" value="" type="hidden">
<input name="sortby" value="<? echo $_POST['sortby']?>" type="hidden">

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
            <td width="562" align="left" ><h2>Events</h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>Events</b></td>
                   <td align="right" class="listboxheadingright"><a href="javascript:createevent()" title="Create event">Create an Event</a>
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
                <tr valign="top">
                  <td align="left">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tr valign="middle">
                        <td align="left">
						   <? createinput_select("Family", "description", $_POST['family_id'], "document.mainform.submit()", true);
						   if (trim($_POST['family_id']) !== '') { ?>
						   <? createinput_select_fm($_POST['familymember_id'], $_POST['family_id'], "document.mainform.submit()", true);
						   }
						   ?> 
                           (Click a heading to sort)
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr valign="top">
                  <td align="center">
					  <table width="100%" cellpadding="5" cellspacing="0" border="0">
						<tr valign="middle">
						  <td align="left" width="200" class="listboxheading"><a href="javascript:sortby('who')" title="Click to sort by name"><b>Who</b></a></td>
						  <td align="left" width="250" class="listboxheading"><a href="javascript:sortby('event')" title="Click to sort by event type"><b>Event Type</b></a></td>
						  <td align="left" width="125" class="listboxheading"><a href="javascript:sortby('date')" title="Click to sort by date"><b>Date</b></a></td>
<?if (getsessionadmin($sessionid)==1) { ?>
						  <td align="center" class="listboxheading"><b>Change</b></td>
						  <td align="center" class="listboxheading"><b>Remove</b></td>
<? }?>
						</tr>
		<? if (!$events)  { ?>
						<tr valign="middle">
						  <td align="left" colspan="6">No events</td>
						</tr>
		<? }
		else if ($err == '') {
				$rowcount = 0;
				while ($row = mysql_fetch_assoc($events)) {
						$rowcount .= 1;

		?>
						<tr valign="middle">
						  <td align="left" class="listitem" width="200"><? echo $row['displayname']?>&nbsp;</td>
						  <td align="left" class="listitem"  width="200"><? echo $row['eventtypedesc']?>&nbsp;</td>
						  <td align="left" class="listitem"  width="125"><? echo eventdate($row['eventday'], $row['eventmonth'], $row['eventyear'], $row['annual'])?></td>
<?if (getsessionadmin($sessionid)==1) { ?>
						  <td align="center" class="listitem" ><? echo showlink('edit', getsessionadmin($sessionid), $row[email], getsessionemail($sessionid), $row['event_id'])?>&nbsp;</td>
						  <td align="center" class="listitem" ><? echo showlink('delete', getsessionadmin($sessionid), $row[email], getsessionemail($sessionid), $row['eventfamilymember_id'])?>&nbsp;</td>
<?}?>
                        </tr>


		<?		}
		   if ($rowcount == 0) {?>

						<tr valign="middle">
						  <td align="left" colspan="6" class="error">No events</td>
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
