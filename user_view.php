<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";

$famid = trim($_POST['famid']);

if (trim($famid) == '') {
  $famid = trim($_GET['famid']);
}
if ($famid !== '') {

  $query = sprintf( "select fm.displayname as createdisplayname, family.description as familyname, l.address1, l.address2, l.town, l.postcode, l.state, c.country, fm.email, fam.createdate, fam.firstname, fam.displayname, fam.family_id, fam.email, fam.password, fam.location_id, fam.workphone, fam.mobilephone, fam.skypename, fam.msnname, fam.workemail, fam.isuser, fam.admin, fam.management, fam.support from familymember fam left outer join familymember fm on fam.familymember_id=fm.id join family family on fam.family_id=family.id join location l on l.id=fam.location_id left  join country c on c.id=l.country_id where fam.id=%s" ,
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
  <title>Malthouse Cottage - User</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhdate.js"></script>
  <script src="script/mhvalidate.js"></script>
  <script src="script/dateselector.js"></script>
  <script>
  <!--

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


   //-->
  </script>

</head>

<body >
<form name="mainform" action="user_add.php" method="post" onsubmit="return validateaddfamily()">
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
            <td width="562" align="left" ><h2>User Details</h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>User Details</b></td>
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
                  <td align="left" width="400"><? echo getformvalue('createdisplayname', $row, !$family)?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Date Created:</b></td>
                  <td align="left" width="400"><? echo displaytime(getformvalue('createdate', $row, !$family))?></td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
<?}?>
                <tr valign="top">
                  <td align="left" width="150"><b>First Names:</b></td>
                  <td align="left" width="400"><? echo getformvalue('firstname', $row, !$family )?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Family:</b></td>
                  <td align="left" width="400"><? echo getformvalue('familyname', $row, !$family )?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" colspan="3">
                      <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr valign="top">
                          <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
                        </tr>
                        <tr valign="top">
                          <td align="left" width="150"><b>Address 1:</b></td>
                          <td align="left" width="400"><img src="image/spacer.gif" border="0" width="8" height="1" ><? echo getformvalue('address1', $row, !$family )?>&nbsp;</td>
                          <td align="left" width="50">&nbsp;</td>
                        </tr>
                        <tr valign="top">
                          <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
                        </tr>
                        <tr valign="top">
                          <td align="left" width="150"><b>Address 2:</b></td>
                          <td align="left" width="400"><img src="image/spacer.gif" border="0" width="8" height="1" ><? echo getformvalue('address2', $row, !$family )?>&nbsp;</td>
                          <td align="left" width="50">&nbsp;</td>
                        </tr>
                        <tr valign="top">
                          <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
                        </tr>
                        <tr valign="top">
                          <td align="left" width="150"><b>Town \ City:</b></td>
                          <td align="left" width="400"><img src="image/spacer.gif" border="0" width="8" height="1" ><? echo getformvalue('town', $row, !$family )?>&nbsp;</td>
                          <td align="left" width="50">&nbsp;</td>
                        </tr>
                        <tr valign="top">
                          <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
                        </tr>
                        <tr valign="top">
                          <td align="left" width="150"><b>Postcode:</b></td>
                          <td align="left" width="400"><img src="image/spacer.gif" border="0" width="8" height="1" ><? echo getformvalue('postcode', $row, !$family )?>&nbsp;</td>
                          <td align="left" width="50">&nbsp;</td>
                        </tr>
                        <tr valign="top">
                          <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
                        </tr>
                        <tr valign="top">
                          <td align="left" width="150"><b>State \ County:</b></td>
                          <td align="left" width="400"><img src="image/spacer.gif" border="0" width="8" height="1" ><? echo getformvalue('state', $row, !$family )?>&nbsp;</td>
                          <td align="left" width="50">&nbsp;</td>
                        </tr>
                        <tr valign="top">
                          <td align="left"><img src="image/spacer.gif" border="0" width="1" height="10" ></td>
                        </tr>
                        <tr valign="top">
                          <td align="left" width="150"><b>Country:</b></td>
                          <td align="left" width="400"><img src="image/spacer.gif" border="0" width="9" height="1" ><? echo getformvalue('country_id', $row, !$family )?>&nbsp;</td>
                          <td align="left" width="50">&nbsp;</td>
                        </tr>
                      </table>
                  </td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Display Name:</b></td>
                  <td align="left" width="400"><? echo getformvalue('displayname', $row, !$family )?>&nbsp;(if different from first name)</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Email:</b></td>
                  <td align="left" width="400"><?  getformvalue('email', $row, !$family )?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Work Email:</b></td>
                  <td align="left" width="400"><? echo getformvalue('workemail', $row, !$family )?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Mobile Telephone:</b></td>
                  <td align="left" width="400"><? echo getformvalue('mobilephone', $row, !$family )?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Work Telephone:</b></td>
                  <td align="left" width="400"><? echo getformvalue('workphone', $row, !$family )?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Skype Name:</b></td>
                  <td align="left" width="400"><? echo getformvalue('skypename', $row, !$family )?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>MSN Name:</b></td>
                  <td align="left" width="400"><? echo getformvalue('msnname', $row, !$family )?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                  <td align="left" colspan="3">
                   
                  </td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Admin User?:</b></td>
                  <td align="left" width="400"><? echo (getformvalue('admin', $row, !$family ) == '1' )? 'Yes': 'No' ?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Support User?:</b></td>
                  <td align="left" width="400"><? echo (getformvalue('support', $row, !$family ) == '1' )? 'Yes': 'No' ?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150"><b>Management?:</b></td>
                  <td align="left" width="400"><? echo (getformvalue('management', $row, !$family ) == '1' )? 'Yes': 'No' ?>&nbsp;</td>
                  <td align="left" width="50">&nbsp;</td>
                </tr>
                <tr valign="top">
                  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
                </tr>
                <tr valign="top">
                  <td align="left" width="150">&nbsp;</td>
                  <td align="left" width="400"><? createinput_submit("Back", false, "viewusers()")?></td>
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
