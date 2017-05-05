<?php
	/*
	Template Name: Find Field
	*/
	get_header();

	// ----------------------
	// Get data by TEAM
	// ----------------------

	$db_hostname = 'gungahlinunitedfc.org.au';
	$db_username = 'gufcweb_dev';
	$db_password = 'deve!oper';
	$db_database = 'gufcweb_player';

	// Database Connection String
	$con = mysql_connect($db_hostname,$db_username,$db_password);

	if (!$con)  
	{
		die('Could not connect: ' . mysql_error());
	}

	mysql_select_db($db_database, $con);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Where am I playing?</title>
    </head>
    <body>
		<form action="" method="post">  
			<p/>
			The field search option is now working for U5, U6, U7, U8 & U9.  
			The games for U8 & U9 are published every week on Wednesday/ Thursday.
			<p/>
			You can enter partial names in the search field.
			<p/>
			Enter Team Name: <input type="text" name="teamname" /><br />  
			<p/>

			<input type="submit" value="Submit" />  
		</form>  
		
		<?php
			if (!empty($_REQUEST['teamname'])) 
			{
				$term  = mysql_real_escape_string($_REQUEST['teamname']);   
				
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
							,round.idround          idround
							,round.date             rounddate
							,groundplace.navigate   gpnavigate
							,groundplace.address 	gpaddress 
							,harrisonsfieldschema.fieldid fieldid
							,harrisonsfieldschema.imagelocation locationinfield
							,case 
								when ( round.date >= SUBDATE(now(), INTERVAL 1 DAY) and date(round.date) < ADDDATE( now(), INTERVAL 6 DAY)) then '  <<<< NEXT GAME >>>>'
								else ''
							end as 'next'
						 FROM game, round, groundplace, harrisonsfieldschema
						WHERE ( game.fkhometeamid like '%".$term."%' OR game.fkawayteamid like '%".$term."%')
						
						   AND game.fkgroundplaceid = groundplace.idgroundplace 
						   AND game.fkroundid = round.idround 
						   AND game.fkfieldid = harrisonsfieldschema.fieldid
						   AND ( round.date >= SUBDATE(now(), INTERVAL 1 DAY)
						   AND round.date < ADDDATE( now(), INTERVAL 6 DAY)  )
						ORDER BY round.date
						";
							
				$r_queryinner = mysql_query($sqlinner);

				$todays_date = date("Y-m-d");						
				//echo '<br /> Today is ' .$todays_date;  
				//echo '<br /> Searching for team: ' .$term;  
				
				while ($rowinner = mysql_fetch_array($r_queryinner))
				{	
					echo '<br/>';
					echo '<br/>';
					
					$mystring = $rowinner['fkhometeamid'];
					$findme   = $term;
					$pos = stripos( $mystring, $findme );

					// Note our use of ===.  Simply == would not work as expected
					// because the position of 'a' was the 0th (first) character.
					if ($pos === false) {
						echo '<br> <h1> Age Group: ' .$rowinner['fkagegroupid'].' = Team: ' .$rowinner['fkawayteamid'].'</h1> </br>';  
					} else {
						echo '<br> <h1> Age Group: ' .$rowinner['fkagegroupid'].' = Team: ' .$rowinner['fkhometeamid'].'</h1> </br>';  
					}

					echo '<table class="imagetable">';
					
					echo '<tr>';
					echo '   <th>Next Game</th><th>'.$rowinner['rounddate'].'</th>';
					echo '</tr>';
					echo '<tr>';
					echo '   <th>Time</th><th>'.$rowinner['time'].'</th>';
					echo '</tr>';
					echo '<tr>';
					echo '   <th>Round</th><th>'.$rowinner['idround'].'</th>';
					echo '</tr>';
					echo '<tr>';
					echo '   <th>Home Team</th><th>'.$rowinner['fkhometeamid'].'</th>';
					echo '</tr>';
					echo '<tr>';
					echo '   <th>Away Team</th><th>'.$rowinner['fkawayteamid'].'</th>';
					echo '</tr>';
					echo '<tr>';
					echo '   <th>Referee</th><th>'.$rowinner['referee'].'</th>';
					echo '</tr>';
					echo '<tr>';
					echo '   <th>Home Job</th><th>'.$rowinner['homejob'].'</th>';
					echo '</tr>';
					echo '<tr>';
					echo '   <th>Ground Address</th><th>'.$rowinner['gpaddress'].'</th>';
					echo '</tr>';
					echo '<tr>';
					echo '   <th>Navigate to</th><th><a href="' .$rowinner['gpnavigate']. '">Show in map</a></th>';
					echo '</tr>';
					
					if ( ! empty( $rowinner['locationinfield'] ) ) 
					{
						echo '<tr>';
						echo '   <th>Field</th><th>'.$rowinner['fieldid'].'</th>';
						echo '</tr>';
					}
					
					echo '</table>';
					
					if ( ! empty( $rowinner['locationinfield'] ) ) 
					{
						echo '<br /> <img src="themes/images/' .$rowinner['locationinfield']. '" alt="Field">';
					}
				}	
			}
			else
			{
			echo 'not found. <br/>';
			}
		?>
    </body>
</html>
<?php 
do_action('generate_sidebars');
get_footer();
