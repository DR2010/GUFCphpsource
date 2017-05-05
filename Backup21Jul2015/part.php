<?php

function showteam( $ffanumber, $lastname )
{

		// This is the official connection string
		$db_hostname = 'gungahlinunitedfc.org.au';
		$db_username = 'gufcweb_dev';
		$db_password = 'deve!oper';
		$db_database = 'gufcweb_player';

    	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

		$playerteam = "";
		
		$sqlinner = " SELECT * FROM gufcdraws.player where FFANumber = '".$ffanumber."' and LastName = '".$lastname."'  and display = 'Y'";

		$sqlexample1 = "  SELECT * FROM gufcdraws.player where FFANumber = 28069631 and LastName = 'Chilmaid' and display = 'Y' ";
		$sqlexample2 = "  SELECT * FROM gufcdraws.player where fkteamid = 'U16 Div 2 Boys' and display = 'Y' ";
		
		$r_queryinner = $mysqli->query($sqlinner);

		$todays_date = date("Y-m-d");						

		$msg = 'No player found.';	

		echo '<table class="table" align="center" border="1" >';
		echo '<th>First Name</th>';
		echo '<th>Last Name</th>';
		echo '<th>Team Name</th>';

		if ( ! $r_queryinner )
		{
			echo 'Player not found'; 
		}
		else
		{

			$sqlteam  = " SELECT FirstName,LastName,fkteamid FROM gufcdraws.player where fkteamid = '".$playerteam."' and display = 'Y'";
			
			$r_queryteam = $mysqli->query($sqlteam);
			
			if ( $r_queryteam ) {
			
				$rowteam = mysqli_fetch_assoc($r_queryteam)
				echo '<tr>';
				echo '<td>'.$rowteam['FirstName'].'</td>';
				echo '<td>'.$rowteam['LastName'].'</td>';
				echo '<td>'.$rowteam['fkteamid'].'</td>';
				echo '</tr>';
			}
			echo '</table>';
			echo '<p/>';
		}
}
?>

