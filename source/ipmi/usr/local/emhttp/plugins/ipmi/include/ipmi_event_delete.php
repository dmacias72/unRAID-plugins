<?
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_options.php';
$cmd = '/usr/sbin/ipmi-sel --comma-separated-output --output-event-state --no-header-output --interpret-oem-data ';
$log = "/boot/config/plugins/ipmi/archived_events.log";
$event = $_GET["event"];
$archive = $_GET["archive"];

/* network options */
if($netsvc == 'enable') {
	if($event){
		$id = explode("_", $event);
		$event = $id[1];
		$options = " -h ".long2ip($id[0]);
	}else
		$options = " -h '$ipaddr'";

	$options .= " -u $user -p ".base64_decode($password)." --always-prefix --session-timeout=5000 --retransmission-timeout=1000 ";
}

/* archive */
if($archive) {
	if($event)
		$append = "--display=".$event.$options;
	else
		$append = $options;

	file_put_contents($log, shell_exec($cmd.$append),FILE_APPEND);
}

if($event)
	$options = "--delete=".$event.$options;
else
	$options = "--clear ".$options;

shell_exec($cmd.$options);
?>