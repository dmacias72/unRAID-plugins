<?
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_options.php';

$cmd     = '/usr/sbin/ipmi-sel --comma-separated-output --output-event-state --no-header-output --interpret-oem-data ';
$log     = '/boot/config/plugins/ipmi/archived_events.log';
$event   = htmlspecialchars($_GET['event']);
$archive = intval($_GET['archive']);

/* network options */
if($netsvc === 'enable') {
    if($event){
        $id = explode('_', $event);
        $event = $id[1];
        $options = ' -h '.escapeshellarg(long2ip($id[0]));
    }else
        $options = ' -h '.escapeshellarg($ipaddr);

    $options .= ' -u '.escapeshellarg($user).' -p '.escapeshellarg(base64_decode($password)).' --always-prefix --session-timeout=5000 --retransmission-timeout=1000 ';
}

/* archive */
if($archive) {
    if($event)
        $append = '--display='.intval($event).$options;
    else
        $append = $options;

    file_put_contents($log, shell_exec($cmd.$append),FILE_APPEND);
}

if($event)
    $options = '--delete='.intval($event).$options;
else
    $options = '--clear '.$options;

shell_exec($cmd.$options);
?>