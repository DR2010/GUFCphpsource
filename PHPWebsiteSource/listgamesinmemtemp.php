<?php
	/*
	Template Name: List Games
	*/
	get_header();

	// -------------------------------------------
	// 
	// List all games for a team in the season
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
    $selectedagegroup = "";

    $cachedlistofdagegroups = array();
    $cachedlistofteams = array();

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
        
        <style>
            table {
              border-collapse: separate;
              border-spacing: 0;
            }
            th,
			td {
			  padding: 10px 15px;
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
        
    	<script language=JavaScript>
			function setselectedagegroup(form)
			{
				var val=form.agegroupselected.options[form.agegroupselected.options.selectedIndex].value;
				$selectedagegroup = val;
			}
		
			function reload(form)
			{
				var val=form.agegroupselected.options[form.agegroupselected.options.selectedIndex].value;
				self.location='?page_id=1227&agegroupselected=' + val ;
			}

			function sayhi()
			{
				window.alert(" Hi " );
			}
			
			function reload2(form)
			{
				var val =form.agegroupselected.options[form.agegroupselected.options.selectedIndex].value;
				var val2=form.teamselected.options[form.teamselected.options.selectedIndex].value;
				self.location='?page_id=162&agegroupselected=' + val + '&teamselected=' + val2;
			}

            function configureDropDownLists( form, ddl2 ) 
            {
				global $cachedlistofteams;

				var val=form.agegroupselected.options[form.agegroupselected.options.selectedIndex].value;
				$selectedagegroup = val;

				window.alert(" Date... " + $selectedagegroup);

				ddl2.options.length = 0; 
				foreach ( $cachedlistofteams as $team ) 
				{
				if ($team->fkagegroupid != $selectedagegroup) continue;

				createOption($team->$teamid);
				}

            }

            function createOption(ddl) {
                var opt = document.createElement('option');
                opt.value = ddl;
                ddl.options.add(opt);
            }
        </script>
        
	
		<meta charset="utf-8" />
        <title>List all games for a team in the season</title>
    </head>
    <body>

		<?php
        
        class Team
        {   
            var $fkagegroupid;
            var $teamid;
        }

        class AgeGroup
        {   
            var $agegroupid;
            var $description;
        }


		// Get Age Group
		// -------------
		@$agegroupselected=$_GET['agegroupselected']; 
		@$teamselected=$_GET['teamselected']; 

		$loc = "findgame.php";	
		$sql_agegroup="select idagegroup, description from agegroup where showindropdown = 'Y' order by idagegroup;";

		$sql_team="select fkagegroupid, idteam, name from team where showindropdown = 'Y' and fkagegroupid like '%".$agegroupselected."%' order by fkagegroupid, idteam";

        // If nothing is passed in, load everything in memory
        //
		
        if ($agegroupselected == "") 
        {
            $cachedlistofdagegroups = loadagegroups();
            $cachedlistofteams =loadteams();

        }

		$query_agegroup = $mysqli->query($sql_agegroup);

		echo "<form method=post name=f1 action=display()>";

		echo "<p/>";
		echo "<p>Age Group:</p> ";
		echo '<label>';
		echo "<select name='agegroupselected' onchange=\"reload(this.form)\">";
		echo '<Option value="">Select Age Group   </option>';	
		
        foreach ($cachedlistofdagegroups as $agegroup) 
        {
            if ($agegroup==$agegroupselected)
            {
                echo "<option selected value='$agegroup->agegroupid'>$agegroup->agegroupid</option><BR>";
            }
            else
            {
                echo "<Option value='$agegroup->agegroupid'>$agegroup->agegroupid</option><BR>";
            }
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
		echo "<select id='teamelid' name='teamselected' onchange=\"reload2(this)\">";
		echo '<Option value="">Select Team   </option>';		

		$found = "N";
        foreach ( $cachedlistofteams as $team ) 
        {
            if ($team->fkagegroupid != $selectedagegroup) continue; 
            
            if ($team==$teamselected)
            {
                echo "<option selected value='".$team->teamid."'>$team->teamid</option>";    
                $found = "Y";
            }
            else
            {
                echo '<Option value="'.$team->teamid.'">'.$team->teamid.'</option>';    
            } 
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

function loadagegroups()
{
    
    global $db_hostname, $db_username, $db_password, $db_database;
    $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);
    
    $sql_agegroup="select idagegroup, description from agegroup where showindropdown = 'Y' order by idagegroup;";

    $query_agegroup = $mysqli->query($sql_agegroup);

    $listofdagegroups = array();
    $agegroup = new AgeGroup();

    while ($rowinner = mysqli_fetch_assoc($query_agegroup))
    {
        $agegroup = new AgeGroup();
        $agegroup->agegroupid  = $rowinner[idagegroup];
        $agegroup->description = $rowinner[description];
        $listofdagegroups[] = $agegroup;
    }

    return $listofdagegroups;
    
}

// --------------------------------------------------
//         List teams
// --------------------------------------------------
function loadteams()
{
    
        
    global $db_hostname, $db_username, $db_password, $db_database;
    $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);
    
    $sql_team="select fkagegroupid, idteam, name from team where showindropdown = 'Y' order by fkagegroupid, idteam";

    $query_team = $mysqli->query($sql_team);

    $listofteams = array();
    $team = new Team();


    while ($rowinner = mysqli_fetch_assoc($query_team))
    {
        $team = new Team();
        $team->fkagegroupid = $rowinner['fkagegroupid'];
        $team->teamid = $rowinner['idteam'];

        $listofteams[] = $team;
    }

    return $listofteams;
    
}


function listGames( $agegroupselected, $teamselected )
{

    global $db_hostname, $db_username, $db_password, $db_database;
  	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

	$term  = $teamselected;
		
	$agegroupcompare = $agegroupselected . "%";
		
	$sqlinner = "
				SELECT 
				 game.gameid            gameid
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
				,game.fkfieldid fieldid
				,case 
					when ( round.date >= SUBDATE(now(), INTERVAL 1 DAY) and date(round.date) < ADDDATE( now(), INTERVAL 6 DAY)) then '< NEXT' 
					else ''
				end as 'next'
			 FROM game, round, groundplace
			WHERE 
			 ( game.fkhometeamid = '".$term."' OR game.fkawayteamid = '".$term."')

			   AND game.fkagegroupid like '".$agegroupcompare."' 
			   AND game.fkgroundplaceid = groundplace.idgroundplace 
			   AND game.fkroundid = round.idround 

			ORDER BY round.date
			";

	$r_queryinner = $mysqli->query($sqlinner);

	$todays_date = date("Y-m-d");						

	$msg = 'No games found.';	

	echo '<table class="rwd-table" align="center" border="1" >';

    echo '<th>Age group</th>';
	echo '<th>____Date____</th>';
	echo '<th>Time</th>';
	echo '<th>Next</th>';
	echo '<th>Round</th>';
	echo '<th>Home</th>';
	echo '<th>Away</th>';
	echo '<th>Referee</th>';
	echo '<th>Home Job</th>';
	echo '<th>Ground</th>';
	echo '<th>____Ground Address____</th>';
	echo '<th>__SystemID__</th>';

	if ( ! $r_queryinner )
	{
		echo 'No games found on date '.$dategame.' '; 
	}
	else
	{
		while ($rowinner = mysqli_fetch_assoc($r_queryinner))
		{	
			echo "\n";
			echo '<tr>';
			echo '<td>'.$rowinner['fkagegroupid'].'</td>';
			echo '<td>'.$rowinner['rounddate'].'</td>';
			echo '<td>'.$rowinner['time'].'</td>';
			echo '<td>'.$rowinner['next'].'</td>';
			echo '<td>'.$rowinner['idround'].'</td>';
			echo '<td>'.strtoupper($rowinner['fkhometeamid']).'</td>';
			echo '<td>'.strtoupper($rowinner['fkawayteamid']).'</td>';
			echo '<td>'.$rowinner['referee'].'</td>';
			echo '<td>'.$rowinner['homejob'].'</td>';
			echo '<td>'.$rowinner['fkgroundplaceid'].'</td>';
			echo '<td>'.$rowinner['gpaddress'].'</td>';
			echo '<td>'.$rowinner['gameid'].'</td>';
			echo '</tr>';

		}	

		echo '</table>';
		echo '<p/>';
	}
}
?>

