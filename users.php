<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";
$msg = $_GET['msg'];


if ($_POST['act'] == 'del') {
  if ($_POST['rowid'] == '') {
    $err = "No row id supplied";
  }
  else {
    $query = sprintf( 'update familymember set isuser=0 where id = %s' ,
          $_POST['rowid']);

    if (!mhexecquery($query, $err)) {
       //
    }
    else {
      $msg = "User deleted";
    }
  }

}

$famidsql = '';
if (trim($_POST['family_id']) !== '') {
  $famidsql = " and fm.family_id=" . trim($_POST['family_id']);
}

$query = "select f.description as familyname, fm.isuser, f.id as family_id, case fm.admin when 1 then 'Yes' else 'No' end as admin , case fm.management when 1 then 'Yes' else 'No' end as management, case fm.support when 1 then 'Yes' else 'No' end as support , fm.id as familymember_id, fm.displayname, fm.email, fm.mobilephone from familymember fm inner join family f on fm.family_id=f.id and fm.deleted=0 and fm.isuser=1 " . $famidsql  . " order by displayname, familyname asc";
$family = mhexecquery($query, $err);



function outputfamilyrow($row, &$familyname, $sessionid) {

  echo '<tr valign="middle">' . "\n";
  echo '<td align="left" class="listitem">' . htmlspecialchars($row['displayname']) . '&nbsp;</td>' . "\n";
  echo '<td align="center" class="listitem">' . htmlspecialchars($row['admin']) . '&nbsp;</td>' . "\n";
  echo '<td align="center" class="listitem">' . htmlspecialchars($row['management']) . '&nbsp;</td>' . "\n";
  echo '<td align="center" class="listitem">' . htmlspecialchars($row['support']) . '&nbsp;</td>' . "\n";
  if (getsessionadmin($sessionid)==1) {
     echo '<td align="center" class="listitem" >' . showlink('edit', getsessionadmin($sessionid), $row['email'], getsessionemail($sessionid), $row['familymember_id']) . '&nbsp;</td>' . "\n";
     echo '<td align="center" class="listitem" >' . showlink('delete', getsessionadmin($sessionid), $row['email'], getsessionemail($sessionid), $row['familymember_id']) . '&nbsp;</td>' . "\n";
  }
  else {
     echo '<td align="center" class="listitem" >' . showlink('view', getsessionadmin($sessionid), $row['email'], getsessionemail($sessionid), $row['familymember_id']) . '&nbsp;</td>' . "\n";
  }
  echo '</tr>' . "\n";
}

function showlink($linkaction, $admin, $rowemail, $sessionemail, $rowid) {
  $htmlstr = "&nbsp;";
  if ($linkaction=='edit') {
       $htmlstr = '<a href="javascript:edituser(' . $rowid . ')" title="Update User details"><img src="image/edit.gif" width="18" height="15" border="0"></a>';
  }
  elseif ($linkaction=='view') {
       $htmlstr = '<a href="javascript:viewuser(' . $rowid . ')" title="View User details"><img src="image/view.png" width="24" height="24" border="0"></a>';
  }
  if ($rowemail == $sessionemail || $admin==1) {
    if ($linkaction=='delete') {
       $htmlstr = '<a href="javascript:deleteuser(' . $rowid . ')" title="Delete User"><img src="image/remove.gif" width="11" height="11" border="0"></a>';
    }
  }
  return $htmlstr;
}

?>
<html>
<head>
  <title>Malthouse Cottage - Users</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhvalidate.js"></script>
  <script>
  <!--

    function deleteuser(famid) {
      if (confirm('Are you sure you would like to delete this user?') ) {
        document.mainform.rowid.value = famid;
        document.mainform.act.value = 'del';
        document.mainform.submit();
      }
    }


 function edituser(famid) {
    	document.location.href='user_add.php?famid=' + famid + '&sess=' + escape(getsession());
    }

 function viewuser(famid) {
    	document.location.href='user_view.php?famid=' + famid + '&sess=' + escape(getsession());
    }

  //-->
  </script>
</head>

<body>
<form name="mainform" action="users.php" method="post" onsubmit="return validatelogin()">
<input name="sess" value="<? echo htmlspecialchars($sessionid) ?>" type="hidden">
<input name="family_id" value="" type="hidden">
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
            <td width="562" align="left" ><h2>Malthouse Cottage Users</h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>Users</b></td>
                   <td align="right" class="listboxheadingright">
							<a href="javascript:adduser()" title="Add a new user">Add New User</a>
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
		<?if ($msg != '')  {?>
						<tr valign="middle">
						  <td align="left" colspan="7" class="message"><? echo $msg?></td>
						</tr>
        <?}?>
                <tr valign="top">
                  <td align="center">
					  <table width="100%" cellpadding="5" cellspacing="0" border="0">

						<tr valign="middle">
						  <td align="left" class="listboxheading"><b>Name</b></td>
						  <td align="center" class="listboxheading"><b>Admin</b></td>
						  <td align="center" class="listboxheading"><b>Management</b></td>
						  <td align="center" class="listboxheading"><b>Support</b></td>
<?if (getsessionadmin($sessionid)==1) { ?>
						  <td align="center" class="listboxheading"><b>Change</b></td>
						  <td align="center" class="listboxheading"><b>Remove</b></td>
<?}
else {?>
						  <td align="center" class="listboxheading"><b>View</b></td>
<?}?>

						</tr>
		<? if (!$family)  { ?>
						<tr valign="middle">
						  <td align="left" colspan="6">No family</td>
						</tr>
		<? }
		else if ($err == '') {
				$familyname = '';
				$rowcount = 0;
				while ($row = mysql_fetch_assoc($family)) {
				  $rowcount .= 1;
				  outputfamilyrow($row, $familyname, $sessionid);
				}
				if ($rowcount == 0 ){?>
						<tr valign="middle">
						  <td align="left" colspan="6">No family members</td>
						</tr>

		<?        }
		}?>


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



