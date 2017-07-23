<?
    $ip = escapeshellarg($_GET['ip']);
    $cmd = "/bin/ping -b -c 1 -i 1 -W 1 $ip | grep 'received' | awk -F',' '{ print $2}' | grep '1 received' && echo 'on' || echo 'blink'";
    $status = trim(exec($cmd));
    echo json_encode($status);
?>
