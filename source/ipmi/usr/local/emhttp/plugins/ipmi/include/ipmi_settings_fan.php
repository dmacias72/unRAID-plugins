<?
/* fan control settings */
$fancfg_file = "$plg_path/fan.cfg";
if (file_exists($fancfg_file))
    $fancfg = parse_ini_file($fancfg_file);
$fanctrl = isset($fancfg['FANCONTROL']) ? $fancfg['FANCONTROL'] :'disable';
$fanpoll = isset($fancfg['FANPOLL'])    ? $fancfg['FANPOLL']    : 3;
$fanip   = (isset($fancfg['FANIP']) && ($netsvc == 'enable')) ? $fancfg['FANIP'] : '';

// fan network options
$fanopts = ($netsvc == 'enable') ? "-h $fanip -u $user -p ".
    base64_decode($password)." --session-timeout=5000 --retransmission-timeout=1000" : '';
?>