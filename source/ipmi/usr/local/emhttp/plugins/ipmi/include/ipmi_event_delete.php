<?php
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_helpers.php';
$cmd = '/usr/sbin/ipmi-sel ';
$event = $_GET["event"];

if($event == 'all') {
	$options = "--clear";
	if($ipmi_network == 'enable')
		$options .= " -h '$ipmi_ipaddr'";
}else{
	$options = "--delete=";
	if($ipmi_network == 'enable'){
		$id = explode("-", $event);
		$options .= $id[1]." -h ".long2ip($id[0])." -u $ipmi_user";
	}else{
		$options .= $event;
		}
}
if($ipmi_network == 'enable')
	$options .= " -p ".base64_decode($ipmi_password)." --session-timeout=5000 --retransmission-timeout=1000 ";

shell_exec($cmd.$options);
?>