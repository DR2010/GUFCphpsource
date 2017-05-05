<?php
	/*
	Template Name: Allocate Referees
	*/
	get_header();

	
	include 'DAreferees.php';
	include 'DAGames.php';

	 // $db_hostname = 'localhost:3306';
	 // $db_username = 'root';
	 // $db_password = 'oculos';
	 // $db_database = 'gufcweb_player';
	
	// This is the official connection string
	$db_hostname = 'gungahlinunitedfc.org.au';
	$db_username = 'gufcweb_dev';
	$db_password = 'deve!oper';
	$db_database = 'gufcweb_player';

	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

	/* check connection */
	if ($mysqli ->connect_errno) {
		printf("Connect failed: %s\n", $mysqli->connect_error);
		exit();
	}

    // phpinfo();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
		<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<script type=text/javaScript>
			function reload(form)
			{
				var val=document.getElementById('searchDate');
				var timeselected=document.getElementById('timeselected');
//				window.alert(" Date... " + val.value);
//				window.alert(" Date... " + location.value);
                self.location = '?page_id=593&dategame=' + val.value +'&timeselected=' + timeselected.value ;
                // self.location = '?page_id=593&dategame=' + val.value;
			}

			function allocatereferee(form)
			{
				var date=document.getElementById('searchDate');
				self.location = '?page_id=593&dategame=' + date.value + '&allocate=yes';
			}

		</script>

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
			
		<meta charset="utf-8" />
        <title>Where is my team playing?</title>
    </head>
    <body>

    	<?php
			@$dategame=$_GET['dategame'];
			@$timeselected=$_GET['timeselected'];
			@$allocate=$_GET['allocate'];

			$sql_agegroup="select idagegroup, description from agegroup where showindropdown = 'Y' order by idagegroup;";
			$query_agegroup = $mysqli->query($sql_agegroup);
			
			echo "<form method='POST' name='formname' action=''>";
			echo '<br>';
			echo 'Date:';
			echo '<p/>';
			echo "<input type='date' name='dategame' id='searchDate' value='".$dategame."' id=\"searchDate\">";
			echo '<p/>';

			echo "<p>Game Time:</p> ";
			echo '<label>';
			echo "<select name='timeselected' id='timeselected'  >";
			echo '<Option value="">Select Time   </option>';

            $listOfTimes = getListOfTimes("ALL","N");
            foreach( $listOfTimes as $time )
            {
                if ($time==@$timeselected)
                    echo '<option selected value="'.$time.'">'.$time.'</option>';
                else
                    echo '<option value="'.$time.'">'.$time.'</option>';
            }
			echo '</select>';
			echo '<p/>';
			echo '<p>';

			echo "<input type='button' name='submit' value='Submit' onclick=\"reload(this.form)\">";
			echo "<input type='button' name='allocate' value='Allocate' onclick=\"allocatereferee(this.form)\">";

			echo '</p>';
			echo "</form>";

		if (!$dategame == "") {

			echo "<h1>Summary of current referees allocated by time</h1> ";
			showGamesMatrix($dategame);

			echo '<h1>Summary of referees availability </h1>';
			$tempreferee = array();
			showListReferees($dategame, $tempreferee);

			if ($allocate == "") {
				listGamesAgeGroup($dategame, $timeselected);
			}

			if ($allocate == "yes") {

				echo '<h1>Referees Allocated to Games by the System</h1>';
				$gamesall = allocateRefereesToGames($dategame);

				echo '<h1>Summary of referees allocated by the system</h1>';
  				showListReferees($dategame, $gamesall);
			}
		}
		?>

    </body>
</html>

<?php 
do_action('generate_sidebars');
get_footer();
?>

