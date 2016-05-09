<?
$log = '/boot/config/plugins/ipmi/archived_events.log';
$event = $_GET["event"];

if(!$event){
    file_put_contents($log, '');
}
?>