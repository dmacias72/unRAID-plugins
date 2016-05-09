<?
/* fan control settings */
$fancfg_file = "$plg_path/fan.cfg";
if (is_file($fancfg_file))
    $fancfg = parse_ini_file($fancfg_file);
$fanctrl = isset($fancfg['FANCONTROL']) ? $fancfg['FANCONTROL'] :'disable';
$fanpoll = isset($fancfg['FANPOLL'])    ? $fancfg['FANPOLL']    : 3;
$fanip   = (isset($fancfg['FANIP']) && ($netsvc == 'enable')) ? $fancfg['FANIP'] : '';
?>