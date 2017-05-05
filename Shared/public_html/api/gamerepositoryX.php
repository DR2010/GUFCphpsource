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

    //  http://gungahlinunitedfc.org.au/api/gamerepository.php?indate=2016-05-09
    //  http://gungahlinunitedfc.org.au/api/gamerepository.php?indate=2016-05-09&ingameid=GAME0012
    
	$indate       = $_GET['indate']; 
	$ingameid     = $_GET['ingameid']; 
    $inhometeam   = $_GET['inhometeam'];
    $inawayteam   = $_GET['inawayteam'];
    $inagegroupid = $_GET['inagegroupid'];
    
    // Block IP addresses
    
    // if ( ! $remoteip == '1.41.26.149')
    // {
    //     echo 'access denied';
    //     exit();
    // }
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
    
    
    getGame( $gameid );

   
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
    
        // --------------------------------------------------------------
    //  Retrieve game ID given date, hometeam, awayteam and agegroup
    // --------------------------------------------------------------
    
    function GetGameIDByOther( $indate, $inhometeam, $inawayteam, $inagegroupid)
    {

        $outgame = "";

        $sqlinner = "
             SELECT 
             gameid 
             ,fkfieldid 
             ,referee 
             ,homejob 
              FROM game
             WHERE date = '".$indate."'
               AND fkhometeamid = '".$inhometeam."'
               AND fkagegroupid like '%".$inagegroupid"%'
               AND fkawayteamid = '".$inawayteam"'
            ";

        $r_queryinner = $mysqli->query($sqlinner);

        // echo $sqlinner;
        // echo "</p>";

        $gameid = "Game not found.";
        
        
        if ( $r_queryinner )
        {
            while ($rowinner = mysqli_fetch_assoc($r_queryinner))
            {

                $gameid = $rowinner['gameid'];

            }
        }

        return $gameid;
    }
    

    // -----------------------------------------------------------
    //  Retrieve list of games by time/ date
    // -----------------------------------------------------------
    function getListGamesTimeOnly( $dategame, $timeselected )
    {

    global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

    $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

    if ($timeselected  == "ALL" or $timeselected  == "" )
        $timeselected  = "";
    else
        $timeselected  = " AND game.time =  '".$timeselected."' ";

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
                AND round.date = '".$dategame."'
                ".$timeselected."
                AND game.fkgroundplaceid = 'HARRISON'

                ORDER BY game.time, seqnum, game.fkagegroupid, fieldid
                ";

        $r_queryinner = $mysqli->query($sqlinner);

        // echo $sqlinner;
        // echo "</p>";

        $gamelist = array();

        if ( $r_queryinner )
        {
            while ($rowinner = mysqli_fetch_assoc($r_queryinner))
            {

                $game = new Game();
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

            }
        }

        return $gamelist;
    }

?>

