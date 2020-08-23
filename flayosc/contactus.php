<?php



	$message = "MESSAGE FROM=" . $_POST['fullname'] . "\r\n";
	$message .= "EMAIL=" . $_POST['email'] . "\r\n";
	$message .= "DATE=" . date("h:i:sa D-d-M-Y") . "\r\n\r\n";
	$message .= "MESSAGE=" . $_POST['message'] . "\r\n";



	//Now send a mail

	$to      = 'stevemackee@btconnect.com';
	$subject = 'Website Message from www.flayosc.co.uk';
	$headers = 'From: ' . $_POST['email'] . "\r\n" .
	    'CC: ' . $cc . "\r\n" ;
	    'Reply-To: ' . $_POST['email'] . "\r\n" ;

	mail($to, $subject, $message, $headers);

	//Now mail the sender
	$to      = $_POST['email'];
	$subject = 'AUTO REPLY: SPM France - Flayosc' ;
	$message = 'Thankyou for sending a message to SPM.  You will receive a reply shortly.'. "\r\n" .
				'You sent the following message to SPM France: '. "\r\n\r\n" .
				$_POST['message'];
	$headers = 'From: noreply@flayosc.co.uk' . "\r\n" .
	    'Reply-To: noreply@flayosc.co.uk' . "\r\n" ;


	mail($to, $subject, $message, $headers);


?>

<html>
<head>
<title>SPM France - Contact Us</title>
<link rel="stylesheet" href="style/flayosc.css">

</head>

<body bgcolor="#90CAC9">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
  <!--menu start-->
  <tr>
    <td align="center">
      <table cellpadding="0" cellspacing="0" border="0" width="800">
        <tr valign="middle">
          <td class="main"  align="center">
            <img src="image/spacer.jpg" border="0" width="0" height="50"></a>
          </td>
        </tr>
      </table>
      <table cellpadding="2" cellspacing="0" border="0" width="800">
        <tr valign="middle">
          <td class="menu" align="center" width="75"><a href="index.html">Home</a></td>
          <td class="menu" align="center" width="75"><a href="http://picasaweb.google.com/JWood225/Flayosc" target="_blank">Photo</a></td>
          <td class="menu" align="center" width="100"><a href="http://www.ville-flayosc.fr/uk/discover/discover.htm" target="_blank">Local Area</a></td>
          <td class="menu" align="center" width="100"><a href="contactus.html">Contact SPM</a></td>
          <td class="menu" align="center">&nbsp;</td>
        </tr>
      </table>
    </td>
  </tr>
  <!--menu end-->
  <tr>
    <td align="center">
  	  <table cellpadding="0" cellspacing="0" border="0" >
	    <tr>
          <td class="main">
			<table width="800" cellpadding="2" cellspacing="2" border="0" >
				<tr valign="top" height="10" >
					<td>&nbsp;</td>
				</tr>
				<tr valign="top" height="350" >
					<td align="center" class="submenu">
						<h2>Message Sent</h2>

					<table width="530" border="0" cellpadding="2" cellspacing="0">
					<tr>
					  <td  align="center">Your message has been sent to SPM France.</td>
					</tr>
					</table>
					</td>
				</tr>
			</table>
          </td>
	    </tr>
	  </table>
    </td>
  </tr>

  <!--footer start-->
  <tr>
    <td align="center">
      <table cellpadding="2" cellspacing="0" border="0" width="800">
        <tr valign="middle">
          <td class="footer" align="center"><a href="www.softwarejewels.com" title="site created by Software Jewels">www.softwarejewels.com</a></td>
        </tr>
      </table>
    </td>
  </tr>
  <!--footer end-->
</table>
<
</body>

</html>