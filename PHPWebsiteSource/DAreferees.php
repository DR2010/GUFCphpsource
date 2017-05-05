<?php
    include 'GUFCHelper.php';

	// ---------------------------------------------------------
	//
	//                 Maintain Referees
	//
	// ---------------------------------------------------------
	

	// This is the official connection string
	// $db_hostname = 'gungahlinunitedfc.org.au';
	// $db_username = 'gufcweb_dev';
	// $db_password = 'deve!oper';
	// $db_database = 'gufcweb_player';
	
	$db_hostname = 'localhost:3306';
	$db_username = 'root';
	$db_password = 'oculos';
	$db_database = 'gufcdraws';

    $mysqli = "";


class Referee
{
    var $idreferee;
    var $name;
    var $gamecount;
    var $availabilitytime;

}

class RefereeListTemp {
    var $idreferee;
    var $availabletimes;
    var $gamescounttoday;
    var $remainingavailability;
    var $allocatedtimes;
}

function showListReferees( $dategame, $temprefereegame )
{
    echo '<table class="table-responsive" align="center" border="1" >';
    echo "\n";
    echo '<th>Referee ID</th>';
    echo '<th>games today count</th>';
    echo '<th>08:50 AM</th>';
    echo '<th>09:40 AM</th>';
    echo '<th>10:35 AM</th>';
    echo '<th>11:30 AM</th>';
    echo '<th>12:30 PM</th>';
    echo '<th> 1:45 PM</th>';
    echo '<th> 3:10 PM</th>';
    echo "\n";

    // Get Referee List

    // $refereelist = CountGamesByReferee($dategame);
    $refereelist = listReferees($dategame);

    foreach( $refereelist as $ref) {

        $checkagainsttime = "";

        $t0850 = 'N/A'; $t0900 = 'N/A'; $t0940 = 'N/A'; $t1000 = 'N/A';
        $t1035 = 'N/A'; $t1130 = 'N/A'; $t1230 = 'N/A'; $t1345 = 'N/A';
        $t1510 = 'N/A';

        $referee = new Referee();
        $referee = getAvailabilityByReferee( $ref->idreferee );
        $checkagainsttime = $referee->availabilitytime;

        if ( strpos($checkagainsttime, "08:50 AM" ) !== false )	$t0850 = '';
        if ( strpos($checkagainsttime, "09:40 AM" ) !== false )	$t0940 = '';
        if ( strpos($checkagainsttime, "10:35 AM" ) !== false )	$t1035 = '';
        if ( strpos($checkagainsttime, "11:30 AM" ) !== false )	$t1130 = '';
        if ( strpos($checkagainsttime, "12:30 PM" ) !== false )	$t1230 = '';
        if ( strpos($checkagainsttime, "1:45 PM" ) !== false )	$t1345 = '';
        if ( strpos($checkagainsttime, "3:10 PM" ) !== false )	$t1510 = '';

        $gamelist = array();
        $gamelist = GetRefereeAllocationsToday( $dategame, $ref->idreferee);

        $gamecnt = 0;
        // using temporary allocations
        //
        if (count($temprefereegame) > 0)
        {
            // look for current referee allocations
            foreach( $temprefereegame as $tempreferee)
            {
                if ($tempreferee->referee == $ref->idreferee )
                {
                    if ($tempreferee->time == "08:50 AM") $t0850 = $ref->idreferee;
                    if ($tempreferee->time == "09:40 AM") $t0940 = $ref->idreferee;
                    if ($tempreferee->time == "10:35 AM") $t1035 = $ref->idreferee;
                    if ($tempreferee->time == "11:30 AM") $t1130 = $ref->idreferee;
                    if ($tempreferee->time == "12:30 PM") $t1230 = $ref->idreferee;
                    if ($tempreferee->time == "1:45 PM") $t1345 = $ref->idreferee;
                    if ($tempreferee->time == "3:10 PM") $t1510 = $ref->idreferee;
                    $gamecnt++;
                }
            }
        }
        else{
            foreach ($gamelist as $gameind) {
                if ($gameind->referee == $ref->idreferee ) {

                    if ($gameind->time == "08:50 AM") $t0850 = '* BUSY *';
                    if ($gameind->time == "09:40 AM") $t0940 = '* BUSY *';
                    if ($gameind->time == "10:35 AM") $t1035 = '* BUSY *';
                    if ($gameind->time == "11:30 AM") $t1130 = '* BUSY *';
                    if ($gameind->time == "12:30 PM") $t1230 = '* BUSY *';
                    if ($gameind->time == "1:45 PM") $t1345 = '* BUSY *';
                    if ($gameind->time == "3:10 PM") $t1510 = '* BUSY *';
                }
            }
            $gamecnt = count($gamelist);
        }

        echo "\n";
        echo '<tr>';
        echo '<td>'.$ref->idreferee.'</td>';
        echo '<td>'.$gamecnt.'</td>';
        echo '<td>'.$t0850.'</td>';
        echo '<td>'.$t0940.'</td>';
        echo '<td>'.$t1035.'</td>';
        echo '<td>'.$t1130.'</td>';
        echo '<td>'.$t1230.'</td>';
        echo '<td>'.$t1345.'</td>';
        echo '<td>'.$t1510.'</td>';
        echo '</tr>';

    }

    echo "\n";
    echo '</table>';
    echo '<p/>';

}


function updateRefereeDB( $gameid, $refereeid )
{

	global $db_hostname,$db_username,$db_password, $db_database, $mysqli;  

   	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

	if ( $gameid == "" || $refereeid == "" )
	{
		// do nothing
	}
	else
	{
		$sql = "UPDATE game SET referee='".$refereeid."' WHERE gameid ='".$gameid."'";
	
		if ($mysqli->query($sql) === TRUE) {
//		    echo "Record updated successfully";
		} else {
//		    echo "Error updating record: " . $mysqli->error;
		}
	}
//	echo '</p>';
//	echo $gameid;
//	echo '</p>';
//	echo $refereeid;

	$mysqli->close();
}

function showListRefereesX( $listtype )
{
    // listtype = TODAYGAMES = show all games for referee today
    // listtype = AVAILABILITY = show available times for referee

	
		global $db_hostname,$db_username,$db_password, $db_database, $mysqli;  
 
    	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);
		
		$sqlinner = "
					SELECT 
					 referee.idreferee                  idreferee
					,referee.name                       name
					,referee.available                  available
					,refereeavailability.UID            refavid
					,refereeavailability.type           type
					,refereeavailability.agegroupids    agegroupids    
					,refereeavailability.timeids        timeids
					,refereeavailability.date           date
					,referee.numberofgames              numberofgames
					,referee.gamestodaycount               gamestodaycount
					,refereeavailability.gamestodaytimes   gamestodaytimes
				 FROM referee, refereeavailability
				WHERE 
				      referee.available = 'Y'
			      AND referee.idreferee = refereeavailability.fkrefereeuid
				  AND refereeavailability.type = 'DEFAULT'
				ORDER BY referee.idreferee
				";
					
		$r_queryinner = $mysqli->query($sqlinner);

        if ($listtype == 'TODAYGAMES') echo '<h1>Todays Games by Referee</h1>';
        if ($listtype == 'AVAILABILITY') echo '<h1>Referees Availability</h1>';

		if ( ! $r_queryinner )
		{
			echo 'No referees found ';
		}
		else
		{
			echo '<table class="table" align="center" border="1" >';
			echo "\n";
			echo '<th>Referee</th>';
            echo '<th>games total</th>';
            echo '<th>games today count</th>';
            echo '<th>0850</th>';
			echo '<th>0940</th>';
			echo '<th>1035</th>';
			echo '<th>1130</th>';
			echo '<th>1230</th>';
			echo '<th>1345</th>';
			echo '<th>1510</th>';
			echo "\n";
		
     		while ($rowinner = mysqli_fetch_assoc($r_queryinner))
			{
                $checkagainsttime = "";

                if ($listtype == 'TODAYGAMES') $checkagainsttime = $rowinner['gamestodaytimes'];
                if ($listtype == 'AVAILABILITY') $checkagainsttime = $rowinner['timeids'];

                $t0850 = ''; $t0900 = ''; $t0940 = ''; $t1000 = '';
                $t1035 = ''; $t1130 = ''; $t1230 = ''; $t1345 = '';
                $t1510 = '';

                if ( strpos($checkagainsttime, "0850" ) !== false )	$t0850 = 'X';
                if ( strpos($checkagainsttime, "0940" ) !== false )	$t0940 = 'X';
                if ( strpos($checkagainsttime, "1035" ) !== false )	$t1035 = 'X';
                if ( strpos($checkagainsttime, "1130" ) !== false )	$t1130 = 'X';
                if ( strpos($checkagainsttime, "1230" ) !== false )	$t1230 = 'X';
                if ( strpos($checkagainsttime, "1345" ) !== false )	$t1345 = 'X';
                if ( strpos($checkagainsttime, "1510" ) !== false )	$t1510 = 'X';

				echo "\n";
				echo '<tr>';
				echo '<td>'.$rowinner['idreferee'].'</td>';
                echo '<td>'.$rowinner['numberofgames'].'</td>';
                echo '<td>'.$rowinner['gamestodaycount'].'</td>';
                echo '<td>'.$t0850.'</td>';
				echo '<td>'.$t0940.'</td>';
				echo '<td>'.$t1035.'</td>';
				echo '<td>'.$t1130.'</td>';
				echo '<td>'.$t1230.'</td>';
                echo '<td>'.$t1345.'</td>';
                echo '<td>'.$t1510.'</td>';
				echo '</tr>';

			}	

			echo "\n";
			echo '</table>';
			echo '<p/>';
		
		}
		
		$mysqli->close();
}

function listReferees()
{
    global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

    $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

    $sqlinner = "
					SELECT
					 referee.idreferee                  idreferee
					,referee.name                       namereferee
					,referee.available                  available
					,refereeavailability.UID            refavid
					,refereeavailability.type           typereferee
					,refereeavailability.timeids        timeids
					,refereeavailability.date           dateavailability
					,referee.numberofgames              numberofgames
					,referee.gamestodaycount               gamestodaycount
					,refereeavailability.gamestodaytimes   gamestodaytimes
				 FROM referee LEFT JOIN refereeavailability
				ON
			          referee.idreferee = refereeavailability.fkrefereeuid
				WHERE referee.available = 'Y'
				  AND refereeavailability.type = 'DEFAULT'
				ORDER BY referee.idreferee
				";

    $r_queryinner = $mysqli->query($sqlinner);

    $refereelist = array();

    if ( $r_queryinner )
    {
        while ($rowinner = mysqli_fetch_assoc($r_queryinner))
        {

            $referee = new Referee();
            $referee->idreferee = $rowinner['idreferee'];
            $referee->name = $rowinner['namereferee'];
            $referee->gamecount = $rowinner['numberofgames'];

            $refereelist[] = $referee;

        }
    }

    $mysqli->close();
    return $refereelist;

}

function getAvailabilityByReferee( $refinput )
{
    global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

    $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

    $sqlinner = "
					SELECT
					 referee.idreferee                  idreferee
					,referee.name                       namereferee
					,referee.available                  available
					,refereeavailability.UID            refavid
					,refereeavailability.type           typereferee
					,refereeavailability.timeids        timeids
					,refereeavailability.date           dateavailability
					,referee.numberofgames              numberofgames
					,referee.gamestodaycount               gamestodaycount
					,refereeavailability.gamestodaytimes   gamestodaytimes
				 FROM referee, refereeavailability
				WHERE
				      referee.available = 'Y'
			      AND referee.idreferee = refereeavailability.fkrefereeuid
				  AND refereeavailability.type = 'DEFAULT'
				  AND referee.idreferee = '".$refinput."'
				ORDER BY referee.idreferee
				";

    $r_queryinner = $mysqli->query($sqlinner);

    $referee = new Referee();

    if ( $r_queryinner )
    {
        if ($rowinner = mysqli_fetch_assoc($r_queryinner)) {

            $referee->idreferee = $rowinner['$idreferee'];
            $referee->availabilitytime = $rowinner['timeids'];
        }
    }
    else {
        $referee->idreferee = "Not Found";
        $referee->availabilitytime = "";
    }

    $mysqli->close();

    return $referee;

}



?>
