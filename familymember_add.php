<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";

$famid = trim($_POST['famid']);

if (trim($famid) == '') {
  $famid = trim($_GET['famid']);
}
if ($_POST['Add'] == 'Add' || $_POST['Update'] == 'Update') {

      if ($_POST['location_id']==-1) {
        //add new address
        $query = sprintf( 'insert into location (familymember_id, address1, address2, town, postcode, state, country_id) select fm.id, %s, %s, %s, %s, %s, %s from familymember fm where email=%s' ,
            sqlsafe($_POST['address1']),
            sqlsafe($_POST['address2']),
            sqlsafe($_POST['town']),
            sqlsafe($_POST['postcode']),
            sqlsafe($_POST['state']),
            sqlsafe($_POST['country_id']),
            sqlsafe(getsessionemail($sessionid))
            );


  	    mhexecquery($query, $err);
  	    if ($err == '' ) { //get the new idfor this location
          $query = sprintf( 'select id from location where address1=%s and address2=%s and town=%s and postcode=%s and state=%s and country_id=%s' ,
              sqlsafe($_POST['address1']),
              sqlsafe($_POST['address2']),
              sqlsafe($_POST['town']),
              sqlsafe($_POST['postcode']),
              sqlsafe($_POST['state']),
              sqlsafe($_POST['country_id'])
            );
          $query = str_replace('=null', ' is null ', $query);
  	      $locations = mhexecquery($query, $err);

  	      $row = mysql_fetch_assoc($locations);
  	      $location_id = $row['id'];


        }
      }
      else {
        $location_id = $_POST['location_id'];
      }

      if ($err == '') {
        $displayname = trim($_POST['displayname']);
        if ($displayname == '') {
          $displayname = $_POST['firstname'];
        }
        $isuser = trim($_POST['isuser']);
        if($isuser == '') $isuser = '0';
        $admin = trim($_POST['admin']);
        if($admin == '') $admin = '0';
        $management = trim($_POST['management']);
        if($management == '') $management = '0';
        $support = trim($_POST['support']);
        if($support == '') $support = '0';

        if ($_POST['Update'] == 'Update') {
          $query = sprintf( 'update familymember set firstname=%s, displayname=%s, family_id=%s, email=%s, location_id=%s, workphone=%s, mobilephone=%s, skypename=%s, msnname=%s, workemail=%s, isuser=%s, admin=%s, management=%s, support=%s where id = %s' ,
             sqlsafe($_POST['firstname']),
             sqlsafe($displayname),
             sqlsafe($_POST['family_id']),
             sqlsafe($_POST['email']),
             $location_id,
             sqlsafe($_POST['workphone']),
             sqlsafe($_POST['mobilephone']),
             sqlsafe($_POST['skypename']),
             sqlsafe($_POST['msnname']),
             sqlsafe($_POST['workemail']),
             $isuser,
             $admin,
             $management,
             $support,
             $famid
             );
  	  		if (mhexecquery($query, $err)) {
  	  		   Header("Location: family.php?msg=" . urlencode("Family Member Updated Successfuly") . "&sess=" . urlencode($sessionid)) ;
      		}
        }
        else {
          $query = sprintf( 'select id from familymember where email=%s' ,
              sqlsafe(getsessionemail($sessionid)));
  	      $fmids = mhexecquery($query, $err);

  	      $row = mysql_fetch_assoc($fmids);
  	      $fmid = $row['id'];
          $query = sprintf( 'insert into familymember (familymember_id, firstname, displayname, family_id, email, password, location_id, workphone, mobilephone, skypename, msnname, workemail, isuser, admin, management, support) values (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)' ,
             $fmid,
             sqlsafe($_POST['firstname']),
             sqlsafe($displayname),
             sqlsafe($_POST['family_id']),
             sqlsafe($_POST['email']),
             sqlsafe($_POST['password']),
             $location_id,
             sqlsafe($_POST['workphone']),
             sqlsafe($_POST['mobilephone']),
             sqlsafe($_POST['skypename']),
             sqlsafe($_POST['msnname']),
             sqlsafe($_POST['workemail']),
             $isuser,
             $admin,
             $management,
             $support
             );
          if (mhexecquery($query, $err)) {
    	    	Header("Location: family.php?msg=" . urlencode("Family Member Added Successfuly") . "&sess=" . urlencode($sessionid)) ;
          }
          else if (strpos ($err, "Duplicate entry") > 0) {
            $err = "Email address already registered.";
        }
        }
      }

}
elseif ($famid !== '') {

  $query = sprintf( "select fm.displayname as createdisplayname, fm.email, fam.createdate, fam.firstname, fam.displayname, fam.family_id, fam.email, fam.password, fam.location_id, fam.workphone, fam.mobilephone, fam.skypename, fam.msnname, fam.workemail, fam.isuser, fam.admin, fam.management, fam.support from familymember fam left outer join familymember fm on fam.familymember_id=fm.id where fam.id=%s" ,
            $famid
             );
  $family = mhexecquery($query, $err);
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
  <title>Malthouse Cottage - Family Members</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhdate.js"></script>
  <script src="script/mhvalidate.js"></script>
  <script src="script/dateselector.js"></script>
  <script>
  <!--
  function validateaddfamily( ) {

    if (!checkmandatoryfield(document.mainform.firstname, 'First Name')) {
      return false;
    }
    if (!checkmandatoryfield(document.mainform.family_id, 'Family', true)) {
      return false;
    }
    if (!checkmandatoryfield(document.mainform.location_id, 'Location', true)) {
      return false;
    }
    if (document.all['addressdet'].style.display== "inline") {
      if (!checkmandatoryfield(document.mainform.address1, 'Address 1')) {
        return false;
      }
      if (!checkmandatoryfield(document.mainform.town, 'Town \ City')) {
        return false;
      }
      if (!checkmandatoryfield(document.mainform.postcode, 'Postcode')) {
        return false;
      }
      if (!checkmandatoryfield(document.mainform.country_id, 'Country', true)) {
        return false;
      }

    }



    if (document.mainform.isuser.checked ) {
      if (document.mainform.email.value.indexOf('@')<0) {
         alert('All Malthouse users must have an email address. Please enter a valid email address.')
         document.mainform.email.focus()
         return false;
      }
      if (document.mainform.password != null ) {
        if (document.mainform.password.value != document.mainform.confirmpassword.value) {
           alert('Passwords do not match.')
           document.mainform.password.select()
           document.mainform.password.focus()
           return false;
        }
      }
    }
    return true;
  }

  function showaddress() {
    if ( document.all) {
      if (document.mainform.location_id.selectedIndex==1) {
        if (document.all['addressdet'].style.display == 'none') {
          document.all['addressdet'].style.display = "inline";
		  document.mainform.address1.focus();
        }
        else {
          document.all['addressdet'].style.display= "none";
        }
      }
      else {
        document.all['addressdet'].style.display= "none";
      }
    }
    else {
      alert('Function not supported by browser');
    }
    return true;
  }

  function showpassword() {
    if ( document.all ) {
      if (document.all['passworddet'] != null) {
        if (document.all['passworddet'].style.display == 'none') {
          document.all['passworddet'].style.display = "inline";
          document.mainform.password.focus();
        }
        else {
          document.mainform.password.value='';
          document.mainform.confirmpassword.value='';
          document.all['passworddet'].style.display= "none";
        }
      }
    }
    else {
      alert('Function not supported by browser');
    }
    return true;
  }


  function loadform() {
        var todaydate = new Date();
        document.mainform.today.value = formatDate(todaydate, 'd/MM/yyyy');

      <?if ($_POST['location_id']==-1) {?>
        document.mainform.location_id.selectedIndex=1
        showaddress();
      <?}?>
      <?if ($_POST['isuser']==1) {?>
        showpassword();
      <?}?>
      document.mainform.firstname.focus()
  }
  //-->
  </script>

</head>

<body onload="loadform();">
<form name="mainform" action="familymember_add.php" method="post" onsubmit="return validateaddfamily()">
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
            <td width="562" align="left" ><h2>Add or Update Family Member Details</h2></td>
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
              <table width="100%" cellpadding="5" cellspacing="0" border="0">
                <tr valign="top" colspan="3">
                   <td align="left" colspan="3"><b>(<span class="error">*Mandatory fields</span>).</b>
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
if (!$family && $famid!='')  { ?>
                <tr valign="middle">
                  <td align="left" colspan="3">Invalid family id</td>
                </tr>
<?}
else {
    if ($family) {
	  $row = mysql_fetch_assoc($family);
	}
}
?>


                <tr valign="top">
                  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
                </tr>
<?if ($famid!='') { ?>
                <tr valign="top">
                  <td align="left" width="150"><b>Created By:</b></td>
                  <td align="left" width="400"><? echo getformvalue('createdisplayname', $row, !$family)?><input type="hidden" value="<? echo getformvalue('displayname', $row, !$family)?>" name="displayname"></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Date Created:</b></td>
                  <td align="left" width="400"><? echo displaytime(getformvalue('createdate', $row, !$family))?><input type="hidden" value="<? echo getformvalue('createdate', $row, !$family)?>" name="createdate"></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
<?}?>
                <tr valign="top">
                  <td align="left" width="150"><b>First Names:</b></td>
                  <td align="left" width="400"><? createinput_text("firstname", getformvalue('firstname', $row, !$family ), "30", "50")?>&nbsp;<span class="error">*</span></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Family:</b></td>
                  <td align="left" width="400"><? createinput_select("Family", "description", getformvalue('family_id', $row, !$family ))?>&nbsp;<span class="error">*</span></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" colspan="3">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tr valign="top">
                        <td align="left" width="150"><b>Address:</b></td>
                        <td align="left" width="400"><img src="image/spacer.gif" border="0" width="9" height="1" ><? createinput_select_location("location", getformvalue('location_id', $row, !$family ), 'showaddress()')?>&nbsp;<span class="error">*</span></td>
                        <td align="left" width="50">&nbsp;</td>
                      </tr>
                    </table>
                    <div id='addressdet' style="display:none">
                      <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr valign="top">
                          <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
                        </tr>
                        <tr valign="top">
                          <td align="left" width="150"><b>Address 1:</b></td>
                          <td align="left" width="400"><img src="image/spacer.gif" border="0" width="8" height="1" ><? createinput_text("address1", getformvalue('address1', $row, !$family ), "30", "100")?>&nbsp;<span class="error">*</span></td>
                          <td align="left" width="50">&nbsp;</td>
                        </tr>
                        <tr valign="top">
                          <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
                        </tr>
                        <tr valign="top">
                          <td align="left" width="150"><b>Address 2:</b></td>
                          <td align="left" width="400"><img src="image/spacer.gif" border="0" width="8" height="1" ><? createinput_text("address2", getformvalue('address2', $row, !$family ), "30", "100")?>&nbsp;</td>
                          <td align="left" width="50">&nbsp;</td>
                        </tr>
                        <tr valign="top">
                          <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
                        </tr>
                        <tr valign="top">
                          <td align="left" width="150"><b>Town \ City:</b></td>
                          <td align="left" width="400"><img src="image/spacer.gif" border="0" width="8" height="1" ><? createinput_text("town", getformvalue('town', $row, !$family ), "30", "100")?>&nbsp;<span class="error">*</span></td>
                          <td align="left" width="50">&nbsp;</td>
                        </tr>
                        <tr valign="top">
                          <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
                        </tr>
                        <tr valign="top">
                          <td align="left" width="150"><b>Postcode:</b></td>
                          <td align="left" width="400"><img src="image/spacer.gif" border="0" width="8" height="1" ><? createinput_text("postcode", getformvalue('postcode', $row, !$family ), "30", "20")?>&nbsp;<span class="error">*</span></td>
                          <td align="left" width="50">&nbsp;</td>
                        </tr>
                        <tr valign="top">
                          <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
                        </tr>
                        <tr valign="top">
                          <td align="left" width="150"><b>State \ County:</b></td>
                          <td align="left" width="400"><img src="image/spacer.gif" border="0" width="8" height="1" ><? createinput_text("state", getformvalue('state', $row, !$family ), "30", "100")?>&nbsp;</td>
                          <td align="left" width="50">&nbsp;</td>
                        </tr>
                        <tr valign="top">
                          <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
                        </tr>
                        <tr valign="top">
                          <td align="left" width="150"><b>Country:</b></td>
                          <td align="left" width="400"><img src="image/spacer.gif" border="0" width="9" height="1" ><? createinput_select("Country", "country", getformvalue('country_id', $row, !$family ))?>&nbsp;<span class="error">*</span></td>
                          <td align="left" width="50">&nbsp;</td>
                        </tr>
                      </table>
                    </div>
                  </td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Display Name:</b></td>
                  <td align="left" width="400"><? createinput_text("displayname", getformvalue('displayname', $row, !$family ), "30", "50")?>&nbsp;(if different from first name)</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Email:</b></td>
                  <td align="left" width="400"><? createinput_text("email", getformvalue('email', $row, !$family ), "30", "100")?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Work Email:</b></td>
                  <td align="left" width="400"><? createinput_text("workemail", getformvalue('workemail', $row, !$family ), "30", "100")?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Mobile Telephone:</b></td>
                  <td align="left" width="400"><? createinput_text("mobilephone", getformvalue('mobilephone', $row, !$family ), "30", "50")?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Work Telephone:</b></td>
                  <td align="left" width="400"><? createinput_text("workphone", getformvalue('workphone', $row, !$family ), "30", "50")?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Skype Name:</b></td>
                  <td align="left" width="400"><? createinput_text("skypename", getformvalue('skypename', $row, !$family ), "30", "50")?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>MSN Name:</b></td>
                  <td align="left" width="400"><? createinput_text("msnname", getformvalue('msnname', $row, !$family ), "30", "50")?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                  <td align="left" colspan="3">
                    <table width="100%" cellpadding="0" cellspacing="0" border="0">
                      <tr valign="top">
                        <td align="left" width="150"><b>Create Malthouse Login?:</b></td>
                        <td align="left" width="400"><img src="image/spacer.gif" border="0" width="6" height="1" ><? createinput_checkbox("isuser", getformvalue('isuser', $row, !$family ), "showpassword()")?></td>
                        <td align="left" width="50">&nbsp;</td>
                      </tr>
                    </table>
                    <?if ($_POST['Add'] == 'Add' || $famid == '') {?>
                    <div id='passworddet' style="display:none">
                      <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr valign="top">
                          <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
                        </tr>
                        <tr valign="top">
                          <td align="left" width="150"><b>Password:</b></td>
                          <td align="left" width="400"><img src="image/spacer.gif" border="0" width="8" height="1" ><? createinput_password("password", "30", "50")?>&nbsp;<span class="error">*</span></td>
                          <td align="left" width="50">&nbsp;</td>
                        </tr>
                        <tr valign="top">
                          <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
                        </tr>
                        <tr valign="top">
                          <td align="left" width="150"><b>Confirm Password:</b></td>
                          <td align="left" width="400"><img src="image/spacer.gif" border="0" width="8" height="1" ><? createinput_password("confirmpassword", "30", "50")?>&nbsp;<span class="error">*</span></td>
                          <td align="left" width="50">&nbsp;</td>
                        </tr>
                      </table>
                    </div>
                    <?}?>
                  </td>
                </tr>
                <?if (getsessionadmin($sessionid) == '1'){?>
                <tr valign="top">
                  <td align="left" width="150"><b>Admin User?:</b></td>
                  <td align="left" width="400"><? createinput_checkbox("admin", getformvalue('admin', $row, !$family ))?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Support User?:</b></td>
                  <td align="left" width="400"><? createinput_checkbox("support", getformvalue('support', $row, !$family ))?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Management?:</b></td>
                  <td align="left" width="400"><? createinput_checkbox("management", getformvalue('management', $row, !$family ))?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <?}?>
                <tr valign="top">
                  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150">&nbsp;</td>
                  <td align="left" width="400"><? if ($famid=='') createinput_submit("Add"); else createinput_submit("Update");?>&nbsp;<? createinput_submit("Cancel", false, "viewfamily()")?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
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
