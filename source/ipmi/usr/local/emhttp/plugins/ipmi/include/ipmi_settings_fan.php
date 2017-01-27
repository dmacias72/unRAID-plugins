<?
/* fan control settings */
$fancfg_file = "$plg_path/fan.cfg";
if (file_exists($fancfg_file))
    $fancfg = parse_ini_file($fancfg_file);
$fanctrl = isset($fancfg['FANCONTROL']) ? htmlspecialchars($fancfg['FANCONTROL']) :'disable';
$fanpoll = isset($fancfg['FANPOLL'])    ? intval($fancfg['FANPOLL'])              : 3;
$fanip   = (isset($fancfg['FANIP']) && ($netsvc === 'enable')) ? htmlspecialchars($fancfg['FANIP']) : htmlspecialchars($ipaddr) ;

/* board files */
$boards = ['ASRock'=>'','ASRockRack'=>'','Supermicro'=>''];
$board_file = "$plg_path/board.json";
$board_file_status = (file_exists($board_file));
$board_json = ($board_file_status) ? json_decode((file_get_contents($board_file)), true) : [];

/* board info */
$board_log = '/var/log/ipmiboard';
if (file_exists($board_log))
    $board = file_get_contents($board_log);
else{
    $board = ($ipmi || !empty($netopts)) ? trim(shell_exec("ipmi-fru $netopts | grep 'Manufacturer' | awk -F 'r:' '{print $2}'")) : 'unknown';
    if ($board !== 'unknown')
        file_put_contents($board_log, $board);
}

//$board = 'Supermicro';

$board_status = array_key_exists($board, $boards);
$board_asrock = ($board === 'ASRock' || $board === 'ASRockRack');

// fan network options
$fanopts = ($netsvc === 'enable') ? '-h '.escapeshellarg($fanip).' -u '.escapeshellarg($user).' -p '.
    escapeshellarg(base64_decode($password)).' --session-timeout=5000 --retransmission-timeout=1000' : '';
?>
