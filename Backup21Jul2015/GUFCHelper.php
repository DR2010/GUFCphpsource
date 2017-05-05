<?php
/**
 * Created by PhpStorm.
 * User: Daniel
 * Date: 5/07/2015
 * Time: 8:39 AM
 */

function getListOfTimes( $withall, $onlyneedref  )
{

    // $onlyneedref - if value = Y will only show times needing referees

    $times = array();
    $times[] = "08:50 AM";
    if ($onlyneedref != 'Y') $times[] = "09:00 AM";
    $times[] = "09:40 AM";
    if ($onlyneedref != 'Y') $times[] = "10:00 AM";
    $times[] = "10:35 AM";
    $times[] = "11:30 AM";
    $times[] = "12:30 PM";
    $times[] = "1:45 PM";
    $times[] = "3:10 PM";

    if ($withall == "ALL")
        $times[] = "ALL";

    return $times;
}

?>