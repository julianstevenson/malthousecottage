<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";

$famid = trim($_POST['famid']);

if (trim($famid) == '') {
  $famid = trim($_GET['famid']);
}

if ($_POST['Add'] == 'Add') {

      $query = sprintf( 'insert into family (familymember_id, description) select fm.id, %s from familymember fm where email=%s' ,
          sqlsafe($_POST['description']),
          sqlsafe(getsessionemail($sessionid))
          );
      mhexecquery($query, $err);
  	  if ($err=='') {
  	  		Header("Location: family.php?msg=" . urlencode("Family Added Successfuly") . "&sess=" . urlencode($sessionid)) ;
      }

}
elseif ($_POST['Update'] == 'Update') {
      $query = sprintf( 'update family set description=%s where id=%s' ,
          sqlsafe($_POST['description']),
          $famid
          );
  	  if (mhexecquery($query, $err)) {
  	  		Header("Location: family.php?msg=" . urlencode("Family Updated Successfuly") . "&sess=" . urlencode($sessionid)) ;
      }
}
elseif ($famid !== '') {

  $query = sprintf( "select fm.displayname, fm.email, fam.id as family_id, fam.createdate, fam.description from family fam left outer join familymember fm on fam.familymember_id=fm.id where fam.id=%s" ,
          $famid);
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
  <title>Malthouse Cottage - Family</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhvalidate.js"></script>
  <script>
  <!--
  function validateaddfamily( ) {

    if (!checkmandatoryfield(document.mainform.description, 'Family Name')) {
      return false;
    }

    return true;
  }
  //-->
  </script>

</head>

<body onload="document.mainform.description.focus()">
<form name="mainform" action="family_add.php" method="post" onsubmit="return validateaddfamily()">
<input name="sess" value="<? echo htmlspecialchars($sessionid) ?>" type="hidden">
<input name="famid" value="<? echo htmlspecialchars($famid) ?>" type="hidden">
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
            <td width="562" align="left" ><h2><? if ($famid=='') echo "New Family"; else echo "Update Family";?></h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b><? if ($famid=='') echo "Create a new family"; else echo "Update family";?></b></td>
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
						   <td align="left" colspan="3"><b>Enter the Family name and click the 'Add' button.  (<span class="error">*Mandatory fields</span>).</b>
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
						  <td align="left" colspan="6">Invalid family id</td>
						</tr>
		<?}
		elseif($family) {
			$row = mysql_fetch_assoc($family);
		}
		?>


						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
		<?if ($famid!='') { ?>
						<tr valign="top">
						  <td align="left" width="150"><b>Created By:</b></td>
						  <td align="left" width="400"><? echo getformvalue('displayname', $row, !$family)?><input type="hidden" value="<? echo getformvalue('displayname', $row, !$family)?>" name="displayname"></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Date Created:</b></td>
						  <td align="left" width="400"><? echo displaytime(getformvalue('createdate', $row, !$family))?><input type="hidden" value="<? echo getformvalue('createdate', $row, !$family)?>" name="createdate"></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
		<?}?>
						<tr valign="top">
						  <td align="left" width="150"><b>Name of Family:</b></td>
						  <td align="left" width="250"><? createinput_text("description", getformvalue('description', $row, !$family ), "30", "100")?>&nbsp;<span class="error">*</span></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
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
