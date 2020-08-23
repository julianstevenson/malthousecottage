
			    <?
				//MALTHOUSE STATUS
				$reserr = '';
				$occupied = false;

				$query = "SELECT r.id AS reservation_id, r.fullname FROM reservation r WHERE TO_DAYS( r.arrivedate) - TO_DAYS(curdate( ) ) <=0 AND TO_DAYS( r.departdate) - TO_DAYS( curdate( ) ) >=0 and location=2";
				$reservations = mhexecquery($query, $reserr);

				if (mysql_num_rows($reservations)>0) {
				  $occupied = true;
				}
			    $infomsg = '';
				if ($reserr !== '' ) {
				  $infomsg = '<span class="error">' . $reserr . '</span>';
				}
				elseif ($occupied) {
				  $infomsg = '<span class="largered">Occupied</span>';
				}
				else {
				  $infomsg = '<span class="largemessage">Vacant</span>';
				}

			    echo writeHTMLinfobox('<p><img src="image/france.jpg" height="20" width="32" border="0"><br><b>Flayosc is currently</b></p><p>' . $infomsg . '</p><p><a href="javascript:viewbookings(2)" title="View the reservations page">View Bookings</a></p>', '140');
			    ?>
