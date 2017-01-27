<?
/* read config files */
$plg_path    = '/boot/config/plugins/ipmi';
$cfg_file    = "$plg_path/ipmi.cfg";
if (file_exists($cfg_file))
    $cfg    = parse_ini_file($cfg_file);

/* ipmi network options */
$netsvc    = isset($cfg['NETWORK'])   ? htmlspecialchars($cfg['NETWORK'])   : 'disable';
$ipaddr    = isset($cfg['IPADDR'])    ? htmlspecialchars($cfg['IPADDR'])    : '';
$user      = isset($cfg['USER'])      ? htmlspecialchars($cfg['USER'])      : '';
$password  = isset($cfg['PASSWORD'])  ? htmlspecialchars($cfg['PASSWORD'])  : '';

$ignore    = isset($cfg['IGNORE'])    ? htmlspecialchars($cfg['IGNORE'])    : '';
$dignore   = isset($cfg['DIGNORE'])   ? htmlspecialchars($cfg['DIGNORE'])   : '';

/* check if local ipmi driver is loaded */
$ipmi = (file_exists('/dev/ipmi0') || file_exists('/dev/ipmi/0') || file_exists('/dev/ipmidev/0')); // Thanks to ljm42

/* options for network access */
$netopts = ($netsvc === 'enable') ? '--always-prefix -h '.escapeshellarg($ipaddr).' -u '.escapeshellarg($user).' -p '.
    escapeshellarg(base64_decode($password))." --session-timeout=5000 --retransmission-timeout=1000" : '';
?>