<?php
	/*
	Template Name: List Games Edit
	*/
	get_header();

	include 'DBFunctions.php';
	
	// http://gungahlinunitedfc.org.au/wordpress/?page_id=593
	
	// ----------------------
	// LOCAL values!!! Replace in production
	// ----------------------

	// This is the official connection string
	$db_hostname = 'gungahlinunitedfc.org.au';
	$db_username = 'gufcweb_dev';
	$db_password = 'deve!oper';
	$db_database = 'gufcweb_player';

	// Ubuntu Hyper V
	// $db_hostname = '192.168.1.12:3306';
	// $db_username = 'danielgufc_user';
	// $db_password = 'danielgufc_password';
	// $db_database = 'gufcdraws';

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
		<style>
		table, th, td {
			border: 1px solid black;
		}
		</style>
	
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
		
		<script language=JavaScript>
			function findbydate(form)
			{
				var date=document.getElementById('searchDate'); 
				self.location='?page_id=593&dategame=' + date.value ;
			}
			
			function updatereferee( elementname, gameid, searchDate )
			{
				var val=document.getElementById( elementname ); 
				var date=document.getElementById('searchDate'); 
				// window.alert(" Update Referee!!!! " + val.value + " Game ID " + gameid);

				self.location='?page_id=593&gameidtoupdate=' + gameid + '&refereetoupdate='+ val.value + '&dategame=' + date.value ;

				// ---------------------------------------------------------------------------------------
				//
				// use ajax to avoid page full refresh - for now page full refresh is the only option
				//
				// ---------------------------------------------------------------------------------------
				
//				$( document ).ready( function() 
//				{
//					alert('Just before the call...');
//
//					var data = {
//					    "gameid": gameid, 
//						"refereeid": val.value 
//					};
//
//					$.ajax({
//					    type: 'POST',
//					    url: 'updateReferee.php',
//					    data: data,
//					    success: function(response) {
//								alert(response);
//							}
//					});
//				});
								
			}
			
		</script>        
		<meta charset="utf-8" />
        <title>Where is my team playing?</title>
    </head>
    <body>

		<?php

			// Get Age Group
			// -------------
			@$searchDate=$_GET['dategame']; 
			@$refereetoupdate=$_GET['refereetoupdate']; 
			@$gameidtoupdate=$_GET['gameidtoupdate']; 
			
			$sql_referee="select idreferee, name from referee order by idreferee;";

			$query_referee = $mysqli->query($sql_referee);
			
			echo "<form method=post name=f1 action=display()>";

			echo '<p/>';
			echo 'Date:';
			echo '<p/>';
			echo "<input type='date' name='dategame' value='".$searchDate."' id=\"searchDate\">";
			echo '<p/>';

			echo "<input type='button' value='Submit' onclick=\"findbydate(this.form)\">";
			
			echo "</form>";

			if (!$searchDate == "")
			{
				listGames($searchDate ); 
			}
			
			if ( ! $gameidtoupdate == "" )
			{
				if ( ! $refereetoupdate == "" )
				{
					updateRefereeDB( $gameidtoupdate, $refereetoupdate );
					$location = '/wordpress/?page_id=593&dategame='.$searchDate;
					header("Location: " . "http://" . $_SERVER['HTTP_HOST'] . $location );
				}
			} 
		?>
    </body>
</html>

<?php 
do_action('generate_sidebars');
get_footer();

?>

<?php


function listGames( $searchDate )
{

		$db_hostname = 'gungahlinunitedfc.org.au';
		$db_username = 'gufcweb_dev';
		$db_password = 'deve!oper';
		$db_database = 'gufcweb_player';

		// $db_hostname = '192.168.1.12:3306';
		// $db_username = 'danielgufc_user';
		// $db_password = 'danielgufc_password';
		// $db_database = 'gufcdraws';
    	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

		$term  = $refereeselected;
		
		$sqlinner = "
					SELECT 
					 game.gameid 
					,game.fkhometeamid      fkhometeamid
					,game.fkawayteamid      fkawayteamid
					,game.fkagegroupid      fkagegroupid
					,game.fkroundid         fkroundid
					,game.fkgroundplaceid   fkgroundplaceid
					,game.referee   		referee
					,game.homejob   		homejob
					,game.time      		time
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
				";
				
		if (! $searchDate == "")
		{
			$sqlinner.=" AND round.date = '".$searchDate."'";
		}		
		$sqlinner.=" ORDER BY round.date, time, seqnum ";
					
		$r_queryinner = $mysqli->query($sqlinner);

		$todays_date = date("Y-m-d");						

		$msg = 'No games found.';	

		echo '<table class="table" align="center" border="1" >';
		echo '<th>Time</th>';
		echo '<th>Field</th>';
		echo '<th>Home</th>';
		echo '<th>Away</th>';
		echo '<th>Referee</th>';
		echo '<th>Age group</th>';
		echo '<th>Date</th>';
		echo '<th>Round</th>';
		echo '<th>Home Job</th>';
		echo '<th>Ground Address</th>';

		if ( ! $r_queryinner )
		{
			echo 'No games found for '.$refereeselected.' '; 
		}
		else
		{
			while ($rowinner = mysqli_fetch_assoc($r_queryinner))
			{	
				
				echo '<tr>';
				echo '<td>'.$rowinner['time'].'</td>';
				echo '<td>'.$rowinner['fieldid'].'</td>';
				echo '<td>'.$rowinner['fkhometeamid'].'</td>';
				echo '<td>'.$rowinner['fkawayteamid'].'</td>';
				echo '<td>';
				echo "<input type='text' name='refchange' id='refchange".$rowinner['gameid']."' value='"
				.$rowinner['referee']."' onchange='updatereferee(\"refchange"
				.$rowinner['gameid']."\",\"".$rowinner['gameid']."\",\"".$searchDate."\")'>";
				
				echo '</td>';
				echo '<td>'.$rowinner['fkagegroupid'].'</td>';
				echo '<td>'.$rowinner['rounddate'].'</td>';
				echo '<td>'.$rowinner['idround'].'</td>';
				echo '<td>'.$rowinner['homejob'].'</td>';
				echo '<td>'.$rowinner['gpaddress'].'</td>';
				echo '</tr>';

			}	

			echo '</table>';
			echo '<p/>';
		}
		
		$mysqli->close();
}
?>

