<?php

function pingDomain($domain){
    $starttime = microtime(true);
    $file      = fsockopen ($domain,465, $errno, $errstr, 10);
    $stoptime  = microtime(true);
    $status    = 0;

    if (!$file) $status = -1;  // Site is down
    else {
        fclose($file);
        $status = ($stoptime - $starttime) * 1000;
        $status = floor($status);
    }
    return $status;
}

$ip_address = "148.251.193.97";

if(pingDomain($ip_address)!=-1)
    echo "server is up";
else
    echo "server is down";

// have to discuss mangal further

?>