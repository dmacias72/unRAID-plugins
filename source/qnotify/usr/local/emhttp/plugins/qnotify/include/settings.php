<?
$qcfg_file = '/boot/config/plugins/qnotify/qnotify.cfg';
if(file_exists($qcfg_file))
    $qcfg = parse_ini_file($qcfg_file);

$pcfg_file = '/boot/config/plugins/qnotify/config.py';
if(file_exists($pcfg_file))
    $pcfg = parse_ini_file($pcfg_file);

$qdaemon = isset($qcfg['DAEMON']) ? $qcfg['DAEMON'] : "disable";
//$qipaddr = preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $ipmi_cfg['host']) ?
//    $qcfg['host'] : $var['IPADDR'];
$qipaddr = 'localhost';
$qport = (isset($pcfg['port']) && is_numeric($pcfg['port']) && $pcfg['port'] > 0 && $pcfg['port'] < 65535 ) ? $pcfg['port'] : 4242;
$qusername = isset($pcfg['username']) ? $pcfg['username'] : '';
$qpassword = isset($pcfg['password']) ? $pcfg['password'] : '';
$qdevice   = isset($pcfg['pushbulletDeviceName']) ? $pcfg['pushbulletDeviceName'] : 'None';
$qkeywords = isset($pcfg['pushIfKeyword']) ? trim($pcfg['pushIfKeyword'], '[]') : '';

//get service token
$qservice_file = '/boot/config/plugins/dynamix/notifications/agents/Pushbullet.sh';
$token = '';
if(file_exists($qservice_file)) {
    $service_array = file($qservice_file);
    foreach($service_array as $line) {
        if(strpos($line, 'TOKEN=') !== false) {
            list(, $token) = explode('=', $line);
        }
    }
    $token = trim(trim($token), '"');
}
$qtoken = isset($pcfg['pushbulletAccessToken']) ? $pcfg['pushbulletAccessToken'] : '';//$token;

//check running status
$qrunning = (trim(shell_exec( "[ -f /proc/`cat /var/run/qnotify.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" )) == 1);
$daemon_running  = "<span class='green'>Running</span>";
$daemon_stopped  = "<span class='orange'>Stopped</span>";
$qstatus  = ($qrunning) ? $daemon_running : $daemon_stopped;
?>