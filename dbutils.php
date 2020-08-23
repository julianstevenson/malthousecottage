<?php
function mhdbconnect(&$err) {
  $UID	= "malthouse";
  $PWD	= "cMUc8qYhxjpW2DXf";

  $dbcon =  mysql_connect('localhost', $UID, $PWD);

  if (!$dbcon) {
	$err = 'Could not connect: ' . mysql_error();
  }


 return $dbcon;

}


function mhexecquery($query, &$err) {
  $dbcon =  mhdbconnect($err);

  if ($dbcon) {
    $result = mysql_db_query ('malthousecottage', $query);
    if (!$result) {
      $err  = 'Invalid query: ' . mysql_error() . "\n";
      $err  .= '<br><i>SQL: ' . $query . "</i>\n";
    }
  }
  return $result;

}


php?>
