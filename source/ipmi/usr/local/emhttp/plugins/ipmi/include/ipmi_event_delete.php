<?php
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_options.php';
$cmd = '/usr/sbin/ipmi-sel --comma-separated-output --output-event-state --no-header-output --interpret-oem-data ';
$logpath = "/boot/config/plugins/ipmi/";
$event = $_GET["event"];
$archive = $_GET["archive"];

if($ipmi_network == 'enable') {
	if($event){
		$id = explode("_", $event);
		$event = $id[1];
		$options = " -h ".long2ip($id[0]);
	}else
		$options = " -h '$ipmi_ipaddr'";

	$options .= " -u $ipmi_user -p ".base64_decode($ipmi_password)." --always-prefix --session-timeout=5000 --retransmission-timeout=1000 ";
}

/*archive function*/
if($archive) {
	if($event)
		$append = "--display=".$event.$options;
	else
		$append = $options;

	file_put_contents("$logpath/archived_events.log", shell_exec($cmd.$append),FILE_APPEND);
}

if($event)
	$options = "--delete=".$event.$options;
else
	$options = "--clear ".$options;

//shell_exec($cmd.$options);
echo json_encode($cmd.$options." APPEND:".$cmd.$append." $event $archive");
?>