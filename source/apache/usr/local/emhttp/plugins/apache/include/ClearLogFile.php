<?
$base = '/var/log/httpd/';
$log  = realpath($_POST['log']);

//check that log file is in the base path and exists
if(!strpos($log, $base) && file_exists($log)){
    file_put_contents($log, '');
}
?>