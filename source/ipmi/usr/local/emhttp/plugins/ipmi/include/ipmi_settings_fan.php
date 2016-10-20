<?
/* fan control settings */
$fancfg_file = "$plg_path/fan.cfg";
if (file_exists($fancfg_file))
    $fancfg = parse_ini_file($fancfg_file);
$fanctrl = isset($fancfg['FANCONTROL']) ? $fancfg['FANCONTROL'] :'disable';
$fanpoll = isset($fancfg['FANPOLL'])    ? $fancfg['FANPOLL']    : 3;
$fanip   = (isset($fancfg['FANIP']) && ($netsvc == 'enable')) ? $fancfg['FANIP'] : (empty($ipaddr)) ? '' : $ipaddr ;

/* board info */
$board = isset($fancfg['BOARD']) ? $fancfg['BOARD'] : '';
$board_file = "$plg_path/board.json";
$board_file_status = (file_exists($board_file));
$boards = ['ASrock'=>'','ASRockRack'=>''];
$board_json = ($board_file_status) ? json_decode(file_get_contents($board_file), true) : [];

// fan network options
$fanopts = ($netsvc == 'enable') ? "-h $fanip -u $user -p ".
    base64_decode($password)." --session-timeout=5000 --retransmission-timeout=1000" : '';
?>