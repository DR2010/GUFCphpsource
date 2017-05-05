<?php

	// This is the official connection string
	$db_hostname = 'gungahlinunitedfc.org.au';
	$db_username = 'gufcweb_dev';
	$db_password = 'deve!oper';
	$db_database = 'gufcweb_player';
	$mysqli = "";


function startdb()
{
	
	global $db_hostname,$db_username,$db_password, $db_database, $mysqli;  
	
	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

	/* check connection */
	if ($mysqli ->connect_errno) {
		printf("Connect failed: %s\n", $mysqli->connect_error);
		exit();
	}
}

// ----------------------------------------------------------------------------------------
//    Dropdown for Age Group
// ----------------------------------------------------------------------------------------
function dropdownAny( $defaultValue, $keyfield, $table, $label, $ReadOnly, $extracondition )
{
	
	global $db_hostname,$db_username,$db_password, $db_database, $mysqli;  
	
	// startdb();

	$sql_stmt="select ".$keyfield." idkey from ".$table." ".$extracondition." order by 1;";
	
	$query_stmt = $mysqli->query($sql_stmt);

	if ($ReadOnly == "ReadOnly") 
	{
		echo $label.' <input type="text" name="'.$label.'" value="'.$defaultValue.'" readonly></br>';
	}
	else
	{
		echo $label;
		echo '<label>';
		echo "<select name='".$table."'>";
		echo '<Option value="">Select...</option>';
		
		while ($rowinner = mysqli_fetch_assoc($query_stmt))
		{
			if ($rowinner['idkey']==@$defaultValue)
			{
				echo "<option selected value='$rowinner[idkey]'>$rowinner[idkey]</option>"."<BR>";
			}
			else
			{
				echo '<Option value="'.$rowinner['idkey'].'">'.$rowinner['idkey'].'</option>';
			}
		}
		echo '</select>';
		echo '</label>';
	}
	echo '<p/>';

}

function updateRefereeDB( $gameid, $refereeid )
{


	global $db_hostname,$db_username,$db_password, $db_database, $mysqli;  

   	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

	if ( $gameid == "" || $refereeid == "" )
	{
		// do nothing
	}
	else
	{
		$sql = "UPDATE game SET referee='".$refereeid."' WHERE gameid ='".$gameid."'";
	
		if ($mysqli->query($sql) === TRUE) {
//		    echo "Record updated successfully";
		} else {
//		    echo "Error updating record: " . $mysqli->error;
		}
	}
//	echo '</p>';
//	echo $gameid;
//	echo '</p>';
//	echo $refereeid;

	$mysqli->close();
}

?>
