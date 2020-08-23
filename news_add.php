<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";
$newsid = trim($_POST['newsid']);

if (trim($newsid) == '') {
  $newsid = trim($_GET['newsid']);
}

if ($_POST['fn'] == 'Create') {
      //Create the message
      $query = sprintf( 'insert into news (familymember_id, title, news, archive) select fm.id,  %s, %s, 0 from familymember fm where email=%s' ,
          sqlsafe($_POST['title']),
          sqlsafe($_POST['news']),
          sqlsafe(getsessionemail($sessionid))
          );

      mhexecquery($query, $err);

}
elseif ($_POST['fn'] == 'Update') {
      //Create the message
      $query = sprintf( 'Update news set title=%s, news=%s where news_id=%s' ,
          sqlsafe($_POST['title']),
          sqlsafe($_POST['news']),
          sqlsafe($_POST['newsid'])
          );

      mhexecquery($query, $err);

}

if ($newsid !== '') {

  $query = sprintf( "select fm.displayname, fm.email, n.news_id as news_id, n.createdate, n.title, n.news from news n left outer join familymember fm on n.familymember_id=fm.id where n.news_id=%s" ,
          $newsid);
  $news = mhexecquery($query, $err);

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
  <title>Malthouse Cottage - Add News</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhvalidate.js"></script>
  <script>
  <!--
  function validateaddfamily( ) {

    if (!checkmandatoryfield(document.mainform.title, 'Title')) {
      return false;
    }
    if (!checkmandatoryfield(document.mainform.news, 'News')) {
      return false;
    }

    return true;
  }
  //-->
  </script>

</head>

<body onload="document.mainform.title.focus()">
<form name="mainform" action="news_add.php" method="post" onsubmit="return validateaddfamily()">
<input name="sess" value="<? echo htmlspecialchars($sessionid) ?>" type="hidden">
<input name="newsid" value="<? echo htmlspecialchars($newsid) ?>" type="hidden">
<? if ($newsid !== '') { ?>
<input name="fn" value="Update" type="hidden">
<? } else { ?>
<input name="fn" value="Create" type="hidden">
<? }  ?>

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
            <td width="562" align="left" ><h2>New News Item</h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>
                   		<? if ($newsid !== '') { ?>
						Update
						<? } else { ?>
						Create a
						<? }  ?>
					News Item</b></td>
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
						   <td align="left" colspan="3"><b>Enter the news item.  The news will appear on the malthouse cottage home page.(<span class="error">*Mandatory fields</span>).</b>
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
		if (!$news && $newsid!='')  { ?>
						<tr valign="middle">
						  <td align="left" colspan="6">Invalid news id</td>
						</tr>
		<?}
		else {
			if ($news) {
			  $row = mysql_fetch_assoc($news);
			}
		}
		?>


						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
		<?if ($newsid!='') { ?>
						<tr valign="top">
						  <td align="left" width="150"><b>Created By:</b></td>
						  <td align="left" width="400"><? echo getformvalue('displayname', $row, !$news)?><input type="hidden" value="<? echo getformvalue('displayname', $row, !$news)?>" name="displayname"></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>Date Created:</b></td>
						  <td align="left" width="400"><? echo displaytime(getformvalue('createdate', $row, !$news))?><input type="hidden" value="<? echo getformvalue('createdate', $row, !$news)?>" name="createdate"></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
		<?}?>
						<tr valign="top">
						  <td align="left" width="150"><b>Title:</b></td>
						  <td align="left" width="400"><? createinput_text("title", getformvalue('title', $row, !$news), "30", "50")?>&nbsp;<span class="error">*</span></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150"><b>News:</b></td>
						  <td align="left" width="400"><? createinput_textarea("news", getformvalue('news', $row, !$news), "10", "40")?>&nbsp;<span class="error">*</span></td>
						  <td align="left" width="50">&nbsp;</td>
						</tr>
						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
						</tr>
						<tr valign="top">
						  <td align="left" width="150">&nbsp;</td>
						  <td align="left" width="400">
							<? if ($newsid !== '') {
							createinput_submit("Update");
							 } else {
							createinput_submit("Create");
							 }
						   ?>&nbsp;<? createinput_submit("Cancel", false, "viewmessages()")?></td>
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
