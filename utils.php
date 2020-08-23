<?
include 'config.php';

function encryptsession($sessionid) {
	$crypt = new encryption_class;


	$encrypt_result = $crypt->encrypt('hello', $sessionid);
	$encrypt_result = $sessionid;

    return $encrypt_result;
}

function decryptsession($sessionid) {


	$crypt = new encryption_class;
	$decrypt_result = $crypt->decrypt('hello', $sessionid);
	$decrypt_result = $sessionid;


	return $decrypt_result;
}


function mhreadfilearray($file_name, &$fileerr) {
    global $CONFIG_PATH;
	$file_name = $CONFIG_PATH . "/" . $file_name;
    if (!($fp = fopen ($file_name, "r"))) {
		$fileerr = "Unable to open the input file: " . $file_name;
		return $fileerr ;
    }
    else {
		fclose ($fp);
	}

	$result = file($file_name);


	return $result;

}

function inputdate($date) {
   if (trim($date) == '') return '';
   if (!strpos($date, '/')) {
     return date('d/m/Y', strtotime($date));	
   }
   else {
     return substr($date, 0, 2) . '/' . substr($date, 3, 2) . '/' . substr($date, 6, 4);
   }

}

function eventdate($day, $month, $year="", $annual="1") {
   if (trim($annual) == '1') {
     return date('j M', strtotime($month . '/' . $day . '/2006'));
   }
   else {
     return date('j M', strtotime($month . '/' . $day . '/' . $year));
   }

}

function displaydate($date, $showweekday=true) {
   if ($showweekday) {
     return date('D, j M y', strtotime($date));
   }
   else {
     return date('j M y', strtotime($date));
   }
}

function tooltipdate($date) {


   return date('D, jS M Y', strtotime($date));
}

function safedate($date) {

   return substr($date, 6, 4) . '-' . substr($date, 3, 2) . '-' . substr($date, 0, 2);
}

function displaytime($date) {
     return date('D, j M Y \a\t h:i:s A', strtotime($date));
}

function displaymsgtime($date, $short=false) {

   if ($short) {
     return date('j M y', strtotime($date));
   }
   else {
     return date('j M y h:i A', strtotime($date));
   }
}


function mhreadfile($file_name, &$fileerr) {
    global $CONFIG_PATH;
	$file_name = $CONFIG_PATH . "/" . $file_name;
    if (!($fp = fopen ($file_name, "r"))) {
		$fileerr = "Unable to open the input file: " . $file_name ;
		return $fileerr ;
    }

	$result = fread($fp, filesize($file_name));
	fclose ($fp);


	return $result;

}


function mhlogtofile($file_name, $logentry) {
    global $CONFIG_PATH;
	$file_name = $CONFIG_PATH . "/" . $file_name;
    if (!($fp = fopen ($file_name, "w"))) {
    	//non fatal error - continue
    	return;
    }

	fwrite($fp, $logentry);
	fclose($fp );
	return;
}

function getsessionuser($sessionid) {
    list($email, $user, $logintime, $admin) =  split('[|]', decryptsession($sessionid));
	return trim($user);
}

function getsessionemail($sessionid) {
    list($email, $user, $logintime, $admin) =  split('[|]', decryptsession($sessionid));
	return trim($email);
}


function getsessiontime($sessionid) {
    list($email, $user, $logintime, $admin) =  split('[|]', decryptsession($sessionid));
	return trim($logintime);
}

function getsessionadmin($sessionid) {
	$admin = '0';
    list($email, $user, $logintime, $admin) =  split('[|]', decryptsession($sessionid));
	return trim($admin);
}

function datediff($interval, $datefrom, $dateto, $using_timestamps = false) {
  /*
    $interval can be:
    yyyy - Number of full years
    q - Number of full quarters
    m - Number of full months
    y - Difference between day numbers
      (eg 1st Jan 2004 is "1", the first day. 2nd Feb 2003 is "33". The datediff is "-32".)
    d - Number of full days
    w - Number of full weekdays
    ww - Number of full weeks
    h - Number of full hours
    n - Number of full minutes
    s - Number of full seconds (default)
  */

  if (!$using_timestamps) {
    $datefrom = strtotime($datefrom, 0);
    $dateto = strtotime($dateto, 0);
  }
  $difference = $dateto - $datefrom; // Difference in seconds

  switch($interval) {

    case 'yyyy': // Number of full years

      $years_difference = floor($difference / 31536000);
      if (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom), date("j", $datefrom), date("Y", $datefrom)+$years_difference) > $dateto) {
        $years_difference--;
      }
      if (mktime(date("H", $dateto), date("i", $dateto), date("s", $dateto), date("n", $dateto), date("j", $dateto), date("Y", $dateto)-($years_difference+1)) > $datefrom) {
        $years_difference++;
      }
      $datediff = $years_difference;
      break;

    case "q": // Number of full quarters

      $quarters_difference = floor($difference / 8035200);
      while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($quarters_difference*3), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
        $months_difference++;
      }
      $quarters_difference--;
      $datediff = $quarters_difference;
      break;

    case "m": // Number of full months

      $months_difference = floor($difference / 2678400);
      while (mktime(date("H", $datefrom), date("i", $datefrom), date("s", $datefrom), date("n", $datefrom)+($months_difference), date("j", $dateto), date("Y", $datefrom)) < $dateto) {
        $months_difference++;
      }
      $months_difference--;
      $datediff = $months_difference;
      break;

    case 'y': // Difference between day numbers

      $datediff = date("z", $dateto) - date("z", $datefrom);
      break;

    case "d": // Number of full days

      $datediff = floor($difference / 86400);
      break;

    case "w": // Number of full weekdays

      $days_difference = floor($difference / 86400);
      $weeks_difference = floor($days_difference / 7); // Complete weeks
      $first_day = date("w", $datefrom);
      $days_remainder = floor($days_difference % 7);
      $odd_days = $first_day + $days_remainder; // Do we have a Saturday or Sunday in the remainder?
      if ($odd_days > 7) { // Sunday
        $days_remainder--;
      }
      if ($odd_days > 6) { // Saturday
        $days_remainder--;
      }
      $datediff = ($weeks_difference * 5) + $days_remainder;
      break;

    case "ww": // Number of full weeks

      $datediff = floor($difference / 604800);
      break;

    case "h": // Number of full hours

      $datediff = floor($difference / 3600);
      break;

    case "n": // Number of full minutes

      $datediff = floor($difference / 60);
      break;

    default: // Number of full seconds (default)

      $datediff = $difference;
      break;
  }

  return $datediff;

}

function sqlsafe($sql) {
    $sql = str_replace("'", '`', $sql);
	$sql = (escapeshellarg(strip_tags(trim($sql))));
	if ($sql == "") {
	  return "null";
	 }
	else {
	  return $sql;
	 }
}

function createinput_text($objname, $objvalue, $size="30", $maxlength="50") {

   echo '<input type="text" name="' . $objname . '" value="' . $objvalue . '" maxlength="' . $maxlength  . '" size="' . $size  . '">';
   return true;
}

function createinput_password($objname, $size="30", $maxlength="50") {

   echo '<input type="password" name="' . $objname . '" maxlength="' . $maxlength  . '" size="' . $size  . '">';
   return true;
}

function createinput_textarea($objname, $objvalue, $rows="4", $cols="25") {

   echo '<textarea name="' . $objname . '" rows="' . $rows . '" cols="' . $cols . '">' . htmlspecialchars($objvalue) . '</textarea>';
   return true;
}


function createinput_submit($objvalue, $submitform=true, $onclick="") {
   if ($submitform) {
     $returnval = "true";
   }
   else {
     $returnval = "false";
   }

   if ($onclick !== "") {
     $onclick .= ";";
   }

   echo '<input type="submit" class="mhsubmit" name="' . $objvalue . '" value="' . htmlspecialchars($objvalue) . '" onclick="' . $onclick . 'return ' . $returnval  . '">';
   return true;
}

function createinput_checkbox($objname, $objvalue, $onclick="") {
  if ($onclick != '') {
    $onclick = ' onclick="' . $onclick . '"';
  }

  if ($objvalue == '1') {
    echo '<input name="' . $objname . '" type="checkbox" value="1" class="clscbox" checked' . $onclick . '>';
  }
  else {
    echo '<input name="' . $objname . '" type="checkbox" value="1" class="clscbox"' . $onclick . '>';
  }
  return true;
}

function createinput_select($tablename, $displayfld, $objvalue, $onchange="", $showall=false) {
  $err = "";
  $retstr = "";

  if ($onchange !== '') {
    $onchange = ' onchange="' . $onchange . '" ';
  }

  $query = sprintf( "select id, %s from %s order by %s asc" ,
            $displayfld,
            strtolower($tablename),
            $displayfld);
  $values = mhexecquery($query, $err);

  if (!$values)  {

     echo "No values matching select query";
     return false;
  }
  else {
    $retstr  = '<select name="' . trim(strtolower($tablename)) . '_id"' . $onchange . '>';
    $retstr  .= '<option value="">&lt;Select ' . $tablename .'&gt;</option>';
    if ($showall) {
      $retstr  .= '<option value="">Show All</option>';
    }
    while ($row = mysql_fetch_assoc($values)) {
      if ($row['id'] == $objvalue) {
        $retstr  .= sprintf( '<option value="%s" selected>%s</option>', $row['id'] , $row[$displayfld]);
      }
      else {
        $retstr  .= sprintf( '<option value="%s">%s</option>', $row['id'] , $row[$displayfld]);
      }
    }
    $retstr  .= '</select>';
  }

  echo $retstr ;
  return true;

}

function createinput_select_fm($fmid, $famid='', $onchange="", $showall=false){

  $err = "";
  $retstr = "";

  if ($onchange !== '') {
    $onchange = ' onchange="' . $onchange . '" ';
  }

  if ($famid !=='') {
      $query = "select id, displayname from familymember where family_id=" . $famid . " order by displayname asc";
  }
  else {
      $query = "select id, displayname from familymember order by displayname asc";
  }


  $values = mhexecquery($query, $err);

  if (!$values)  {

     echo "No values matching select query";
     return false;
  }
  else {
    $retstr  = '<select name="familymember_id"' . $onchange . '>';
    $retstr  .= '<option value="">&lt;Select Family Member&gt;</option>';
    if ($showall) {
      $retstr  .= '<option value="">Show All</option>';
    }
    while ($row = mysql_fetch_assoc($values)) {
      if ($row['id'] == $fmid) {
        $retstr  .= sprintf( '<option value="%s" selected>%s</option>', $row['id'] , $row['displayname']);
      }
      else {
        $retstr  .= sprintf( '<option value="%s">%s</option>', $row['id'] , $row['displayname']);
      }
    }
    $retstr  .= '</select>';
  }

  echo $retstr ;
  return true;

}

function createinput_select_recipient($objvalue, $hasemail=1) {
  $err = "";
  $retstr = "";
  if ($hasemail == 1) {
   $query = 'select id, displayname from familymember where email is not null and email <> "" order by displayname asc';
  }
  else {
   $query = 'select id, displayname from familymember order by displayname asc';
  }
  $values = mhexecquery($query, $err);

  if (!$values)  {
     echo "No values matching select query";
     return false;
  }
  else {
    $retstr  = '<select name="family_id[]" size="5" class="mhmulti" style="height:100" multiple>';

    while ($row = mysql_fetch_assoc($values)) {
      $selected = false;
      if ($objvalue) {
        foreach ($objvalue as $value) {
          if ($value == $row['id']) {
            $selected = true;
          }
        }
      }
      if ($selected) {
        $retstr  .= sprintf( '<option value="%s" selected>%s</option>', $row['id'] , $row['displayname']);
      }
      else {
        $retstr  .= sprintf( '<option value="%s">%s</option>', $row['id'] , $row['displayname']);
      }
    }
    $retstr  .= '</select>';
  }

  echo $retstr ;
  return true;

}

function createinput_select_location($tablename, $objvalue, $onchange="") {
  $err = "";
  $retstr = "";
  $query = 'select l.id as location_id, l.address1, l.address2, l.town, l.postcode, l.state, c.country from location l join country c where c.id=l.country_id order by address1, address2 asc';
  $values = mhexecquery($query, $err);

  if (!$values)  {
    echo "No Locations defined";
    return false;
  }
  else {
    if ($onchange !=='') {
      $onchange = ' onchange="' . $onchange . '"';
    }
    $retstr  = '<select name="location_id"' . $onchange . '>';
    $retstr  .= '<option value="">&lt;Select Address&gt;</option>';
    $retstr  .= '<option value="-1">New Address...</option>';
    while ($row = mysql_fetch_assoc($values)) {
      $address = trim($row['country']);
      if (trim($row['town']) !== '') {
        if ($address !== '' ) $address .= ', ' . trim($row['town']); else $address .= trim($row['town']);
      }
      if (trim($row['address1']) !== '') {
        if ($address !== '' ) $address .= ', ' . trim($row['address1']); else $address .= trim($row['address1']);
      }
      if (trim($row['address2']) !== '') {
        if ($address !== '' ) $address .= ', ' . trim($row['address2']); else $address .= trim($row['address2']);
      }
      if (trim($row['postcode']) !== '') {
        if ($address !== '' ) $address .= ', ' . trim($row['postcode']); else $address .= trim($row['postcode']);
      }

      if ($row['location_id'] == $objvalue) {
        $retstr  .= sprintf( '<option value="%s" selected>%s</option>', $row['location_id'] , htmlspecialchars($address));
      }
      else {
        $retstr  .= sprintf( '<option value="%s">%s</option>', $row['location_id'] , htmlspecialchars($address));
      }
    }
    $retstr  .= '</select>';
  }

  echo $retstr ;
  return true;

}

function dateadd($interval, $number, $date) {

    $date_time_array = getdate($date);
    $hours = $date_time_array['hours'];
    $minutes = $date_time_array['minutes'];
    $seconds = $date_time_array['seconds'];
    $month = $date_time_array['mon'];
    $day = $date_time_array['mday'];
    $year = $date_time_array['year'];

    switch ($interval) {
        case 'y':
            $year += $number;
            break;
        case 'm':
            $month += $number;
            break;
        case 'd':
            $day += $number;
            break;
    }
    return strftime('%Y-%m-%d', mktime($hours,$minutes,$seconds,$month,$day,$year));

}

function sendemailmsg($subject, $msg, $to, $cc="", $from="Malthouse Cottage Web", $mailsender=false) {

	//Now send a mail


	$headers = 'From: ' . $from . "\r\n" .
	    'CC: ' . $cc . "\r\n" ;
	    'Reply-To: noreply@malthousecottage.com' . "\r\n" ;

	mail($to, $subject, $msg, $headers);

    if ($mailsender) {
	  $headers = 'From: Malthouse Cottage Confirmation' . "\r\n" .
	      'Reply-To: noreply@malthousecottage.com' . "\r\n" ;

	  mail($from, 'Receipt: ' . $subject, 'Your Malthouse Cottage message has been sent: ' . "\r\n" . $msg, $headers);
    }
    return true;

}

function writeHTMLinfobox($text, $width) {
  $html = '<table width="' . $width .  '" cellpadding="5" cellspacing="0" border="0">'
        . '<tr valign="top">'
        . '<td class="mhinfobox" align="center">'
        . $text
        . '</td>'
        . '</tr>'
        . '</table>';

  return $html;
}
?>