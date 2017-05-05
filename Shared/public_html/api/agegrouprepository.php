<?php
	/*
	Age Group
	*/
    include 'DBFunctions.php';
	
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
    
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $remoteipforwarded = $_SERVER['HTTP_X_FORWARDED_FOR']; // easily spoofed
    $requestmethod = $_SERVER['REQUEST_METHOD'];
    $action=$_GET['action']; 
    $agegroupid=$_GET['agegroupid']; 
    $description=$_GET['description']; 
    $showindropdown=$_GET['showindropdown']; 
    
    // Block IP addresses
    
    // if ( ! $remoteip == '1.41.26.149') 120.19.67.172
    // {
    //     echo 'access denied';
    //     exit();
    // }
    
    $isallowed = authorisationipisallowed( $remoteip );
    
    // if ( $isallowed !== 'true') 
    // {
    //     echo 'access denied';
    //     exit();
    // }
    
    $hashed = hash('md5','daniel test');
    
    // echo $hashed;
    
    
    switch ($action) {
    case 'get':
        get( $agegroupid ); 
        break;
    case 'list':
        listAgeGroup();
        break;
    case 'add':
        insert( $agegroupid, $description, $showindropdown );
        break;
     }
    
    
    
    class AgeGroup
    {   
        var $idagegroup;
        var $description;
        var $showindropdown;
    }

    function listAgeGroup( )    
    {

        global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

        $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

        $sqlinner = " 
                    SELECT 
                        idagegroup,
                        description,
                        showindropdown
                    FROM agegroup
                ORDER BY idagegroup 
                ";
                    
        $r_queryinner = $mysqli->query($sqlinner);

        $agegrouplist = array();
        if ( $r_queryinner )
        {
            while ($rowinner = mysqli_fetch_assoc($r_queryinner))
            {
                $agegroup = new AgeGroup();
                $agegroup->idagegroup = strtoupper($rowinner['idagegroup']); 
                $agegroup->description = strtoupper($rowinner['description']); 
                $agegroup->showindropdown = strtoupper($rowinner['showindropdown']); 
                $agegrouplist[] = $agegroup;
                
            }
        }

        echo ( json_encode( $agegrouplist ) );
    }
    
    
    
    function get( $inid )    
    {

        global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

        $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

        $sqlinner = " 
                    SELECT 
                        idagegroup,
                        description,
                        showindropdown
                    FROM agegroup
                    WHERE idagegroup = '".$inid."'                    
                ";
                    
        $r_queryinner = $mysqli->query($sqlinner);

        $agegrouplist = array();
        $agegroup = new AgeGroup(); 
        $agegroup->idagegroup = "Not Found";

        if ( $r_queryinner )
        {
            while ($rowinner = mysqli_fetch_assoc($r_queryinner))
            {

                $agegroup->idagegroup = strtoupper($rowinner['idagegroup']); 
                $agegroup->description = strtoupper($rowinner['description']); 
                $agegroup->showindropdown = strtoupper($rowinner['showindropdown']); 
                $agegrouplist[] = $agegroup;
                
                break;
                
            }
        }
        // echo $sqlinner;
        echo ( json_encode($agegroup) );
      
    }
    
    
    
        //
    //
    //
    function insert( $idagegroup, $description, $showindropdown )
    {
        
        
        global $db_hostname,$db_username,$db_password, $db_database, $mysqli;  

        $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

        $results = "Not good.";

        $agegroup = new AgeGroup();
        $agegroup->idagegroup = $idagegroup;
        $agegroup->description = $description;
        $agegroup->showindropdown = $showindropdown;

         $sql =  "INSERT INTO agegroup 
            (
              idagegroup   
            , description  
            , showindropdown   
            ) 
                 VALUES ('" 
               .$idagegroup. 
            "','".$description. 
            "','".$showindropdown.
            "')";

        if ( $idagegroup== "" ||  $description == "" )
        {
            // do nothing
            echo ' fields empty </p>';
            echo $sql;
        }
        else
        {
        // echo 'sql..';
        // echo $sql;
    
            if ($mysqli->query($sql) === TRUE) {
                
                // echo "Record updated successfully";
                $results = "Good";
            } else {
                // echo "Error updating record: " . $mysqli->error;
                $results = "Error updating record: " . $mysqli->error;
            }
        }
    //	echo '</p>';
    //	echo $gameid;
    //	echo '</p>';
    //	echo $refereeid;

        $mysqli->close();
        
        // echo $results;
        // echo $sql;
        return $results;
        
        
    }
    
?>

