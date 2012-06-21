<?php

//include "Ssh2_crontab_manager.php";
//
//try {
//    $crontab = new Ssh2_crontab_manager('conradv.sytes.net', '22', 'zwave', 'psx1242');
//    //$crontab->append_cronjob('39 16 * * * curl "http://conradv.sytes.net/zwave/server.php?command=control&type=binary&node=2&level=255"');
//
//    $job = '47 17 * * * curl "http://conradv.sytes.net/zwave/server.php?command=control&type=binary&node=2&level=255"';
//
//    $job = str_replace("*", "\*", $job);
//    $job = str_replace("/", "\/", $job);
//    $job = str_replace("?", "\?", $job);
//    $job = "/" . $job . "/";
//
//    //echo $job;
//    $crontab->remove_cronjob($job);
//} catch (Exception $exc) {
//    echo $exc->getTraceAsString();
//}
 
function parseCommand($strCommand){
    //get time/date details
    $dateTime = substr($strCommand, 0, strpos($strCommand, "curl")-1);
    $dateTime = explode(" ", $dateTime);
    
    $details["min"] = $dateTime[0];
    $details["hr"] = $dateTime[1];
    $details["day"] = $dateTime[2];
    $details["month"] = $dateTime[3];
    $details["dayofweek"] = $dateTime[4];
    
    //get node details
    $strCommand = substr($strCommand, strpos($strCommand, ".php?")+5);
    $tmp = explode("&", $strCommand);
    foreach($tmp as $attribute){
        $tmp = explode("=",$attribute);
        $details[$tmp[0]] = $tmp[1];        
    }
    return $details;
}

print_r(parseCommand('47 17 * * * curl "http://conradv.sytes.net/zwave/server.php?command=control&type=binary&node=2&level=255"'));

?>