<?
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_helpers.php';

/* ipmi settings variables*/
$seld     = isset($cfg['IPMISELD']) ? $cfg['IPMISELD'] : "disable";
$seldpoll = isset($cfg['IPMIPOLL']) ? $cfg['IPMIPOLL'] : "60";
$local    = isset($cfg['LOCAL'])    ? $cfg['LOCAL']    : "disable";

//check running status
$seld_run    = trim(shell_exec( "[ -f /proc/`cat /var/run/ipmiseld.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" ));
$fanctrl_run = trim(shell_exec( "[ -f /proc/`cat /var/run/ipmifan.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" ));
$running = "<span class='green'>Running</span>";
$stopped = "<span class='orange'>Stopped</span>";
$seld_status    = ($seld_run) ? $running : $stopped;
$fanctrl_status = ($fanctrl_run)  ? $running : $stopped;

/* get display temps and fans */
$disp_temp1 = isset($cfg['DISP_TEMP1']) ? $cfg['DISP_TEMP1'] : "";
$disp_temp2 = isset($cfg['DISP_TEMP2']) ? $cfg['DISP_TEMP2'] : "";
$disp_fan1  = isset($cfg['DISP_FAN1'])  ? $cfg['DISP_FAN1']  : "";
$disp_fan2  = isset($cfg['DISP_FAN2'])  ? $cfg['DISP_FAN2']  : "";


/* Get sensor info and check connection */
$fantemp = [];
$sensors = [];
$board = '';
if(($mod == 1) || ($netsvc == 'enable')) {
	$sensors = ipmi_sensors();
	$fantemp = ipmi_get_fantemp();
	$board   = trim(shell_exec("ipmi-fru $netopts | grep 'Board Manufacturer' | awk -F 'r:' '{print $2}'")); // motherboard
}
$conn = '';
if($netsvc == 'enable'){
	$conn = ($sensors) ? "Connection successful" : "Connection failed";
}
?>