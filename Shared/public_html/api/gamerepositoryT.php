<?php
	/*
	Game Repository
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
	
    
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $remoteipforwarded = $_SERVER['HTTP_X_FORWARDED_FOR']; // easily spoofed
	$indate=$_GET['indate']; 
	$gameid=$_GET['ingameid']; 
    
    // Block IP addresses
    
    // if ( ! $remoteip == '1.41.26.149')
    // {
    //     echo 'access denied';
    //     exit();
    // }
    
    // http://gungahlinunitedfc.org.au/api/gamerepositoryT.php?ingameid=GIRLS009
    
    
    getGame( $gameid ); 
        
     class Game
    {   
        var $gameid;
        var $time;
        var $referee;
        var $refereeAUX1;
        var $refereeAUX2;
        var $fkhometeamid;
        var $fkawayteamid;
        var $rounddate;
        var $fieldid;
        var $fkagegroupid;
        var $homejob;

    }

    // -------------------------------------------------------------------------
    //   Retrieve game details
    // -------------------------------------------------------------------------
    function getGame( $gameid )
    {

        global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

        $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

        $sqlinner = "
                        SELECT
                        game.gameid
                        ,game.fkhometeamid      fkhometeamid
                        ,game.fkawayteamid      fkawayteamid
                        ,game.fkagegroupid      fkagegroupid
                        ,game.fkroundid         fkroundid
                        ,game.fkgroundplaceid   fkgroundplaceid
                        ,game.referee   referee
                        ,game.homejob   homejob
                        ,game.time      time
                        ,game.seqnum      seqnum
                        ,round.idround          idround
                        ,round.date             rounddate
                        ,groundplace.navigate   gpnavigate
                        ,groundplace.address 	gpaddress
                        ,harrisonsfieldschema.fieldid fieldid
                        ,harrisonsfieldschema.imagelocation locationinfield
                    FROM game, round, groundplace, harrisonsfieldschema
                    WHERE
                        game.fkgroundplaceid = groundplace.idgroundplace
                    AND game.fkroundid = round.idround
                    AND game.fkfieldid = harrisonsfieldschema.fieldid
                    AND game.gameid = '".$gameid."'
                    ";
                    
        $r_queryinner = $mysqli->query($sqlinner);

        $gamelist = array();
        
        $game = new Game();
        
        if ( $r_queryinner )
        {
            while ($rowinner = mysqli_fetch_assoc($r_queryinner))
            {


                $game->gameid = $rowinner['gameid'];
                $game->time = $rowinner['time'];
                $game->fieldid = $rowinner['fieldid'];
                $game->referee = $rowinner['referee'];
                $game->refereeAUX1 = $rowinner['refereeAUX1'];
                $game->refereeAUX2 = $rowinner['refereeAUX2'];
                $game->fkhometeamid = $rowinner['fkhometeamid'];
                $game->fkawayteamid = $rowinner['fkawayteamid'];
                $game->fkawayteamid = $rowinner['fkagegroupid'];
                $game->rounddate = $rowinner['rounddate'];
                $gamelist[] = $game;
                
                break;

            }
        }

        echo (json_encode( $game ));
    }



   
?>

