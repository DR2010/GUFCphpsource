<?php
	/*
	Template Name: Show Player Team
	*/
	get_header();

	// ----------------------
	// Show Team List
	// ----------------------

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
		<style>
		table, th, td {
			border: 1px solid black;
		}
		</style>
		<script language=JavaScript>
			function reload(form)
			{
				var val=form.agegroupselected.options[form.agegroupselected.options.selectedIndex].value;
				self.location='?page_id=489' ;
			}
		</script>     
		<meta charset="utf-8" />
        <title>Where is my team playing?</title>
    </head>
    <body>

		<?php

			echo "<form method=post name=f1 action=''>";

			// echo "The team list has been updated on 29 March 2015 at 11:05PM.";
			echo "The team list has been updated on Monday 06 April 2015 at 06:20PM. (Previously updated on 29/03/15 11:05PM)";
			// echo "This option is under maintenance 06 April 2015 at 05:15PM. It will be back shortly.";
			echo "<p/>";

			echo "<p>FFA Number:</p> ";
			echo '<input type="text" name="ffanumber">';
			echo '<p/>';
			echo "<p>Last Name:</p> ";
			echo '<input type="text" name="lastname">';
			echo '<p/>';
			echo "<p/>";
			echo "<input type=submit value=Submit>";

			echo "</form>";

			$ffanumber = $_POST['ffanumber'];
			$lastname = $_POST['lastname'];
			
			// echo $ffanumber; 
			// echo $lastname; 
			
			if ($ffanumber != "")
			{
				showteam($ffanumber, $lastname); 
			}
			
		?>
    </body>
</html>

<?php 
do_action('generate_sidebars');
get_footer();

?>

<?php

function showteam( $ffanumber, $lastname )
{

	// This is the official connection string
	$db_hostname = 'gungahlinunitedfc.org.au';
	$db_username = 'gufcweb_dev';
	$db_password = 'deve!oper';
	$db_database = 'gufcweb_player';

	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

	// echo 'inside showteam';
	// echo "<p/>";
	// echo $ffanumber;
	// echo "<p/>";
	// echo $lastname;
	
	$lastnameUpper = strtoupper( $lastname );
	$playerteam = "";
	
	$sqlplayer = "SELECT FirstName, LastName, fkteamid, fkagegroupid  FROM player where FFANumber = ".$ffanumber." and display = 'Y' ";
	// echo $sqlplayer;
	
	$qplayer = $mysqli->query($sqlplayer);
	$namematch = "NO";
	$firstname = "";
	$lastnameDB = "";
	$fkteamid = "";
	$fkagegroupid = "";
	$tbateam = "";
	
	if ( $qplayer->num_rows > 0  )
	{
		while ($rowinner = mysqli_fetch_assoc($qplayer))
		{
			$firstname = strtoupper( $rowinner['FirstName'] );
			$lastnameDB = strtoupper( $rowinner['LastName'] );
			$fkteamid = $rowinner['fkteamid'] ;
			$fkagegroupid = $rowinner['fkagegroupid'] ;
			
			$lastnameUpperNoQuote = str_replace("'","",$lastnameUpper);  
			$lastnameUpperNoQuote = str_replace("\\","",$lastnameUpperNoQuote);
			$lastnameDBNoQuote = str_replace("'","",$lastnameDB);
			
			// echo "<p/>";
			// echo 'lastname = '.$lastnameUpper.' '; 
			// echo "<p/>";
			// echo 'lastnamedb = '.$lastnameDB.' '; 
			// echo "<p/>";
			// echo 'lastnameUpperNoQuote = '.$lastnameUpperNoQuote.' ';
			// echo "<p/>";
			// echo 'lastnameDBNoQuote = '.$lastnameDBNoQuote.' ';
			// echo "<p/>";
			
			if (strcmp($lastnameUpperNoQuote, $lastnameDBNoQuote) == 0)
			{
				if ($fkteamid == "TBA")
				{
					$namematch = "NO";
					$tbateam = "YES";
				}
				else
				{
					$namematch = "YES";
				}
			}
		}
	}

	if ($namematch == "YES")
	{
		
		$sqlteam  = " SELECT FirstName,LastName,fkteamid FROM player where fkteamid = '".$fkteamid."' and display = 'Y'";
		
		$r_queryteam = $mysqli->query($sqlteam);
		
		if ( $r_queryteam->num_rows > 0  )
		{		
			echo '<table class="table" align="center" border="1" >';
			echo '<th>First Name</th>';
			echo '<th>Last Name</th>';
			echo '<th>Team Name</th>';
			
			while ($rowteam = mysqli_fetch_assoc($r_queryteam))
			{
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
	else
	{ 	
		if ($tbateam == "YES")
		{
			echo "Player found but team allocated is not available.";
		}
		else
		{
			echo "Player not found.";
		}
	}
}
?>

