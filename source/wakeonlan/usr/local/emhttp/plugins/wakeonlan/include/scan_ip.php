<?
    $ip = escapeshellarg($_GET['ip']);
    $cmd = "/usr/bin/nmap -sP $ip | grep 'Host is up' && echo 'on' || echo 'blink'";
    $status = trim(exec($cmd));
    echo json_encode($status);
?>
