<?
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
$seld_status    = ($seld_run) ? $running : $stopped;
$fanctrl_status = ($fanctrl_run)  ? $running : $stopped;

/* get display temps and fans */
$disp_sensor1 = isset($cfg['DISP_SENSOR1']) ? $cfg['DISP_SENSOR1'] : '';
$disp_sensor2 = isset($cfg['DISP_SENSOR2']) ? $cfg['DISP_SENSOR2'] : '';
$disp_sensor3 = isset($cfg['DISP_SENSOR3']) ? $cfg['DISP_SENSOR3'] : '';
$disp_sensor4 = isset($cfg['DISP_SENSOR4']) ? $cfg['DISP_SENSOR4'] : '';


/* Get sensor info */
$fantemp = [];
$sensors = [];
$board = 'unknown';
if($mod || ($netsvc == 'enable')) {
	$sensors = ipmi_sensors();
	$fantemp = ipmi_get_fantemp();
	$board   = trim(shell_exec("ipmi-fru $netopts | grep 'Board Manufacturer' | awk -F 'r:' '{print $2}'")); // motherboard
}

/* get board info and status */
$boards_repo = 'https://raw.githubusercontent.com/dmacias72/unRAID-plugins/master/plugins/boards.json'; 
$boards_file = "$plg_path/boards.json";
if(!is_file($boards_file) || (filemtime($boards_file) < (time() - 3600)))
	get_content_from_github($boards_repo, $boards_file);
$boards = json_decode(file_get_contents($boards_file), true);
$board_status = array_key_exists($board, $boards);

/* check connection */
if($netsvc == 'enable'){
	$conn = (!empty($sensors)) ? "Connection successful" : "Connection failed";
}
?>