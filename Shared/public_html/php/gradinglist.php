<?php
	/*
	Template Name: Grading List
	*/
	
	// This is the official connection string


		$db_hostname = 'gungahlinunitedfc.org.au';
		$db_username = 'gufcweb_readmain';
		$db_password = 'r&admain2015';
		$db_database = 'gufcweb_wordpress';

	$mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

	/* check connection */
	if ($mysqli ->connect_errno) {
		printf("Connect failed: %s\n", $mysqli->connect_error);
		exit();
	}
	
    
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $remoteipforwarded = $_SERVER['HTTP_X_FORWARDED_FOR']; // easily spoofed
	$updateflag=$_GET['updateflag']; 
    
    // Block IP addresses
    
    if ( ! $remoteip == '1.41.133.120')
    {
        echo 'access denied';
        exit();
    }
    
   
    listGradingPlayers( $updateflag ); 

    function listGradingPlayers( $updateflag )
    {


            $db_hostname = 'gungahlinunitedfc.org.au';
            $db_username = 'gufcweb_readmain';
            $db_password = 'r&admain2015';
            $db_database = 'gufcweb_wordpress';

            // $db_hostname = '192.168.1.12:3306';
            // $db_username = 'danielgufc_user';
            // $db_password = 'danielgufc_password';
            // $db_database = 'gufcdraws';



            $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);
            
            $sqlinner = " 
                        SELECT 
                            UID, NAME, DOB, PHONE, EMAIL, AGEGROUP, SUBMISSIONID, 
                            IPADDRESS, USERID, USERNAME, USEREMAIL, EMAILSENTIND                    
                        FROM wp_manual_EOI_player
                    ORDER BY UID 
                    ";
                        
            $r_queryinner = $mysqli->query($sqlinner);

            while ($rowinner = mysqli_fetch_assoc($r_queryinner))
            {
                
                
                $uid=$rowinner['UID']; 
                $name=$rowinner['NAME']; 
                $dob=$rowinner['DOB']; 
                $phone=$rowinner['PHONE']; 
                $email=$rowinner['EMAIL']; 
                $agegroup=$rowinner['AGEGROUP']; 
                $submissionid=$rowinner['SUBMISSIONID']; 
                $ipaddress=$rowinner['IPADDRESS']; 
                $userid=$rowinner['USERID']; 
                $username=$rowinner['USERNAME']; 
                $useremail=$rowinner['USEREMAIL']; 
                $emailsentind=$rowinner['EMAILSENTIND']; 

                $posts[] = array(
                                'uid'=> $uid, 
                                'name'=> $name,
                                'dob'=> $dob,
                                'phone'=> $phone,
                                'email'=> $email,
                                'agegroup'=> $agegroup,
                                'submissionid'=> $submissionid,
                                'ipaddress'=> $ipaddress,
                                'userid'=> $userid,
                                'username'=> $username,
                                'useremail'=> $useremail,
                                'emailsentind'=> $emailsentind
                                    );

            }
            echo (json_encode($posts));
            
            
            
            // -----------------------------------
            // Update flag 
            // -----------------------------------

            if ($updateflag == "Y")
            {
                $mysqliUP = new mysqli($db_hostname,$db_username,$db_password, $db_database);

                $sqlupdate = "UPDATE wp_manual_EOI_player SET EMAILSENTIND='Y' WHERE uid > 1";
                if ( $mysqliUP->query($sqlupdate) === TRUE)
                {
                    // ok
                }
                else
                {
                    echo "Error : " .$mysqliUP->error; 
                    exit();
                }
                $mysqliUP->close();
            }
            // -----------------------------------
            // End - Update flag 
            // -----------------------------------

    }
    
?>

