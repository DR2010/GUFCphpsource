<?php

	// This is the official connection string
	$db_hostname = 'gungahlinunitedfc.org.au';
	$db_username = 'gufcweb_dev';
	$db_password = 'deve!oper';
	$db_database = 'gufcweb_player';
	$mysqli = "";
	
    class tokeninfo
    {
        var $userid;
        var $password;
        var $ipaddress;
    }
    
    function istokenvalid( $token )
    {
        $isallowed = 'Y';
        return $isallowed;
    }
	
    
    function gettoken( $user, $password, $ipaddress )
    {
        $tokeni = new tokeninfo();
        
        $tokeni->userid = $user;
        $tokeni->password = $ipaddress;
        $tokeni->ipaddress = $ipaddress;
        
        $token = hash('md5',$tokeni->userid.$tokeni->password.$tokeni->ipaddress);
        
        // The token has to be calculated and stored on the database
        // It needs to be stored with userID, a sort of salt, timestamp, expiry datetime
        
        return $token;
    }
    
    
    
    function getexpirytime()
    {
        $expirytime = 60;
        return $expirytime;
        
    }
    
?>
