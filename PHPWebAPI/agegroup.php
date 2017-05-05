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
	// $updateflag=$_GET['updateflag']; 
    
    // Block IP addresses
    
    // if ( ! $remoteip == '1.41.26.149') 120.19.67.172
    // {
    //     echo 'access denied';
    //     exit();
    // }
    
    $isallowed = authorisationipisallowed( $remoteip );
    
    if ( $isallowed !== 'true') 
    {
        echo 'access denied';
        exit();
    }
    
    $hashed = hash('md5','daniel test');
    
    echo $hashed;
    
    listAgeGroup( ); 

    function listAgeGroup( )    
    {

		$db_hostname = 'gungahlinunitedfc.org.au';
		// $db_hostname = 'ub007lcs13.cbr.the-server.net.au';
		$db_username = 'gufcweb_dev';
		$db_password = 'deve!oper';
		$db_database = 'gufcweb_player';

            $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);
            
            $sqlinner = " 
                        SELECT 
                            idagegroup
                        FROM agegroup
                    ORDER BY idagegroup 
                    ";
                        
            $r_queryinner = $mysqli->query($sqlinner);

            while ($rowinner = mysqli_fetch_assoc($r_queryinner))
            {
                
                $idagegroup=$rowinner['idagegroup']; 

                $posts[] = array(
                                'idagegroup'=> $idagegroup
                                    );

            }
            echo (json_encode($posts));
      
    }
    
?>

