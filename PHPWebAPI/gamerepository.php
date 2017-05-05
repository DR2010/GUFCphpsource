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
	
    $requestmethod = $_SERVER['REQUEST_METHOD'];
    $remoteip = $_SERVER['REMOTE_ADDR'];
    $remoteipforwarded = $_SERVER['HTTP_X_FORWARDED_FOR']; // easily spoofed
	
    $action=$_GET['action'];
    $indate=$_GET['indate']; 
	$gameid=$_GET['ingameid']; 
	$referee=$_GET['referee']; 
	$homejob=$_GET['homejob']; 
	$fkfieldid=$_GET['fkfieldid'];
    
    $inhometeam   = $_GET['inhometeam'];
    $inawayteam   = $_GET['inawayteam'];
    $inagegroupid = $_GET['inagegroupid'];

    $matchid=$_GET['matchid']; 
    $fkroundid=$_GET['fkroundid']; 
    $fkgroundplaceid=$_GET['fkgroundplaceid']; 
    $homescore=$_GET['homescore']; 
    $awayscore=$_GET['awayscore']; 
    $week=$_GET['week']; 
    $date=$_GET['date']; 
    $time=$_GET['time']; 
    $seqnum=$_GET['seqnum']; 
    $rounddate=$_GET['rounddate']; 
    $time=$_GET['time']; 


    
    // Block IP addresses
    
    // if ( ! $remoteip == '1.41.26.149')
    // {
    //     echo 'access denied';
    //     exit();
    // }
    
    // http://gungahlinunitedfc.org.au/api/gamerepository.php?ingameid=GIRLS012
    // http://gungahlinunitedfc.org.au/api/gamerepository.php?indate=2015-05-16&inhometeam=Pink All Stars&fkawayteamid=Girls MiniRoos&inagegroupid=U5
    // http://gungahlinunitedfc.org.au/api/gamerepository.php?action=getgameid&indate=2015-05-16&inhometeam=Pink All Stars&fkawayteamid=Girls MiniRoos&inagegroupid=U5
    // http://gungahlinunitedfc.org.au/api/gamerepository.php?action=getgamebyid&ingameid=GIRLS012
    // http://gungahlinunitedfc.org.au/api/gamerepository.php?indate=2015-05-16&inhometeam=Pink All Stars&fkawayteamid=Girls MiniRoos&inagegroupid=U5
    
    
    
    switch ($action) {
    case 'getgamebyid':
        getGame( $gameid ); 
        break;
    case 'getgameid':
        GetGameIDByOther( $indate, $inhometeam, $inawayteam, $inagegroupid );
        break;
     case 'updategame':       
        updategame( $gameid, $referee, $homejob, $fkfieldid, $seqnum, $time, $fkgroundplaceid );
        break;
     case 'listgamesdate':       
        listGamesonDate( $indate );
        break;
     case 'getnextgameid':       
        getnextgameid();
        break;
     case 'addgame':
         insertgame(
                    $gameid,
                    $matchid,
                    $referee,
                    $homejob,
                    $fkfieldid,
                    $inhometeam,
                    $inawayteam,
                    $inagegroupid,
                    $fkroundid,
                    $fkgroundplaceid,
                    $homescore,
                    $awayscore,
                    $week,
                    $date,
                    $time,
                    $seqnum,
                    $rounddate
                    );
          break;
     }
    
    // if (  $gameid != "") {
    //     getGame( $gameid ); 
    // }
    // else {
    //     GetGameIDByOther( $indate, $inhometeam, $inawayteam, $inagegroupid );
    // }
           
        
    class Game
    {   
        var $gameid;
        var $matchid;
        var $referee;
        var $homejob;
        var $fkfieldid;
        var $fkhometeamid;
        var $fkawayteamid;
        var $fkagegroupid;
        var $fkroundid;
        var $fkgroundplaceid;
        var $homescore;
        var $awayscore;
        var $week;
        var $date;
        var $time;
        var $seqnum;
        var $rounddate;

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
                        ,harrisonsfieldschema.fieldid fkfieldid
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
        
        
        if ( $r_queryinner )
        {
            while ($rowinner = mysqli_fetch_assoc($r_queryinner))
            {

                $game = new Game();

                $game->gameid = $rowinner['gameid'];
                $game->date = $rowinner['rounddate'];
                $game->time = $rowinner['time'];
                $game->fkfieldid = $rowinner['fkfieldid'];
                $game->referee = $rowinner['referee'];
                $game->refereeAUX1 = $rowinner['refereeAUX1'];
                $game->refereeAUX2 = $rowinner['refereeAUX2'];
                $game->fkhometeamid = strtoupper($rowinner['fkhometeamid']);
                $game->fkawayteamid = strtoupper($rowinner['fkawayteamid']);
                $game->fkagegroupid = strtoupper($rowinner['fkagegroupid']);
                $game->rounddate = $rowinner['rounddate'];
                $game->homejob = $rowinner['homejob'];
                
                $game->matchid = $rowinner['matchid'];
                $game->fkroundid = $rowinner['fkroundid'];
                $game->fkgroundplaceid = $rowinner['fkgroundplaceid'];
                $game->seqnum = $rowinner['seqnum'];
                $game->week = $rowinner['fkroundid'];
                
                
                $gamelist[] = $game;
                
                break;

            }
        }

        // echo $sqlinner;

        echo (json_encode( $game ));
    }

    // --------------------------------------------------------------
    //  Retrieve game ID given date, hometeam, awayteam and agegroup
    // --------------------------------------------------------------
    
    // http://gungahlinunitedfc.org.au/api/gamerepository.php?action=getgameid&indate=2015-05-16&inhometeam=Pink%20All%20Stars&inawayteam=Swifts&inagegroupid=Girls%20MiniRoos
    
    function GetGameIDByOther( $indate, $inhometeam, $inawayteam, $inagegroupid)
    {
        global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

        $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);
        
        $outgame = "";
        
        $sqlinner = " SELECT 
                             gameid 
                            ,fkfieldid 
                            ,referee 
                            ,homejob 
                            FROM game
              WHERE date = '".$indate."'
                AND fkhometeamid = '".$inhometeam."'
                AND fkagegroupid like '%".$inagegroupid."%'
                AND fkawayteamid = '".$inawayteam."'";
       
        $r_queryinner = $mysqli->query($sqlinner);
        
        $gameid = "Game not found.";
       
        if ( $r_queryinner )
        {
            while ($rowinner = mysqli_fetch_assoc($r_queryinner))
            {

                $gameid = $rowinner['gameid'];

            }
        }

        // echo $sqlinner;
        
        echo (json_encode( $gameid ));
        
    }


    function updategame( $ingameid, $referee, $homejob, $fkfieldid, $seqnum, $time, $fkgroundplaceid )
    {
        // echo 'here now';
        global $db_hostname,$db_username,$db_password, $db_database, $mysqli;  

        $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);


        if ( $time == "" || $ingameid == "" ) 
        {
            return;
        }

        // echo 'sql..';
        // echo $sql;
        // echo '</b>'; 

        // http://gungahlinunitedfc.org.au/api/gamerepository.php?action=updategame&ingameid=SYS00000314&referee=&homejob=SETUP %26 PACKUP&seqnum=360&time=1:45 PM&fkfieldid=1&fkgroundplaceid=PALMERSTON
        
        $sql = "UPDATE game SET referee='".$referee.
                "', homejob='".$homejob.
                "', fkfieldid='".$fkfieldid.
                "', seqnum='".$seqnum.
                "', time='".$time.
                "', fkgroundplaceid='".$fkgroundplaceid.
                "' WHERE gameid ='".$ingameid."'";
        // echo $sql;
    
        if ($mysqli->query($sql) === TRUE) {
//		    echo "Record updated successfully";
        } else {
//		    echo "Error updating record: " . $mysqli->error;
        }
            
            // echo $sql;
            
    //	echo '</p>';
    //	echo $gameid;
    //	echo '</p>';
    //	echo $refereeid;

        $mysqli->close();
    }

    //
    //
    //
    function insertgame(
                        $gameid,
                        $matchid,
                        $referee,
                        $homejob,
                        $fkfieldid,
                        $fkhometeamid,
                        $fkawayteamid,
                        $fkagegroupid,
                        $fkroundid,
                        $fkgroundplaceid,
                        $homescore,
                        $awayscore,
                        $week,
                        $date,
                        $time,
                        $seqnum,
                        $rounddate
                        )
    {
        
        
        // echo 'here now';
        global $db_hostname,$db_username,$db_password, $db_database, $mysqli;  

        $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

        $gameid = getnextgameid();

        $game = new Game();
        $game->gameid = $gameid;
        $game->matchid = $matchid;
        $game->referee = $referee;
        $game->homejob = $homejob;
        $game->fkfieldid = $fkfieldid;
        $game->fkhometeamid = $fkhometeamid;
        $game->fkawayteamid = $fkawayteamid;
        $game->fkagegroupid = $fkagegroupid;
        $game->fkroundid = $fkroundid;
        $game->fkgroundplaceid = $fkgroundplaceid;
        $game->homescore = $homescore;
        $game->awayscore = $awayscore;
        $game->week = $week;
        $game->date = $date;
        $game->seqnum = $seqnum;
        $game->time = $time;

         $sql =  "INSERT INTO game 
            (
              gameid   
            , matchid  
            , date   
            , time 
            , referee   
            , fkfieldid 
            , fkhometeamid 
            , fkawayteamid 
            , fkagegroupid 
            , homejob 
            , fkroundid 
            , fkgroundplaceid 
            , homescore 
            , awayscore 
            , week 
            , seqnum 
            ) 
                 VALUES ('" 
               .$gameid. 
            "','".$game->matchid. 
            "','".$date.
            "','".$time.
            "','".$referee.
            "','".$fkfieldid.
            "','".$fkhometeamid.
            "','".$fkawayteamid.
            "','".$fkagegroupid.
            "','".$homejob.
            "','".$fkroundid.
            "','".$fkgroundplaceid.
            "','".$homescore.
            "','".$awayscore.
            "','".$week.
            "','".$seqnum.
            "')";

        if ( 
                $gameid== "" || 
            $fkhometeamid == "" ||
            $fkawayteamid == "" ||
            $time == "" ||
            $date == "" ||
            $fkagegroupid == ""
        
        )
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
                echo "Record updated successfully";
            } else {
                echo "Error updating record: " . $mysqli->error;
            }
        }
    //	echo '</p>';
    //	echo $gameid;
    //	echo '</p>';
    //	echo $refereeid;

        $mysqli->close();
    }


    // -------------------------------------------------------------------------
    //   Retrieve game details
    // -------------------------------------------------------------------------
    function getnextgameid( )
    {

        global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

        $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

        $sqlinner = "SELECT MAX(gameid) LASTUID FROM game where gameid like 'SYS%'";
                    
        $r_queryinner = $mysqli->query($sqlinner);

        $lastuid = "SYS00000000";
        
        if ( $r_queryinner )
        {
            while ($rowinner = mysqli_fetch_assoc($r_queryinner))
            {

                $lastuid = $rowinner['LASTUID'];
                break;

            }
        }

        
        // get substring last 8
        $lastnum = substr($lastuid, 3, 8);
        
        // transform to number 
        $lastnum = $lastnum + 1;
        // add one 
        // concatenate
       
        $padding = str_pad($lastnum, 8, "0", STR_PAD_LEFT);
       
        $lastuid = 'SYS'.$padding;
        
        
        // echo $lastuid;
        
        return $lastuid;

    }




    // -------------------------------------------------------------------------
    //   Retrieve all games on a date
    // -------------------------------------------------------------------------
    function listGamesonDate( $indate )
    {

        // echo 'asdjasdjsakdsad skajhd ksajdkas';
        
        global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

        $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

        $sqlinner = "
                        SELECT
                        game.gameid
                        ,game.matchid
                        ,game.fkhometeamid      fkhometeamid
                        ,game.fkawayteamid      fkawayteamid
                        ,game.fkagegroupid      fkagegroupid
                        ,game.fkroundid         fkroundid
                        ,game.fkgroundplaceid   fkgroundplaceid
                        ,game.referee           referee
                        ,game.homejob           homejob
                        ,game.time              time
                        ,game.seqnum            seqnum
                        ,game.fkfieldid         fkfieldid
                        ,round.idround          idround
                        ,game.date             rounddate
                    FROM game, round
                    WHERE
                        game.fkroundid = round.idround
                    AND round.date = '".$indate."'
                    ";
                    
        $r_queryinner = $mysqli->query($sqlinner);

        $gamelist = array();
        

        
        if ( $r_queryinner )
        {
            while ($rowinner = mysqli_fetch_assoc($r_queryinner))
            {

                $game = new Game();
                $game->gameid = $rowinner['gameid'];
                $game->matchid = $rowinner['matchid'];
                $game->date = $rowinner['rounddate'];
                $game->time = $rowinner['time'];
                $game->fkfieldid = $rowinner['fkfieldid'];
                $game->fkroundid = $rowinner['fkroundid'];
                $game->fkgroundplaceid = $rowinner['fkgroundplaceid'];
                $game->referee = $rowinner['referee'];
                $game->refereeAUX1 = $rowinner['refereeAUX1'];
                $game->refereeAUX2 = $rowinner['refereeAUX2'];
                $game->fkhometeamid = strtoupper(trim($rowinner['fkhometeamid']));
                $game->fkawayteamid = strtoupper(trim($rowinner['fkawayteamid']));
                $game->fkagegroupid = strtoupper($rowinner['fkagegroupid']);
                $game->rounddate = $rowinner['rounddate'];
                $game->homejob = $rowinner['homejob'];
                $game->seqnum = $rowinner['seqnum'];
                $game->week = $rowinner['fkroundid'];
                $gamelist[] = $game;

            }
        }

        // echo $sqlinner;

        echo (json_encode( $gamelist ));
    }


   
?>

