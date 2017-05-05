<?php

// ---------------------------------------------------------
//
//                 Maintain Games
//
// ---------------------------------------------------------


// This is the official connection string
// $db_hostname = 'gungahlinunitedfc.org.au';
// $db_username = 'gufcweb_dev';
// $db_password = 'deve!oper';
// $db_database = 'gufcweb_player';

$db_hostname = 'localhost:3306';
$db_username = 'root';
$db_password = 'oculos';
$db_database = 'gufcdraws';

$mysqli = "";

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



function listGamesAgeGroup( $dategame, $timeselected )
{

    global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

    $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

    if ($timeselected  == "ALL" or $timeselected  == "")
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
				   AND game.fkgroundplaceid = 'HARRISON'
				   AND game.fkroundid = round.idround
				   AND game.fkfieldid = harrisonsfieldschema.fieldid
				   AND round.date = '".$dategame."' ".$timeselected."
				ORDER BY game.time, seqnum, game.fkagegroupid, fieldid
				";

    $r_queryinner = $mysqli->query($sqlinner);

    $todays_date = date("Y-m-d");

    // echo $sqlinner;

    if ( ! $r_queryinner )
    {
        echo 'No games found on date '.$dategame.' ';
    }
    else
    {
        $currentage = "";
        $previousage = "";

        while ($rowinner = mysqli_fetch_assoc($r_queryinner))
        {
            $currentage = $rowinner['fkagegroupid'];

            if  ( strpos($currentage, 'Girls MiniRoos') !== false )	{ $currentage = 'Girls MiniRoos';  }
            elseif ( strpos($currentage, 'U5'            ) !== false) { $currentage = 'UNDER 5';       }
            elseif ( strpos($currentage, 'U6'            ) !== false) { $currentage = 'UNDER 6';       }
            elseif ( strpos($currentage, 'U7'            ) !== false) { $currentage = 'UNDER 7';       }
            elseif ( strpos($currentage, 'U8'            ) !== false) { $currentage = 'UNDER 8';       }
            elseif ( strpos($currentage, 'U9'            ) !== false) { $currentage = 'UNDER 9';       }
            elseif ( strpos($currentage, 'U10'           ) !== false) { $currentage = 'UNDER 10';       }
            elseif ( strpos($currentage, 'U11'           ) !== false) { $currentage = 'U11, U13 and Seniors';       }
            elseif ( strpos($currentage, 'U12'           ) !== false) { $currentage = 'U12, U14';       }
            elseif ( strpos($currentage, 'U13'           ) !== false) { $currentage = 'U11, U13 and Seniors';       }
            elseif ( strpos($currentage, 'U14'           ) !== false) { $currentage = 'U12, U14';       }
            else { $currentage = 'UNDER 15,16,17,18 and Seniors'; };

            if ($currentage == "" or $currentage !== $previousage)
            {
                // only if previous is not empty
                if ( $previousage !== "" ) {
                    echo '</tbody></table><p>';
                }

                echo '<h1>'.$currentage.'</h1>';

                echo '<table class="table" align="center" border="1" >';
                echo "\n";
                echo '<th>Time</th>';
                echo '<th>Field</th>';
                echo '<th>Home</th>';
                echo '<th>Away</th>';
                echo '<th>Referee</th>';
                echo '<th>Age</th>';
                echo '<th>Date</th>';
                echo '<th>Round</th>';
                echo '<th>Home Job</th>';
                echo '<th>Ground Address</th>';
                echo "\n";
            }

            echo "\n";
            echo '<tr>';
            echo '<td>'.$rowinner['time'].'</td>';
            echo '<td>'.$rowinner['fieldid'].'</td>';
            echo '<td>'.$rowinner['fkhometeamid'].'</td>';
            echo '<td>'.$rowinner['fkawayteamid'].'</td>';
            echo '<td>'.$rowinner['referee'].'</td>';
            echo '<td>'.$rowinner['fkagegroupid'].'</td>';
            echo '<td>'.$rowinner['rounddate'].'</td>';
            echo '<td>'.$rowinner['idround'].'</td>';
            echo '<td>'.$rowinner['homejob'].'</td>';
            echo '<td>'.$rowinner['gpaddress'].'</td>';
            echo '</tr>';

            $previousage = $currentage;

        }

        echo "\n";
        echo '</table>';
        echo '<p/>';

    }
}

function GetGameCountByReferee( $dategame )
{

    global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

    $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

    $criteria = ' and groundplace.idgroundplace = "HARRISON" ';

    $sqlinner = "
                SELECT
                     game.referee   referee,
                     count(*)       refcount
				 FROM game, round, groundplace, harrisonsfieldschema
				WHERE
				       game.fkgroundplaceid = groundplace.idgroundplace
				   AND game.fkroundid = round.idround
				   AND game.fkfieldid = harrisonsfieldschema.fieldid
				   AND round.date = '".$dategame."'
                   AND groundplace.idgroundplace = 'HARRISON'
                   AND (referee != '' OR referee != null)
				GROUP BY game.referee
				";

    $r_queryinner = $mysqli->query($sqlinner);

    echo '</tbody></table><p>';
    echo '<table class="table" align="center" border="1" >';
    echo "\n";
    echo '<th>Referee</th>';
    echo '<th>Number of Games</th>';
    echo "\n";

    if ( $r_queryinner )
    {
        while ($rowinner = mysqli_fetch_assoc($r_queryinner))
        {
            $referee = new Referee();
            $referee->gamecount = $rowinner['refcount'];
            $referee->name = $rowinner['referee'];

            echo '<tr>';
            echo '<td>'.$referee->name.'</td>';
            echo '<td>'.$referee->gamecount.'</td>';
            echo '</tr>';
        }
        echo "\n";
        echo '</table>';
        echo '<p/>';

    }
}

function CountGamesByRefereeSample( $dategame )
{

    global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

    $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

    $sqlinner = "
                SELECT
                     game.referee   referee,
                     count(*)       refcount
				 FROM game, round, groundplace, harrisonsfieldschema
				WHERE
				       game.fkgroundplaceid = groundplace.idgroundplace
				   AND game.fkroundid = round.idround
				   AND game.fkfieldid = harrisonsfieldschema.fieldid
				   AND round.date = '".$dategame."'
                   AND groundplace.idgroundplace = 'HARRISON'
                   AND (referee != '' OR referee != null)
				GROUP BY game.referee
				";

    $r_queryinner = $mysqli->query($sqlinner);

    echo '</tbody></table><p>';
    echo '<table class="table" align="center" border="1" >';
    echo "\n";
    echo '<th>Referee</th>';
    echo '<th>Number of Games</th>';
    echo "\n";

    $refereelist = array();

    if ( $r_queryinner )
    {
        while ($rowinner = mysqli_fetch_assoc($r_queryinner))
        {
            $referee = new Referee();
            $referee->gamecount = $rowinner['refcount'];
            $referee->name = $rowinner['referee'];
            $refereelist[] = $referee;
        }

        foreach( $refereelist as $ref)
        {
            echo '<tr>';
            echo '<td>'.$ref->name.'</td>';
            echo '<td>'.$ref->gamecount.'</td>';
            echo '</tr>';

        }
        echo "\n";
        echo '</table>';
        echo '<p/>';
    }
}

function CountGamesByReferee( $dategame )
{

    global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

    $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

    $sqlinner = "
                SELECT
                     game.referee   referee,
                     count(*)       refcount
				 FROM game, round, groundplace, harrisonsfieldschema
				WHERE
				       game.fkgroundplaceid = groundplace.idgroundplace
				   AND game.fkroundid = round.idround
				   AND game.fkfieldid = harrisonsfieldschema.fieldid
				   AND round.date = '".$dategame."'
                   AND groundplace.idgroundplace = 'HARRISON'
                   AND (referee != '' OR referee != null)
				GROUP BY game.referee
				";

    $r_queryinner = $mysqli->query($sqlinner);

    $refereelist = array();

    if ( $r_queryinner )
    {
        while ($rowinner = mysqli_fetch_assoc($r_queryinner))
        {
            $referee = new Referee();
            $referee->name = $rowinner['referee'];
            $referee->gamecount = $rowinner['refcount'];
            $refereelist[] = $referee;
        }

    }

    $mysqli->close();
    return $refereelist;

}

function GetRefereeAllocationsToday( $dategame, $refereeid )
{
    global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

    $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

    $sqlinner = "
            SELECT
                       gameid,
                       time,
                       referee,
                       refereeAUX1,
                       refereeAUX2
				 FROM game, round
				WHERE
				       game.fkroundid = round.idround
				   AND round.date = '".$dategame."'
               AND ( referee = '".$refereeid."' OR refereeAUX1 = '".$refereeid."'  OR refereeAUX2 = '".$refereeid."'  )
               				";

    $r_queryinner = $mysqli->query($sqlinner);

    $gamelist = array();

    if ( $r_queryinner )
    {
        while ($rowinner = mysqli_fetch_assoc($r_queryinner))
        {
            $game = new Game();
            $game->gameid = $rowinner['gameid'];
            $game->referee = $rowinner['referee'];
            $game->refereeAUX1 = $rowinner['refereeAUX1'];
            $game->refereeAUX2 = $rowinner['refereeAUX2'];
            $game->time = $rowinner['time'];
            $gamelist[] = $game;
        }
    }

    $mysqli->close();
    return $gamelist;
}

// ---------------------------------------------------------
//   Return list of games for specific time on date
// ---------------------------------------------------------

function getListGamesTimeAll( $dategame, $timeselected )
{
    getListGamesTime( $dategame, $timeselected, "No");

}

function getListGamesTime( $dategame, $timeselected, $onlygameref )
{

    global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

    // If $showgamesforref is true, we are only going to show the games that require referee.
    //
    // and game.time in ('09:00 AM','10:00 AM')

    $timestocheck = "";
    if ($onlygameref == 'OnlyGameRef') {

        $listOfTimes = getListOfTimes("NOTALL", "Y"); // only games needing referee

        foreach ($listOfTimes as $time) {
            if ( $timestocheck == "")
                $timestocheck = " AND game.time IN ('" . $time . "'";
            else
                $timestocheck = $timestocheck . ",'" . $time . "'";
        }
        $timestocheck = $timestocheck . ") ";
    }


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
				   ".$timestocheck."
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

// -----------------------------------------------------------
// This function will return only games needing referees
// -----------------------------------------------------------
function getListGamesForReferee( $dategame, $timeselected )
{

    global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

    $timestocheck = "";

    $listOfTimes = getListOfTimes("NOTALL", "Y"); // only games needing referee

    foreach ($listOfTimes as $time) {
        if ( $timestocheck == "")
            $timestocheck = " AND game.time IN ('" . $time . "'";
        else
            $timestocheck = $timestocheck . ",'" . $time . "'";
    }
    $timestocheck = $timestocheck . ") ";


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
				   ".$timestocheck."
				   AND game.fkgroundplaceid = 'HARRISON'
				   AND game.fkfieldid != 'BYE'
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



function showGamesMatrix( $dategame )
{
    echo '</tbody></table><p>';
    echo '<table class="table" align="center" border="1" >';
    echo "\n";

    echo '<th>Seq Num</th>';
    $listOfTimes = getListOfTimes("NOTALL", "Y");
    foreach( $listOfTimes as $time ) {
        echo '<th>' . $time . '</th>';
    }

    // List all games per time

    $max = 0;
    $games0850 = getListGamesTimeOnly( $dategame, "08:50 AM");
    $max = count ($games0850);
//    $games0900 = getListGamesTime( $dategame, "09:00 AM");
//    if ( count ($games0900) > $max ) $max = count ($games0900);
    $games0940 = getListGamesTimeOnly( $dategame, "09:40 AM");
    if ( count ($games0940) > $max ) $max = count ($games0940);
//    $games1000 = getListGamesTime( $dategame, "10:00 AM");
//    if ( count ($games1000) > $max ) $max = count ($games1000);
    $games1035 = getListGamesTimeOnly( $dategame, "10:35 AM");
    if ( count ($games1035) > $max ) $max = count ($games1035);
    $games1130 = getListGamesTimeOnly( $dategame, "11:30 AM");
    if ( count ($games1130) > $max ) $max = count ($games1130);
    $games1230 = getListGamesTimeOnly( $dategame, "12:30 PM");
    if ( count ($games1230) > $max ) $max = count ($games1230);
    $games0145 = getListGamesTimeOnly( $dategame, "1:45 PM");
    if ( count ($games0145) > $max ) $max = count ($games0145);
    $games0310 = getListGamesTimeOnly( $dategame, "3:10 PM");
    if ( count ($games0310) > $max ) $max = count ($games0310);

    $cond = true;
    $i = 0;
    while ( $i < $max )
    {
        $col0850 = "";
//        $col0900 = "";
        $col0940 = "";
//        $col1000 = "";
        $col1035 = "";
        $col1130 = "";
        $col1230 = "";
        $col0145 = "";
        $col0310 = "";

        if ( $i < count( $games0850 ) ) $col0850 = $games0850[$i]->fieldid ." - ". $games0850[$i]->referee;
//        if ( $i < count( $games0900 ) )  $col0900 = $games0900[$i]->fieldid ." - ". $games0900[$i]->referee;
        if ( $i < count( $games0940 ) )  $col0940 = $games0940[$i]->fieldid ." - ". $games0940[$i]->referee;
//        if ( $i < count( $games1000 ) )  $col1000 = $games1000[$i]->fieldid ." - ". $games1000[$i]->referee;
        if ( $i < count( $games1035 ) )  $col1035 = $games1035[$i]->fieldid ." - ". $games1035[$i]->referee;
        if ( $i < count( $games1130 ) )  $col1130 = $games1130[$i]->fieldid ." - ". $games1130[$i]->referee;
        if ( $i < count( $games1230 ) )  $col1230 = $games1230[$i]->fieldid ." - ". $games1230[$i]->referee;
        if ( $i < count( $games0145 ) )  $col0145 = $games0145[$i]->fieldid ." - ". $games0145[$i]->referee;
        if ( $i < count( $games0310 ) )  $col0310 = $games0310[$i]->fieldid ." - ". $games0310[$i]->referee;

        echo '<tr>';
        echo '<td>' . $i . '</td>';
        echo '<td>' . $col0850 . '</td>';
//        echo '<td>' . $col0900 . '</td>';
        echo '<td>' . $col0940 . '</td>';
//        echo '<td>' . $col1000 . '</td>';
        echo '<td>' . $col1035 . '</td>';
        echo '<td>' . $col1130 . '</td>';
        echo '<td>' . $col1230 . '</td>';
        echo '<td>' . $col0145 . '</td>';
        echo '<td>' . $col0310 . '</td>';
        echo '</tr>';

        $i++;
    }

    echo "\n";

    echo '</table>';
    echo '<p/>';
    echo '<b/>';
    echo "\n";
}


function getPossibleRefereesForGame( $gameid, $gametime )
{
    //
    //  Retrieve all possible referees for a given game
    //

    global $db_hostname,$db_username,$db_password, $db_database, $mysqli;

    $mysqli = new mysqli($db_hostname,$db_username,$db_password, $db_database);

    $sqlinner = "
                select
                UID,
                FKRefereeUID,
                TimeIDS,
                Type
                from   refereeavailability
                where  timeIDS like '%".$gametime."%'
                order by FKRefereeUID
      			";

    $r_queryinner = $mysqli->query($sqlinner);

    $refereelist = array();

    if ( $r_queryinner )
    {
        while ($rowinner = mysqli_fetch_assoc($r_queryinner))
        {
            $referee = new Referee();
            $referee->idreferee = $rowinner['FKRefereeUID'];
            $refereelist[] = $referee;
        }
    }

    $mysqli->close();
    return $refereelist;

}


// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
//             Main function to allocate referees to Games
// ------------------------------------------------------------------------
// ------------------------------------------------------------------------
function allocateRefereesToGames( $dategame )
{

    //    var $refereeid;
    //    var $availabletimes;
    //    var $gamescounttoday;
    //    var $remainingavailability;
    //    var $allocatedtimes;


    // find referees availability today (ref name, availability, game count today)

    $refereelist = listReferees($dategame);

    $refereesmatrix = array();


    foreach( $refereelist as $ref)
    {


        $refereetimes = getAvailabilityByReferee( $ref->idreferee );

        $referee = new RefereeListTemp();
        $referee->idreferee = $ref->idreferee;
        $referee->availabletimes = $refereetimes->availabilitytime;
        $referee->allocatedtimes = '';
        $referee->gamescounttoday = 0;
        $referee->remainingavailability = '';

        $refereesmatrix[] = $referee;

    }


//    echo '</tbody></table><p>';
//    echo '<table class="table" align="center" border="1" >';
//    echo "\n";
//    echo '<th>Referee ID</th>';
//    echo '<th>availabletimes</th>';
//    echo "\n";
//
//    foreach ($refereesmatrix as $ref)
//    {
//
//        echo '<tr>';
//        echo '<td>'.$ref->idreferee.'</td>';
//        echo '<td>'.$ref->availabletimes.'</td>';
//        echo '</tr>';
//    }
//
//    echo "\n";
//    echo '</table>';
//    echo '<p/>';

    // for each game, find all possible referees (array)

    // load all games in memory
    $gamesall = getListGamesForReferee( $dategame, "ALL");

    echo '</tbody></table><p>';
    echo '<table class="table-responsive" align="center" border="1" >';
    echo "\n";
    echo '<th>gameid</th>';
    echo '<th>time</th>';
    echo '<th>Field</th>';
    echo '<th>Home Team</th>';
    echo '<th>Away Team</th>';
    echo '<th>Current Referee</th>';
    echo '<th>Suggested Referee</th>';
    echo "\n";

    foreach( $gamesall as $game )
    {

        $availableref = allocateAvailableReferee( $refereesmatrix, $game->time );

        echo '<tr>';
        echo '<td>'.$game->gameid.'</td>';
        echo '<td>'.$game->time.'</td>';
        echo '<td>'.$game->fieldid.'</td>';
        echo '<td>'.$game->fkhometeamid.'</td>';
        echo '<td>'.$game->fkawayteamid.'</td>';
        echo '<td>'.$game->referee.'</td>';
        echo '<td>'.$availableref.'</td>';
        echo '</tr>';

        $game->referee = $availableref;

    }
    echo "\n";
    echo '</table>';
    echo '<p/>';

//    showUpdatedGames($gamesall);

    return $gamesall;

}


// ---------------------------------------------------------------------
//                  Allocate Referee for a Game
// ---------------------------------------------------------------------
function allocateAvailableReferee( $refereesmatrix, $gametime )
{
    //
    // find referees available for the time
    //

    $refereesavailX = array();

    foreach ($refereesmatrix as $ref)
    {
        $checkagainsttime = $ref->availabletimes;
        $allocatedtimes = $ref->allocatedtimes;
        $avail = 'no';

        if (strpos($checkagainsttime, $gametime) !== false)
            $avail = 'yes';
        else
            continue;

        // if referee has already been allocated
        if (strpos($allocatedtimes, $gametime) !== false) continue;

        // filter list only
        $refereesavailX[] = $ref; // only the current record
    }

    // If there are no referees available, don't bother.
    //
    if ( count($refereesavailX) == 0 )
    {
        return "";
    }

    //
    // look for the referee with less allocations
    //
    $refwithlessallocations = '';
    $reflessallocations = 0;

    $cont = 'Y';
    while ($cont= 'Y') {
        foreach ($refereesavailX as $ref) {

            if ($ref->gamescounttoday <= $reflessallocations) {

                $refwithlessallocations = $ref->idreferee;
                $reflessallocations = $ref->gamescounttoday;
            }
        }
        if ($refwithlessallocations == "")
            $reflessallocations++;
        else
            break;

        if ($reflessallocations >= 10)
            break;
    }


    foreach ($refereesavailX as $ref) {

        if ($refwithlessallocations == $ref->idreferee ) {
            $ref->gamescounttoday = $ref->gamescounttoday + 1;
            $ref->allocatedtimes = $ref->allocatedtimes.';'.$gametime;

            return $ref->idreferee;
        }
    }


    return count($refereesavailX);
}

// ---------------------------------------------------------------------
//       Show Updated Games with Referee allocated
// ---------------------------------------------------------------------
function showUpdatedGames( $gamelist )
{
    echo '</tbody></table><p>';
    echo '<table class="table" align="center" border="1" >';
    echo "\n";
    echo '<th>Game ID</th>';
    echo '<th>Referee</th>';
    echo '<th>Field ID</th>';
    echo "\n";

    foreach( $gamelist as $game)
    {
        echo '<tr>';
        echo '<td>'.$game->gameid.'</td>';
        echo '<td>'.$game->referee.'</td>';
        echo '<td>'.$game->fieldid.'</td>';
        echo '</tr>';

    }
    echo "\n";
    echo '</table>';
    echo '<p/>';

}

?>
