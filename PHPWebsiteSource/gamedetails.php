<?php
	/*
	Template Name: Game Details
	*/
	get_header();

	include 'DBFunctions.php';

	// Store details of selected game in session
	session_start(); 

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
	
		<script language=JavaScript>
			function reload(form)
			{
				var val=form.refereeselected.options[form.refereeselected.options.selectedIndex].value;
				self.location='?page_id=174&refereeselected=' + val ;
			}

			function updateRefField(form)
			{
				var referee=form.referee.options[form.referee.options.selectedIndex].value;
				var fieldid=form.agegroup.options[form.agegroup.options.selectedIndex].value;
				@$gameid=$_GET['gameid']; 

				updateRefereeField($gameid, referee, field);
	
			}
		
			
		</script>        
		<meta charset="utf-8" />
        <title>Where is my team playing?</title>
    </head>
    <body>

		<?php

			// Get Age Group
			// -------------
			@$gameid=$_GET['gameid']; 

			echo "<form>";
			if (!$gameid == "")
			{
				showGameDetails($gameid); 
			}
			echo "<input type='button' value='Submit' onclick=\"updateRefField(this.form)\">";
			echo "</form>";
		?>
    </body>
</html>

<?php 
do_action('generate_sidebars');
get_footer();

?>

<?php


// ---------------------------------------------------------------------
//  Update Referee Field
// ---------------------------------------------------------------------
function updateRefereeField( $gameid, $referee, $field )
{
	global $db_hostname,$db_username,$db_password, $db_database, $mysqli;  
	
	echo "Updating Referee....";
	
}

// ---------------------------------------------------------------------
//  Display game details
// ---------------------------------------------------------------------
function showGameDetails( $gameid )
{

	// using the following global variables
	//
	global $db_hostname,$db_username,$db_password, $db_database, $mysqli;  

	$term  = $gameid;
	
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
				,round.idround          idround
				,round.date             rounddate
				,groundplace.navigate   gpnavigate
				,groundplace.address 	gpaddress 
				,harrisonsfieldschema.fieldid fieldid
				,harrisonsfieldschema.imagelocation locationinfield

			 FROM game, round, groundplace, harrisonsfieldschema
			WHERE 
				   game.gameid = '".$term."'
			   AND game.fkgroundplaceid = groundplace.idgroundplace 
			   AND game.fkroundid = round.idround 
			   AND game.fkfieldid = harrisonsfieldschema.fieldid

			";
			
				
	$r_queryinner = $mysqli->query($sqlinner);

//		$todays_date = date("Y-m-d");						


	$msg = 'No games found.';	

	if ( ! $r_queryinner )
	{
		echo 'No games found for '.$refereeselected.' '; 
	}
	else
	{
		while ($rowinner = mysqli_fetch_assoc($r_queryinner))
		{	
			
			echo 'Game ID: <input type="text" name="time" value="'.$rowinner["gameid"].'" readonly></br>';
			echo '</p>';
			echo 'Time: <input type="text" name="time" value="'.$rowinner["time"].'" readonly></br>';
			echo '</p>';
			dropdownAny( $rowinner["fieldid"], "fieldid", "harrisonsfieldschema", "Field:", "Edit", "" );
			echo '</p>';
			echo 'Home Team: <input type="text" name="time" value="'.$rowinner["fkhometeamid"].'" readonly></br>';
			echo '</p>';
			echo 'Away Team: <input type="text" name="time" value="'.$rowinner["fkawayteamid"].'" readonly></br>';
			echo '</p>';
			// dropdownAny( $defaultValue, $keyfield, $table, $label, $ReadOnly, $extracondition )
			dropdownAny( $rowinner["referee"], "idreferee", "referee", "Referee:", "Edit", "" );
			echo '</p>';
			dropdownAny( $rowinner["fkagegroupid"], "idagegroup", "agegroup", "Age Group:", "ReadOnly", "" );
			echo '</p>';
			echo 'Round Date: <input type="text" name="time" value="'.$rowinner["rounddate"].'" readonly></br>';
			echo '</p>';
			echo 'Round: <input type="text" name="time" value="'.$rowinner["idround"].'" readonly></br>';
			echo '</p>';
			echo 'Home Job: <input type="text" name="time" value="'.$rowinner["homejob"].'" readonly></br>';
			echo '</p>';
			echo 'Address: <input type="text" name="time" value="'.$rowinner["gpaddress"].'" readonly></br>';
			echo '</p>';

		}	

		echo '<p/>';
	}
}
?>

