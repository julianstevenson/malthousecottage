<?
include 'dbutils.php';
include 'utils.php';
include 'validateuser.php';

$err = "";
$msg = $_GET['msg'];


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
    $query = sprintf( "update messagefamilymember set deleted=1 where id = %s" ,
          $_POST['rowid']);

    if (!mhexecquery($query, $err)) {
       //
    }
    else {
      $msg = "Message deleted";
    }
  }

}
elseif ($_POST['act'] == 'archive') {
  if ($_POST['rowid'] == '') {
    $err = "No row id supplied";
  }
  else {
    $query = sprintf( "update news set archive =1 where news_id = %s" ,
          $_POST['rowid']);

    if (!mhexecquery($query, $err)) {
       //
    }
    else {
      $msg = "News item archived";
    }
  }

}
elseif ($_POST['act'] == 'unarchive') {
  if ($_POST['rowid'] == '') {
    $err = "No row id supplied";
  }
  else {
    $query = sprintf( "update news set archive =0 where news_id = %s" ,
          $_POST['rowid']);

    if (!mhexecquery($query, $err)) {
       //
    }
    else {
      $msg = "News item activated";
    }
  }

}



$query = sprintf( "select * from news" ,
          sqlsafe(getsessionemail($sessionid)));
$latestnews = mhexecquery($query, $err);


function outputnewsrow($row, $highlightunread) {
  $sbold = "";
  $ebold = "";
  echo '<tr valign="middle">' . "\n";
  echo '<td align="left" class="listitem" width="200">' .  displaymsgtime($row['createdate'])   . '&nbsp;</td>' . "\n";
  echo '<td align="left" class="listitem"  width="450">'  . '<a href="javascript:viewnews(' . $row['news_id'] . ')" title="' . htmlspecialchars($row['news']) . '">' . htmlspecialchars($row['title'])  . '&nbsp;</td>' . "\n";

  if ($row['archive'] == 0 ) {
	  echo '<td align="center" class="listitem" ><a href="javascript:archive(' . $row['news_id'] . ')" title="Archive News">Archive</a></td>' . "\n";
  }
  else {
	  echo '<td align="center" class="listitem" ><a href="javascript:unarchive(' . $row['news_id'] . ')" title="Archive News">Activate</a></td>' . "\n";
  }


  echo '</tr>' . "\n";


}
?>


<html>
<head>
  <title>Malthouse Cottage - Latest News</title>
  <link rel="stylesheet" href="style/malthouse.css">
  <script src="script/mhlinks.js"></script>
  <script src="script/mhdate.js"></script>
  <script src="script/mhvalidate.js"></script>
  <script src="script/dateselector.js"></script>
  <script>
  <!--


  function archive(id) {
	if (confirm('Archiving the news item will remove it from the home page. Continue?') ) {
	  document.mainform.rowid.value = id;
	  document.mainform.act.value = 'archive';
	  document.mainform.submit();
	}
  }
  function unarchive(id) {
	if (confirm('This action will add the news to the home page. Continue?') ) {
	  document.mainform.rowid.value = id;
	  document.mainform.act.value = 'unarchive';
	  document.mainform.submit();
	}
  }

    function viewnews(newsid) {
    	document.location.href='news_add.php?newsid=' + newsid + '&sess=' + escape(getsession());
    }

//-->
  </script>

</head>
<body>
<form name="mainform" action="latestnews.php" method="post">
<input name="sess" value="<? echo htmlspecialchars($sessionid) ?>" type="hidden">
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
            <td width="562" align="left" ><h2>Latest News</h2></td>
            <td width="18">&nbsp;</td>
          </tr>
          <tr valign="top">
            <td width="20">&nbsp;</td>
            <td width="562" align="left" >
               <table width="562" cellpadding="5" cellspacing="0" border="0">
                 <tr valign="top">
                   <td width="220" align="center" class="listboxheadingleft"><b>News</b></td>
                   <td align="right" class="listboxheadingright"><a href="javascript:createnews()" title="Create a news item">Create a News Item</a>&nbsp;

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
                <?if ($everr !== '' ) {?>
                <tr valign="top">
                  <td colspan="3" align="center" class="error"><? echo $everr ?></td>
                </tr>
                <?}?>
                <tr valign="top">
                  <td align="left">
					  <table width="100%" cellpadding="5" cellspacing="0" border="0">
						<tr valign="middle">
						  <td align="left" width="200" class="listboxheading"><b>Date Created</b></td>
						  <td align="left" width="450" class="listboxheading"><b>Title</b></td>
						  <td align="left" class="listboxheading"><b>Archive</b></td>
						</tr>
		<? if (!$latestnews)  { ?>
						<tr valign="middle">
						  <td align="left" colspan="2">No news items</td>
						</tr>
		<? }
		else if ($err == '') {
				$rowcount = 0;
				while ($row = mysql_fetch_assoc($latestnews)) {
				  $rowcount .= 1;
				  outputnewsrow($row, true);
				}
				if ($rowcount == 0) {?>

						<tr valign="middle">
						  <td align="left" colspan="2" class="error">No News</td>
						</tr>

		<?	    }
		}

		?>
						<tr valign="top">
						  <td align="left"><img src="image/spacer.gif" border="0" width="1" height="2" ></td>
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
