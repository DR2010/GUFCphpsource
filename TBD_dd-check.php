<?php

	get_header();
	
	// ----------------------
	// LOCAL values!!! Replace in production
	// ----------------------

	// $db_hostname = 'localhost:3308';
	// $db_username = 'root';
	// $db_password = 'oculos';
	// $db_database = 'gufcdraws';

	$db_hostname = 'gungahlinunitedfc.org.au';
	$db_username = 'gufcweb_dev';
	$db_password = 'deve!oper';
	$db_database = 'gufcweb_player';	
	
	// Ubuntu Hyper V
	// $db_hostname = 'localhost:3306';
	// $db_username = 'root';
	// $db_password = '';
	// $db_database = 'gufcdraws';
	
	// Database Connection String
	$con = mysql_connect($db_hostname,$db_username,$db_password);

	if (!$con)  
	{
		die('Could not connect: ' . mysql_error());
	}

	mysql_select_db($db_database, $con);
?>

<!doctype html">

<html>
<head>
		<link rel="stylesheet" href="taskman.css">
</head>
<body>
<br><br>
<a href=?page_id=152>Back to search</a>
<br><br>
<?php
	$agegroupselected=$_POST['agegroupselected'];
	$teamselected=$_POST['teamselected'];

	// echo "Value of \$agegroupselected = $agegroupselected <br>Value of \$teamselected = $teamselected ";
			
		$term  = $teamselected;
		
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
				WHERE ( game.fkhometeamid = '".$term."' OR game.fkawayteamid = '".$term."')
				
				   AND game.fkagegroupid = '".$agegroupselected."' 
				   AND game.fkgroundplaceid = groundplace.idgroundplace 
				   AND game.fkroundid = round.idround 
				   AND game.fkfieldid = harrisonsfieldschema.fieldid
				   AND ( round.date >= SUBDATE(now(), INTERVAL 1 DAY)
				   AND round.date < ADDDATE( now(), INTERVAL 6 DAY)  )
				ORDER BY round.date
				";
					
		$r_queryinner = mysql_query($sqlinner);

		$todays_date = date("Y-m-d");						
		echo '<h2>';  
		echo '<br /> Today is ' .$todays_date. '</b>';  
		echo '<br /> Searching for team: '.$term;  
		echo '</h2>';  

		$msg = 'No games found.';	
		while ($rowinner = mysql_fetch_array($r_queryinner))
		{	
			$msg = '';
			echo '<br/> ';
			echo '<br/>';

			$mystring = $rowinner['fkhometeamid'];
			$findme   = $term;
			$pos = stripos( $mystring, $findme );

			// Note our use of ===.  Simply == would not work as expected
			// because the position of 'a' was the 0th (first) character.
			// echo '<h3>Age Group: ' .$rowinner['fkagegroupid'].'</h3>';   
			// if ($pos === false) {
				// echo '<h2> Team:      ' .$rowinner['fkawayteamid'].'</h2>';  
			// } else {
				// echo '<h2> Team:      ' .$rowinner['fkhometeamid'].'</h2>';  
			// }

			echo '<table class="task" align="center">';
			
			echo '<tr>';
			echo '   <td>Age group</td><th>'.$rowinner['fkagegroupid'].'</th>';
			echo '</tr>';
			echo '<tr>';
			echo '   <td>Next Game</td><th>'.$rowinner['rounddate'].'</th>';
			echo '</tr>';
			echo '<tr>';
			echo '   <td>Time</td><th>'.$rowinner['time'].'</th>';
			echo '</tr>';
			echo '<tr>';
			echo '   <td>Round</td><th>'.$rowinner['idround'].'</th>';
			echo '</tr>';
			echo '<tr>';
			echo '   <td>Home Team</td><th>'.$rowinner['fkhometeamid'].'</th>';
			echo '</tr>';
			echo '<tr>';
			echo '   <td>Away Team</td><th>'.$rowinner['fkawayteamid'].'</th>';
			echo '</tr>';
			echo '<tr>';
			echo '   <td>Referee</td><th>'.$rowinner['referee'].'</th>';
			echo '</tr>';
			echo '<tr>';
			echo '   <td>Home Job</td><th>'.$rowinner['homejob'].'</th>';
			echo '</tr>';
			echo '<tr>';
			echo '   <td>Ground Address</td><th>'.$rowinner['gpaddress'].'</th>';
			echo '</tr>';
			echo '<tr>';
			echo '   <td>Navigate to</td><th><a href="' .$rowinner['gpnavigate']. '">Show in map</a></th>';
			echo '</tr>';
			
			if ( ! empty( $rowinner['locationinfield'] ) ) 
			{
				echo '<tr>';
				echo '   <td>Field</td><th>'.$rowinner['fieldid'].'</th>';
				echo '</tr>';
			}
			
			echo '</table>';
			
			if ( ! empty( $rowinner['locationinfield'] ) ) 
			{
				echo '<br /> <img src="' .$rowinner['locationinfield']. '" alt="Field">';
			}
		}	

		echo '<p/>';
		echo '<b>';
		echo '<div class=task>'.$msg.'</div>' ;
		echo '</b>';
?>
<br><br>
<a href=?page_id=152&agegroupselected=>Back to search</a>

<br><br>

</center> 
</body>

</html>

<?php 
do_action('generate_sidebars');
get_footer();