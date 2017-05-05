    <?php
	/*
	Template Name: Age Group
	*/
	
	// This is the official connection string

		// $db_hostname = 'gungahlinunitedfc.org.au';
		// $db_username = 'gufcweb_readmain';
		// $db_password = 'r&admain2015';
		// $db_database = 'gufcweb_wordpress';

        // ub007lcs13.cbr.the-server.net.au
		$db_hostname = 'gungahlinunitedfc.org.au';
		// $db_hostname = 'ub007lcs13.cbr.the-server.net.au';
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
	$indate=$_GET['indate']; 
    
    // Block IP addresses
    
    // if ( ! $remoteip == '1.41.26.149')
    // {
    //     echo 'access denied';
    //     exit();
    // }
    
    settings(); 

    function settings()
    {

		$db_hostname = 'gungahlinunitedfc.org.au';
		// $db_hostname = 'ub007lcs13.cbr.the-server.net.au';
		$db_username = 'gufcweb_dev';
		$db_password = 'deve!oper';
		$db_database = 'gufcweb_player';

        $posts[] = array( 'hostname'=> $db_hostname );

        echo (json_encode( 'hostname'.$db_hostname ) );
    }
   
?>

