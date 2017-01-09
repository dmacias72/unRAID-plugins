<?
/* fan control settings */
$fancfg_file = "$plg_path/fan.cfg";
if (file_exists($fancfg_file))
    $fancfg = parse_ini_file($fancfg_file);
$fanctrl = isset($fancfg['FANCONTROL']) ? $fancfg['FANCONTROL'] :'disable';
$fanpoll = isset($fancfg['FANPOLL'])    ? $fancfg['FANPOLL']    : 3;
$fanip   = (isset($fancfg['FANIP']) && ($netsvc == 'enable')) ? $fancfg['FANIP'] : $ipaddr ;

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

$boardname_log = '/var/log/boardname';
if (file_exists($boardname_log))
    $boardname = file_get_contents($boardname_log);
else{
    $boardname = ($ipmi || !empty($netopts)) ? trim(shell_exec("dmidecode -q -t 2|awk -F: '/^\tProduct Name:/{p=$2;} END{print p}'")) : 'unknown';
    if ($boardname !== 'unknown')
        file_put_contents($boardname_log, $boardname);
}

//$board = 'Supermicro';

$board_status = array_key_exists($board, $boards);
$board_asrock = ($board == 'ASRock' || $board == 'ASRockRack');

// fan network options
$fanopts = ($netsvc == 'enable') ? "-h $fanip -u $user -p ".
    base64_decode($password)." --session-timeout=5000 --retransmission-timeout=1000" : '';
?>
