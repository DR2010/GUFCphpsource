<?php

	// This is the official connection string
	$db_hostname = 'gungahlinunitedfc.org.au';
	$db_username = 'gufcweb_dev';
	$db_password = 'deve!oper';
	$db_database = 'gufcweb_player';
	$mysqli = "";

	
	
function authorisationipisallowed( $remoteip )
{
    // Only accept calls from this IP address
    $retip = '120.19.67.172'; 
    $isallowed = 'true';
    
    if ( $remoteip !== $retip) 
    {
        $isallowed = 'false';
    }
    
    // Temporary override
    // $isallowed = 'true';
    
    return $isallowed;
    
}
	
	
	
function startdb()
{
	
	global $db_hostname,$db_username,$db_password, $db_database, $mysqli;  
	
	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

	/* check connection */
	if ($mysqli ->connect_errno) {
		printf("Connect failed: %s\n", $mysqli->connect_error);
		exit();
	}
}

// ----------------------------------------------------------------------------------------
//    Dropdown for Age Group
// ----------------------------------------------------------------------------------------
function dropdownAny( $defaultValue, $keyfield, $table, $label, $ReadOnly, $extracondition )
{
	
	global $db_hostname,$db_username,$db_password, $db_database, $mysqli;  
	
	// startdb();

	$sql_stmt="select ".$keyfield." idkey from ".$table." ".$extracondition." order by 1;";
	
	$query_stmt = $mysqli->query($sql_stmt);

	if ($ReadOnly == "ReadOnly") 
	{
		echo $label.' <input type="text" name="'.$label.'" value="'.$defaultValue.'" readonly></br>';
	}
	else
	{
		echo $label;
		echo '<label>';
		echo "<select name='".$table."'>";
		echo '<Option value="">Select...</option>';
		
		while ($rowinner = mysqli_fetch_assoc($query_stmt))
		{
			if ($rowinner['idkey']==@$defaultValue)
			{
				echo "<option selected value='$rowinner[idkey]'>$rowinner[idkey]</option>"."<BR>";
			}
			else
			{
				echo '<Option value="'.$rowinner['idkey'].'">'.$rowinner['idkey'].'</option>';
			}
		}
		echo '</select>';
		echo '</label>';
	}
	echo '<p/>';

}

function listGames( $dategame, $location )
{

		global $db_hostname,$db_username,$db_password, $db_database, $mysqli;  

		// $db_hostname = 'gungahlinunitedfc.org.au';
		// $db_username = 'gufcweb_dev';
		// $db_password = 'deve!oper';
		// $db_database = 'gufcweb_player';

		// $db_hostname = '192.168.1.12:3306';
		// $db_username = 'danielgufc_user';
		// $db_password = 'danielgufc_password';
		// $db_database = 'gufcdraws';
    	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);
		
		$criteria = '';
		if ( $location == 'Harrison')
		{
			$criteria = ' and groundplace.idgroundplace = "HARRISON" ';
		}
		if ( $location == 'Away')
		{
			$criteria = ' and groundplace.idgroundplace != "HARRISON" and game.fkawayteamid like "%Gungahlin%" ';
		}
		
		$sqlinner = "
					SELECT 
					 game.gameid 
					,game.fkhometeamid      fkhometeamid
					,game.fkawayteamid      fkawayteamid
					,game.fkagegroupid      fkagegroupid
					,game.fkroundid         fkroundid
					,game.fkgroundplaceid   fkgroundplaceid
					,game.referee   referee
					,game.homejob   homejob
					,game.time      time
					,game.seqnum      seqnum
					,round.idround          idround
					,round.date             rounddate
					,groundplace.navigate   gpnavigate
					,groundplace.address 	gpaddress 
					,harrisonsfieldschema.fieldid fieldid
					,harrisonsfieldschema.imagelocation locationinfield
				 FROM game, round, groundplace, harrisonsfieldschema
				WHERE 
				       game.fkgroundplaceid = groundplace.idgroundplace 
				   AND game.fkroundid = round.idround 
				   AND game.fkfieldid = harrisonsfieldschema.fieldid
				   AND round.date = '".$dategame."'
				   ".$criteria."
				ORDER BY game.time, seqnum, game.fkagegroupid, fieldid 
				";
					
		$r_queryinner = $mysqli->query($sqlinner);

		$todays_date = date("Y-m-d");						

		if ( ! $r_queryinner )
		{
			echo 'No games found on date '.$dategame.' '; 
		}
		else
		{
            $currentage = "";
            $previousage = "";
		
     		while ($rowinner = mysqli_fetch_assoc($r_queryinner))
			{	
				$currentage = $rowinner['fkagegroupid'];

				if  ( strpos($currentage, 'Girls MiniRoos') !== false )	{ $currentage = 'Girls MiniRoos';  }
				elseif ( strpos($currentage, 'U5'            ) !== false) { $currentage = 'UNDER 5';       } 
				elseif ( strpos($currentage, 'U6'            ) !== false) { $currentage = 'UNDER 6';       } 
				elseif ( strpos($currentage, 'U7'            ) !== false) { $currentage = 'UNDER 7';       } 
				elseif ( strpos($currentage, 'U8'            ) !== false) { $currentage = 'UNDER 8';       } 
				elseif ( strpos($currentage, 'U9'            ) !== false) { $currentage = 'UNDER 9';       } 
				elseif ( strpos($currentage, 'U10'           ) !== false) { $currentage = 'UNDER 10';       } 
				elseif ( strpos($currentage, 'U11'           ) !== false) { $currentage = 'U11, U13 and Seniors';       } 
				elseif ( strpos($currentage, 'U12'           ) !== false) { $currentage = 'U12, U14';       } 
				elseif ( strpos($currentage, 'U13'           ) !== false) { $currentage = 'U11, U13 and Seniors';       } 
				elseif ( strpos($currentage, 'U14'           ) !== false) { $currentage = 'U12, U14';       } 
				else { $currentage = 'UNDER 15,16,17,18 and Seniors'; };
		
		        if ($currentage == "" or $currentage !== $previousage)
				{
					// only if previous is not empty
					if ( $previousage !== "" ) {
						echo '</tbody></table><p>';
					}

     				echo '<h1>'.$currentage.'</h1>';

					echo '<table class="table" align="center" border="1" >';
					echo "\n";
					echo '<th>Time</th>';
					echo '<th>Field</th>';
					echo '<th>Home</th>';
					echo '<th>Away</th>';
					echo '<th>Referee</th>';
					echo '<th>Age</th>';
					echo '<th>Date</th>';
					echo '<th>Round</th>';
					echo '<th>Home Job</th>';
					echo '<th>Ground Address</th>';
					echo "\n";
				}					
		
				echo "\n";
				echo '<tr>';
				echo '<td>'.$rowinner['time'].'</td>';
				echo '<td>'.$rowinner['fieldid'].'</td>';
				echo '<td>'.$rowinner['fkhometeamid'].'</td>';
				echo '<td>'.$rowinner['fkawayteamid'].'</td>';
				echo '<td>'.$rowinner['referee'].'</td>';
				echo '<td>'.$rowinner['fkagegroupid'].'</td>';
				echo '<td>'.$rowinner['rounddate'].'</td>';
				echo '<td>'.$rowinner['idround'].'</td>';
				echo '<td>'.$rowinner['homejob'].'</td>';
				echo '<td>'.$rowinner['gpaddress'].'</td>';
				echo '</tr>';

				$previousage = $currentage;

			}	

			echo "\n";
			echo '</table>';
			echo '<p/>';
		
		}
}


?>
