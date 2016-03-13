<?php
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_options.php';
$cmd = '/usr/sbin/ipmi-sel ';
$event = $_GET["event"];

if($event == 'clear' || $event == 'post-clear') {
	$options = "--$event";
	if($ipmi_network == 'enable')
		$options .= " -h '$ipmi_ipaddr'";
}else{
	$options = "--delete=";
	if($ipmi_network == 'enable'){
		$id = explode("_", $event);
		$options .= $id[1]." -h ".long2ip($id[0])." -u $ipmi_user";
	}else{
		$options .= $event;
		}
}
if($ipmi_network == 'enable')
	$options .= " -p ".base64_decode($ipmi_password)." --session-timeout=10000 --retransmission-timeout=1000 ";

if($event == 'post-clear'){
	$logpath = "/boot/config/plugins/ipmi/logs";
	if(!is_dir($logpath))
		mkdir($logpath);
	$gzfile = "$logpath/ipmi_event_log-".date("Y-m-d-His").".gz";
	$fp = gzopen($gzfile, 'w9'); // w == write, 9 == highest compression
	gzwrite($fp, shell_exec($cmd.$options));
	gzclose($fp);
}else{
	shell_exec($cmd.$options);
}
?>