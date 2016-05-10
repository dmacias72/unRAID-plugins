<?
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_options.php';
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_settings_display.php';
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_helpers.php';

/* ipmi settings variables*/
$seld     = isset($cfg['IPMISELD']) ? $cfg['IPMISELD'] : "disable";
$seldpoll = isset($cfg['IPMIPOLL']) ? $cfg['IPMIPOLL'] : "60";
$local    = isset($cfg['LOCAL'])    ? $cfg['LOCAL']    : "disable";

//check running status
$seld_run    = (trim(shell_exec( "[ -f /proc/`cat /var/run/ipmiseld.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" )) == 1);
$fanctrl_run = (trim(shell_exec( "[ -f /proc/`cat /var/run/ipmifan.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" )) == 1);
$running = "<span class='green'>Running</span>";
$stopped = "<span class='orange'>Stopped</span>";
$seld_status    = ($seld_run)    ? $running : $stopped;
$fanctrl_status = ($fanctrl_run) ? $running : $stopped;

/* Get sensor info */
$sensors = [];
if($mod || ($netsvc == 'enable'))
	$sensors = ipmi_sensors();
?>