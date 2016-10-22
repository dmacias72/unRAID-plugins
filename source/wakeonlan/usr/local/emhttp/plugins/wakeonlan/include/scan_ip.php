<?
    $ip = $_GET['ip'];
    $cmd = "/usr/bin/nmap -sP $ip | grep 'Host is up' && echo 'on' || echo 'blink'";
    $status = exec($cmd);
    echo json_encode($status);
?>
