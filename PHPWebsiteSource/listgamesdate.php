<?php
	/*
	Template Name: Games on Date
	*/
	get_header();

	// -------------------------------------------
	// 
	// List all games on a date
	// 
	// -------------------------------------------

	// $db_hostname = 'localhost:3308';
	// $db_username = 'root';
	// $db_password = 'oculos';
	// $db_database = 'gufcdraws';
	
	// This is the official connection string
	$db_hostname = 'gungahlinunitedfc.org.au';
	$db_username = 'gufcweb_dev';
	$db_password = 'deve!oper';
	$db_database = 'gufcweb_player';

	// Database Connection String
	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

	/* check connection */
	if ($mysqli ->connect_errno) {
		printf("Connect failed: %s\n", $mysqli->connect_error);
		exit();
	}
	
?>

<!DOCTYPE html>
<html lang="en">
    <head>
		<link rel="stylesheet" type="text/css" href="rwdstyle.css">
		<script type=text/javaScript>
			function reload(form)
			{
				var val=document.getElementById('searchDate'); 
				var location=document.getElementById('location'); 
//				window.alert(" Date... " + val.value);
//				window.alert(" Date... " + location.value);
				self.location = '?page_id=168&dategame=' + val.value +'&location=' + location.value ;
			}

		</script>        

		<style>
			table {
			  border-collapse: separate;
			  border-spacing: 0;
			}
			th,
			td {
			  padding: 0 10px 0 15px;
			}
			thead {
			  background: #395870;
			  color: #fff;
			}
			tbody tr:nth-child(even) {
			  background: #f0f0f2;
			}
			td {
			  border-bottom: 1px solid #cecfd5;
			  border-right: 1px solid #cecfd5;
			}
			td:first-child {
			  border-left: 1px solid #cecfd5;
			}
						
		</style>
			
		<meta charset="utf-8" />
        <title>Where is my team playing?</title>
    </head>
    <body>

    	<?php
			@$dategame=$_GET['dategame']; 
			@$location=$_GET['location']; 

			echo "<form method='POST' name='formname' action=''>";
			echo '<br>';
			echo 'Date:';
			echo '<p/>';
			echo "<input type='date' name='dategame' id='searchDate' value='".$dategame."' id=\"searchDate\">";
			echo '<p/>';
			echo 'Location:';
			echo '<p/>';
			echo '<label>';
			echo "<select name='location' id='location'>";
			echo '<Option value="">Select...</option>';
			echo '<Option value="Harrison">All Games at GUFC Home</option>';
			echo '<Option value="Away">U8 U9 GUFC Teams Not Playing at Home</option>';
			echo '<Option value="AllGames">All Games</option>';
			echo '</select>';
			echo '</label>';
			echo '<p/>';

			echo "<input type='button' value='Submit' onclick=\"reload(this.form)\">";
			echo "</form>";
			
 			if (! $dategame == "")
			{
				listGames( $dategame, $location ); 
			}
		
		?>

    </body>
</html>

<?php 
do_action('generate_sidebars');
get_footer();

?>

<?php

function listGames( $dategame, $location )
{

		$db_hostname = 'gungahlinunitedfc.org.au';
		$db_username = 'gufcweb_dev';
		$db_password = 'deve!oper';
		$db_database = 'gufcweb_player';

		// $db_hostname = '192.168.1.12:3306';
		// $db_username = 'danielgufc_user';
		// $db_password = 'danielgufc_password';
		// $db_database = 'gufcdraws';

		// calculate dategame + 1 to include Sundays
		//
		$sundaydate = strftime("%Y-%m-%d", strtotime("$dategame +1 day"));

    	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);
		
		$criteria = '';
		if ( $location == 'Harrison')
		{
			// $criteria = ' and groundplace.idgroundplace = "HARRISON" ';
			$criteria = ' and groundplace.idgroundplace in ("HARRISON","PALMERSTON","NICHOLLS") ';
		}
		if ( $location == 'Away')
		{
			$criteria = ' and groundplace.idgroundplace != "HARRISON" and game.fkawayteamid like "%Gungahlin%" ';
		}
		
		$sqlinner = "
					SELECT 
					 game.gameid            gameid
					,game.fkhometeamid      fkhometeamid
					,game.fkawayteamid      fkawayteamid
					,game.fkagegroupid      fkagegroupid
					,game.fkroundid         fkroundid
					,game.fkfieldid         fkfieldid
					,game.fkgroundplaceid   fkgroundplaceid
					,game.referee   referee
					,game.homejob   homejob
					,game.time      time
					,game.seqnum      seqnum
					,game.timetosort      timetosort
					,round.idround          idround
					,round.date             rounddate
					,game.date             gamedate
					,groundplace.navigate   gpnavigate
					,groundplace.address 	gpaddress 
				 FROM game, round, groundplace
				WHERE 
				       game.fkgroundplaceid = groundplace.idgroundplace 
				   AND game.fkroundid = round.idround 
				   AND (round.date = '".$dategame."' OR game.date = '".$sundaydate."' OR game.date = '".$dategame."') 
				   ".$criteria."
				ORDER BY time, fkfieldid ; 
				";

					 // ORDER BY seqnum, game.fkagegroupid, fkfieldid 
				//    AND (round.date = '".$dategame."' OR gamedate = '".$sundaydate."')
				//    AND (round.date = '".$dategame."')

	
	
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
            
			$currenttime = "";
			$previoustime = "";
		
     		while ($rowinner = mysqli_fetch_assoc($r_queryinner))
			{	
				$currentage = $rowinner['fkagegroupid'];
				$currenttime = $rowinner['time'];

				if     ( strpos($currentage, 'U9 Girls'      ) !== false) { $currentage = 'U9 Girls';  }
				elseif ( strpos($currentage, 'U7-9 Miniroos Girls' ) !== false) { $currentage = 'U7-9 Miniroos Girls';       } 
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
		
				
				if     ( strpos($currenttime, '08:50 AM' ) !== false) { $currenttime = '08:50 AM';  }
				elseif ( strpos($currenttime, '08:30 AM' ) !== false) { $currenttime = '08:30 AM';  } 
				elseif ( strpos($currenttime, '09:00 AM' ) !== false) { $currenttime = '09:00 AM';  } 
				elseif ( strpos($currenttime, '09:40 AM' ) !== false) { $currenttime = '09:40 AM';  } 
				elseif ( strpos($currenttime, '10:00 AM' ) !== false) { $currenttime = '10:00 AM';  } 
				elseif ( strpos($currenttime, '10:35 AM' ) !== false) { $currenttime = '10:35 AM';  } 
				elseif ( strpos($currenttime, '11:00 AM' ) !== false) { $currenttime = '11:00 AM';  } 
				elseif ( strpos($currenttime, '11:30 AM' ) !== false) { $currenttime = '11:30 AM';  } 
				elseif ( strpos($currenttime, '12:00 PM' ) !== false) { $currenttime = '12:00 PM';  } 
				elseif ( strpos($currenttime, '12:30 PM' ) !== false) { $currenttime = '12:30 PM';  } 
				elseif ( strpos($currenttime, '1:45 PM'  ) !== false) { $currenttime = '1:45 PM';   } 
				elseif ( strpos($currenttime, '3:10 PM'  ) !== false) { $currenttime = '3:10 PM';   } 
				else { $currenttime = 'Other Times'; };
				
				
//		        if ($currentage == "" or $currentage !== $previousage)

				$tablestarted="N";
				$showheader="N";
				$showage="N";
				$showtime="N";

		        if ($currenttime == "" or $currenttime !== $previoustime)
				{
					$showtime="Y";
					$showheader="Y";
				}

		        if ($currenttime == "09:00 AM")
				{
					if ($currentage !== $previousage)
					{
						$showage="Y";
						$showheader="Y";
					}
				}

				if ($showheader == "Y")
				{
					echo '</table><p>';
					if ($showtime=="Y")
					{
						echo '<h1>Time: '.$currenttime.'</h1>';
						// echo '<h1>Sunday: '.$sundaydate.'</h1>';
					}
					if ($showage=="Y")
					{
						echo '<h2>Competition: '.$rowinner['fkagegroupid'].'</h2>';
					}

					echo '<table class="rwd-table">';
					echo '<tr>';
					echo '<th>___Date___</th>';
					echo '<th>___Time___</th>';
					echo '<th>Field</th>';
					echo '<th>Home</th>';
					echo '<th>Away</th>';
					echo '<th>Referee</th>';
					echo '<th>Age</th>';
					echo '<th>Round</th>';
					echo '<th>Home Job</th>';
					echo '<th>Ground Address</th>';
					echo '<th>SystemID</th>';
					echo '</tr>';

				}				
				

				$showdate = $rowinner['gamedate'];
				if ( $rowinner['gamedate'] == $sundaydate )
				{
					$showdate = $rowinner['gamedate']." << **SUNDAY** ";
				}

				// echo "\n";
				echo '<tr>';
				// echo '<td>'.$rowinner['rounddate'].'</td>';
				echo '<td>'.$showdate.'</td>';
				echo '<td>'.$rowinner['time'].'</td>';
				echo '<td>'.$rowinner['fkfieldid'].'</td>';
				echo '<td>'.strtoupper($rowinner['fkhometeamid']).'</td>';
				echo '<td>'.strtoupper($rowinner['fkawayteamid']).'</td>';
				echo '<td>'.$rowinner['referee'].'</td>';
				echo '<td>'.$rowinner['fkagegroupid'].'</td>';
				echo '<td>'.$rowinner['idround'].'</td>';
				echo '<td>'.$rowinner['homejob'].'</td>';
				echo '<td>'.$rowinner['gpaddress'].'</td>';
				echo '<td>'.$rowinner['gameid'].'</td>';
				echo '</tr>';

				$previousage = $currentage;
				$previoustime = $currenttime;

			}	

			echo "\n";
			echo '</table>';
			echo '<p/>';
		
		}
}
?>

