<?
    $log = '/var/log/wakeonlan/scan.xml';
    if (file_exists($log))
        file_put_contents($log, '');
    $ip = $_GET['ip'];
    $net = substr_replace($ip ,'',-1).'0/24';
    $cmd = "/usr/bin/nmap -sn -oX $log --exclude $ip $net";
    exec($cmd);
    echo json_encode(true);
?>