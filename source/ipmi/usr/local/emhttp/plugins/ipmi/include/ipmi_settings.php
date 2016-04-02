<?php
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_helpers.php';

/* ipmi settings variables*/
$ipmiseld   = isset($ipmi_cfg['IPMISELD']) ? $ipmi_cfg['IPMISELD'] : "disable";
$ipmipoll   = isset($ipmi_cfg['IPMIPOLL']) ? $ipmi_cfg['IPMIPOLL'] : "60";
$ipmi_local = isset($ipmi_cfg['LOCAL'])    ? $ipmi_cfg['LOCAL']    : "disable";

//check running status
$ipmiseld_running = trim(shell_exec( "[ -f /proc/`cat /var/run/ipmiseld.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" ));
$ipmifan_running = trim(shell_exec( "[ -f /proc/`cat /var/run/ipmifan.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" ));
$ipmi_running = "<span class='green'>Running</span>";
$ipmi_stopped = "<span class='orange'>Stopped</span>";
$ipmiseld_status = ($ipmiseld_running) ? $ipmi_running : $ipmi_stopped;
$ipmifan_status = ($ipmifan_running) ? $ipmi_running : $ipmi_stopped;

/* get display temps and fans */
$ipmi_disp_temp1 = isset($ipmi_cfg['DISP_TEMP1']) ? $ipmi_cfg['DISP_TEMP1'] : "";
$ipmi_disp_temp2 = isset($ipmi_cfg['DISP_TEMP2']) ? $ipmi_cfg['DISP_TEMP2'] : "";
$ipmi_disp_fan1  = isset($ipmi_cfg['DISP_FAN1'])  ? $ipmi_cfg['DISP_FAN1']  : "";
$ipmi_disp_fan2  = isset($ipmi_cfg['DISP_FAN2'])  ? $ipmi_cfg['DISP_FAN2']  : "";

/* Get sensor info and check connection */
if($ipmi_mod || $ipmi_network == 'enable') {
	$ipmi_sensors = ipmi_sensors($ipmi_options);
	$ipmi_fans    = ipmi_get_fans($ipmi_sensors);
	$ipmi_board   = trim(shell_exec("ipmi-fru $ipmi_options | grep 'Board Manufacturer' | awk -F ':' '{print $2}'")); // motherboard
}
if($ipmi_network == 'enable'){
	$ipmi_conn = ($ipmi_sensors) ? "Connection successful" : "Connection failed";
}
?>