<?
$log = '/var/log/wakeonlan/scan.xml';
if (file_exists($log))
    file_put_contents($log, '');
    echo json_encode(true);
?>
