<?
$log = '/boot/config/plugins/ipmi/archived_events.log';
$event = intval($_GET['event']);

if(!$event){
    file_put_contents($log, '');
}
?>