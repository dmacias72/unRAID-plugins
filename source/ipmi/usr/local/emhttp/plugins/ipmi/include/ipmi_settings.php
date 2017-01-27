<?
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_helpers.php';
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_settings_display.php';
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_settings_fan.php';

/* ipmi settings variables*/
$seld     = isset($cfg['IPMISELD']) ? htmlspecialchars($cfg['IPMISELD']) : 'disable';
$seldpoll = isset($cfg['IPMIPOLL']) ? intval($cfg['IPMIPOLL'])           : 60;
$local    = isset($cfg['LOCAL'])    ? htmlspecialchars($cfg['LOCAL'])    : 'disable';
$dash     = isset($cfg['DASH'])     ? htmlspecialchars($cfg['DASH'])     : 'disable';

// check running status
$seld_run       = (intval(trim(shell_exec( "[ -f /proc/`cat /var/run/ipmiseld.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" ))) === 1);
$fanctrl_run    = (intval(trim(shell_exec( "[ -f /proc/`cat /var/run/ipmifan.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" ))) === 1);
$running        = "<span class='green'>Running</span>";
$stopped        = "<span class='orange'>Stopped</span>";
$seld_status    = ($seld_run)    ? $running : $stopped;
$fanctrl_status = ($fanctrl_run) ? $running : $stopped;

/* get sensors */
$sensors     = ipmi_sensors($ignore);
$allsensors  = ipmi_sensors();
$fansensors  = ipmi_fan_sensors($ignore);

// create a lockfile for ipmi dashboard
$dashfile = "$plg_path/ipmidash";
$dashlock = (file_exists($dashfile));
if($dash === 'enable'){
    if(!$dashlock)
        file_put_contents($dashfile, '');
}else{
    if($dashlock)
        unlink($dashfile);
}

/* check connection */
if (!empty($netopts))
    $conn = (empty($sensors)) ? 'Connection failed' : 'Connection successful';
?>