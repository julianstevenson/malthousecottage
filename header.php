<?php  if (!$hidemenus) { php?>
<script language="JavaScript" src="script/menu.js"></script>
<script language="JavaScript" src="script/menu_items.js"></script>
<script language="JavaScript" src="script/menu_tpl.js"></script>
<link rel="stylesheet" href="style/menu.css">
<?}?>


<table align="left" width="800" cellpadding="0" cellspacing="0" border="0">
<tr valign="bottom">
<td class="headermain">
  <table width="800" cellpadding="2" cellspacing="0" border="0">
    <tr valign="bottom">
      <td background="image/mhheader.jpg" align="left"><img src="image/spacer.gif" border="0" width="1" height="60" ></td>
    </tr>
    <tr valign="middle" height="25">
      <td class="mhmenu">&nbsp;
<?  if (!$hidemenus) {?>
		<script language="JavaScript">
			new menu (MENU_ITEMS, MENU_TPL);
		</script>
<?}?>
</td>
    </tr>
  </table>
