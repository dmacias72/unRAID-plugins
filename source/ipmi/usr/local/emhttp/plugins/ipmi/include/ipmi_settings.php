<?
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_helpers.php';
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_settings_display.php';
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_settings_fan.php';

/* ipmi settings variables*/
$seld     = isset($cfg['IPMISELD']) ? $cfg['IPMISELD'] : 'disable';
$seldpoll = isset($cfg['IPMIPOLL']) ? $cfg['IPMIPOLL'] : '60';
$local    = isset($cfg['LOCAL'])    ? $cfg['LOCAL']    : 'disable';

// create a lockfile for ipmi dashboard
$dashfile = "$plg_path/ipmidash";
$dashlock = (file_exists($dashfile));
if(empty($dashtypes)){
    if ($dashlock)
        unlink($dashfile);
}else {
    if (!$dashlock)
        file_put_contents($dashfile, '');
}

// check running status
$seld_run    = (trim(shell_exec( "[ -f /proc/`cat /var/run/ipmiseld.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" )) == 1);
$fanctrl_run = (trim(shell_exec( "[ -f /proc/`cat /var/run/ipmifan.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" )) == 1);
$running     = "<span class='green'>Running</span>";
$stopped     = "<span class='orange'>Stopped</span>";
$seld_status    = ($seld_run)    ? $running : $stopped;
$fanctrl_status = ($fanctrl_run) ? $running : $stopped;

/* get sensor and board info */
$sensors = ipmi_sensors();
$fantemp = ipmi_get_fantemp();
$board   = ($ipmi || !empty($netopts)) ? trim(shell_exec("ipmi-fru $netopts | grep 'Board Manufacturer' | awk -F 'r:' '{print $2}'")) : 'unknown';
$boards_repo = 'https://raw.githubusercontent.com/dmacias72/unRAID-plugins/master/plugins/boards.json';
$boards_file = "$plg_path/boards.json";

if (!is_file($boards_file) || (filemtime($boards_file) < (time() - 3600)))
	get_content_from_github($boards_repo, $boards_file);

$boards       = json_decode(file_get_contents($boards_file), true);
$board_status = array_key_exists($board, $boards);

/* check connection */
if (!empty($netopts))
    $conn = (empty($sensors)) ? 'Connection failed' : 'Connection successful';
?>