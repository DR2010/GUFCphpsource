<?php

	// This is the official connection string
	$db_hostname = 'gungahlinunitedfc.org.au';
	$db_username = 'gufcweb_dev';
	$db_password = 'deve!oper';
	$db_database = 'gufcweb_player';
	$mysqli = "";

    if ( isset($_POST['gameid']) )  {
		
		$gameid = $_POST['gameid'];
		$refereeid = $_POST['refereeid'];
		
        $result = updateRefereeDB($gameid, $refereeid);
    }
	
	echo json_encode($result);
	
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
		    echo "Record updated successfully";
		} else {
		    echo "Error updating record: " . $mysqli->error;
		}

	}
	echo '</p>';
	echo $gameid;
	echo '</p>';
	echo $refereeid;

	$mysqli->close();
	
	return $gameid.$refereeid;
}

?>