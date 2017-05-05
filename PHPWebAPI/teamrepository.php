<?php
	/*
	Team Repository
	*/
	
    $db_hostname = 'gungahlinunitedfc.org.au';
    $db_username = 'gufcweb_dev';
    $db_password = 'deve!oper';
    $db_database = 'gufcweb_player';

	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

	/* check connection */
	if ($mysqli ->connect_errno) {
		printf("Connection failed: %s\n", $mysqli->connect_error);
		exit();
	}
	
    $requestmethod = $_SERVER['REQUEST_METHOD'];
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $remoteipforwarded = $_SERVER['HTTP_X_FORWARDED_FOR']; // easily spoofed
	
    $action=$_GET['action'];
    $infkagegroupid=$_GET['infkagegroupid']; 
	$inteamid=$_GET['inteamid']; 
    $name=$_GET['name'];
    $showindropdown=$_GET['showindropdown'];
    $IsGungahlinTeam=$_GET['IsGungahlinTeam'];
    $division=$_GET['division'];
    
    // Block IP addresses
    
    // if ( ! $remoteip == '1.41.26.149')
    // {
    //     echo 'access denied';
    //     exit();
    // }

    // http://gungahlinunitedfc.org.au/api/gamerepository.php?action=getgamebyid&ingameid=GIRLS012
    
    // http://gungahlinunitedfc.org.au/api/gamerepository.php?action=getteam&infkagegroupid=U5&inteamid=U5060
    
    
    switch ($action) {
    case 'getteam':
        getTeam( $infkagegroupid, $inteamid ); 
        break;
    case 'listteams':
        listteams(); 
        break;
    case 'addteam':
        insertteam( $infkagegroupid, $inteamid, $name, $showindropdown, $IsGungahlinTeam, $division );
        break;
    }
    
    // if (  $gameid != "") {
    //     getGame( $gameid ); 
    // }
    // else {
    //     GetGameIDByOther( $indate, $inhometeam, $inawayteam, $inagegroupid );
    // }
        
        
        
        
        
    class Team
    {   
        var $FKAgeGroupID;
        var $UID;
        var $FKDivisionID;
        var $NameID;
        var $PublicName;
        var $showindropdown;
        var $IsGungahlinTeam;

    }

    // -------------------------------------------------------------------------
    //   Retrieve team details
    // -------------------------------------------------------------------------
    function getTeam( $infkagegroupid, $inteamid )
    {

        global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

        $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

       $sqlinner = "
                 SELECT 
                 fkagegroupid   
                 ,idteam 
                 ,division 
                 ,name 
                 ,showindropdown
                 ,IsGungahlinTeam 
                  FROM team 
                 WHERE idteam = '".$inteamid."' and fkagegroupid like '%" . trim($infkagegroupid) . "%' ";
                    
        $r_queryinner = $mysqli->query($sqlinner);

        $teamlist = array();
        
        $team = new Team();
        $team->NameID = "Not found";
        
        
        if ( $r_queryinner )
        {
            while ($rowinner = mysqli_fetch_assoc($r_queryinner))
            {


                $team->FKAgeGroupID = $rowinner['fkagegroupid'];
                $team->UID = $rowinner['idteam'];
                $team->FKDivisionID = $rowinner['division'];
                $team->NameID = $rowinner['name'];
                $team->showindropdown = $rowinner['showindropdown'];
                $team->IsGungahlinTeam = $rowinner['IsGungahlinTeam'];

                $teamlist[] = $team;
                
                break;

            }
        }

       // echo $sqlinner;

        echo (json_encode( $team ));
    }

    // -----------------------------------------------------------------
    //    Insert Team 
    // -----------------------------------------------------------------
    function insertteam( $fkagegroupid, $idteam, $name, $showindropdown, $IsGungahlinTeam, $division )
    {
        echo '<p/>fkagegroupid: ';
        echo $fkagegroupid;
        echo '<p/>idteam: ';
        echo $idteam;
        echo '<p/>name: ';
        echo $name;
        echo '<p/>showindropdown : ';
        echo $showindropdown;
        echo '<p/>IsGungahlinTeam :';
        echo $IsGungahlinTeam;
        echo '<p/>division: ';
        echo $division;
        echo '<p/>';
        
        
        
        global $db_hostname,$db_username,$db_password, $db_database, $mysqli;  

        $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);


        if ( $fkagegroupid == "" || $idteam == "" || $name == ""  )
        {
            // do nothing
            echo 'empty parms';
        }
        else
        {
            // echo 'sql..';
            
            $sql = 
                "
                   INSERT INTO team 
                   (
                     fkagegroupid   
                     ,idteam 
                     ,name 
                     ,showindropdown 
                     ,IsGungahlinTeam 
                     ,division 
                   )
                     VALUES  
                   ( 
                      '".$fkagegroupid.  "','"
                       .$idteam.         "','"
                       .$name.           "','"
                       .$showindropdown. "','"
                       .$IsGungahlinTeam."','"
                       .$division.
                      "')"; 
            
            echo $sql;
        
            if ($mysqli->query($sql) === TRUE) {
    //		    echo "Record updated successfully";
            } else {
    //		    echo "Error updating record: " . $mysqli->error;
            }
            
            // echo $sql;
            
        }
    //	echo '</p>';
    //	echo $gameid;
    //	echo '</p>';
    //	echo $refereeid;

        $mysqli->close();
    }




    // -------------------------------------------------------------------------
    //   List all teams
    // -------------------------------------------------------------------------
    function listteams()
    {

        global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

        $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

       $sqlinner = "
                 SELECT 
                 fkagegroupid   
                 ,idteam 
                 ,division 
                 ,name 
                 ,showindropdown
                 ,IsGungahlinTeam 
                  FROM team 
                 ORDER BY fkagegroupid, idteam ";
                    
        $r_queryinner = $mysqli->query($sqlinner);

        $teamlist = array();
        

        
        if ( $r_queryinner )
        {
            while ($rowinner = mysqli_fetch_assoc($r_queryinner))
            {

                $team = new Team();
                $team->FKAgeGroupID = strtoupper($rowinner['fkagegroupid']);
                $team->UID = $rowinner['idteam'];
                $team->FKDivisionID = $rowinner['division'];
                $team->NameID = strtoupper($rowinner['name']);
                $team->showindropdown = $rowinner['showindropdown'];
                $team->IsGungahlinTeam = $rowinner['IsGungahlinTeam'];

                $teamlist[] = $team;

            }
        }

       // echo $sqlinner;

        echo (json_encode( $teamlist ));
    }
   
?>

