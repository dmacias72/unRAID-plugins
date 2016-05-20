<?
$pfg      = '/boot/config/plugins/qnotify/config.py';
$cfg      = '/boot/config/plugins/qnotify/qnotify.cfg';
$data     = '';
$port     = $_POST['port'];
$username = trim($_POST['username']);
$password = trim($_POST['password']);
$token    = trim($_POST['pushbulletAccessToken']);
$device   = trim($_POST['pushbulletDeviceName']);
$keyword  = implode(',', preg_split('/[\ \,]+/', $_POST['pushIfKeyword'], -1, PREG_SPLIT_NO_EMPTY));
$daemon   = $_POST['DAEMON'];

$data = "host = \"localhost\"\n".
        "port = $port\n".
        "username = \"$username\"\n".
        "password = \"$password\"\n".
        "enabledPlugins = [\"pushbullet\"]\n".
        "pushbulletAccessToken = \"$token\"\n".
        "pushbulletDeviceName = \"$device\"\n".
        "pushIfKeyword = [\"$keyword\"]\n";

file_put_contents($pcfg, $data);

$data = "DAEMON=\"$daemon\"\n";

file_put_contents($cfg, $data);

if($daemon == 'enable')
    $cmd = 'start';
else
    $cmd = 'stop';

shell_exec("/etc/rc.d/rc.qnotify $cmd");
?>