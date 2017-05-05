<?php
	/*
	Template Name: List Update Games
	*/

	require_once('config.php');     
	require_once('EditableGrid.php');            

	get_header();

	$mysqli = new mysqli($config['db_host'],$config['db_user'],$config['db_password'],$config['db_name']); 

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
			
				var val=form.agegroupselected.options[form.agegroupselected.options.selectedIndex].value;
				self.location='?page_id=162&agegroupselected=' + val ;
			}
			
			function reload2(form)
			{
				var val =form.agegroupselected.options[form.agegroupselected.options.selectedIndex].value;
				var val2=form.teamselected.options[form.teamselected.options.selectedIndex].value;
				self.location='?page_id=162&agegroupselected=' + val + '&teamselected=' + val2;
			}
			
			
		</script>        
		<meta charset="utf-8" />
        <title>Where is my team playing?</title>
    </head>
    <body>

		<?php

			// Get Age Group
			// -------------
			@$agegroupselected=$_GET['agegroupselected']; 
			@$teamselected=$_GET['teamselected']; 
			
			$loc = "findgame.php";	
			$sql_agegroup="select idagegroup, description from agegroup order by idagegroup;";
			$sql_team="select fkagegroupid, idteam, name from team where fkagegroupid='".$agegroupselected."' order by fkagegroupid";

			// $query_agegroup = mysqi_query($sql_agegroup, $con);

			$query_agegroup = $mysqli->query($sql_agegroup);
			
			echo "<form method=post name=f1 action=display()>";

			echo "<p/>";
			echo "<p>Age Group:</p> ";
			echo '<label>';
			echo "<select name='agegroupselected' onchange=\"reload(this.form)\">";
			echo '<Option value="">Select Age Group   </option>';		
			while ($rowinner = mysqli_fetch_assoc($query_agegroup))
			{
				if ($rowinner['idagegroup']==@$agegroupselected)
				{
					echo "<option selected value='$rowinner[idagegroup]'>$rowinner[idagegroup]</option>"."<BR>";
				}
				echo '<Option value="'.$rowinner['idagegroup'].'">'.$rowinner['idagegroup'].'</option>';	
			}
			echo '</select>';
			echo '</label>';
			// --------------
			
			// Get Team List
			// -------------
			echo '<p/>';
			
			$query_team = $mysqli->query($sql_team);

			echo "<p>Team:</p> ";
			echo '<label>';
			echo "<select name='teamselected' onchange=\"reload2(this.form)\">";
			echo '<Option value="">Select Team   </option>';		
			
			$found = "N";
			while ($rowinnerx = mysqli_fetch_assoc($query_team))
			{
				if ($rowinnerx['idteam']==@$teamselected)
				{
					echo "<option selected value='$rowinnerx[idteam]'>$rowinnerx[idteam]</option>";	
					
					$found = "Y";
				}
				echo "<Option value='$rowinnerx[idteam]'>$rowinnerx[idteam]</option>";	
			}
			echo '</select>';
			echo '</label>';
			echo '<p/>';
			// echo "<input type=submit value=Submit>";

			echo "</form>";

			if ($found = "Y")
			{
				listGames($agegroupselected, $teamselected); 
			}
			
		?>
    </body>
</html>

<?php 
do_action('generate_sidebars');
get_footer();

?>

<?php

/**
 * fetch_pairs is a simple method that transforms a mysqli_result object in an array.
 * It will be used to generate possible values for some columns.
*/
function fetch_pairs($mysqli,$query){
	if (!($res = $mysqli->query($query)))return FALSE;
	$rows = array();
	while ($row = $res->fetch_assoc()) {
		$first = true;
		$key = $value = null;
		foreach ($row as $val) {
			if ($first) { $key = $val; $first = false; }
			else { $value = $val; break; } 
		}
		$rows[$key] = $value;
	}
	return $rows;
}

function listGames( $agegroupselected, $teamselected )
{
		$mysqli = new mysqli($config['db_host'],$config['db_user'],$config['db_password'],$config['db_name']); 

		// create a new EditableGrid object
		$grid = new EditableGrid();

		$grid->addColumn('gameid', 'gameid', 'integer', NULL, false); 
		$grid->addColumn('fkhometeamid', 'fkhometeamid', 'string');  
		$grid->addColumn('fkawayteamid', 'fkawayteamid', 'string');  
		$grid->addColumn('fkroundid', 'fkroundid', 'integer');  

		/* The column id_country and id_continent will show a list of all available countries and continents. So, we select all rows from the tables */
		// $grid->addColumn('id_continent', 'Continent', 'string' , fetch_pairs($mysqli,'SELECT id, name FROM continent'),true);  
		
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
				WHERE 
				 ( game.fkhometeamid = '".$term."' OR game.fkawayteamid = '".$term."')
				
				   AND game.fkagegroupid = '".$agegroupselected."' 
				   AND game.fkgroundplaceid = groundplace.idgroundplace 
				   AND game.fkroundid = round.idround 
				   AND game.fkfieldid = harrisonsfieldschema.fieldid

				ORDER BY round.date
				";
					
		$r_queryinner = $mysqli->query($sqlinner);
		
		// send data to the browser
		$grid->renderXML($result);
}
?>

