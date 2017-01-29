<?
$sName = 'httpd';
$errorMsg = '';
$apache_datasize = 0;
$apache_cfg     = parse_plugin_cfg("apache");
$apache_service = isset($apache_cfg['SERVICE']) ? htmlspecialchars($apache_cfg['SERVICE']) : 'disable';
$apache_docroot = isset($apache_cfg['DOCROOT']) ? htmlspecialchars($apache_cfg['DOCROOT']) : '/var/www/html';
$apache_runas   = isset($apache_cfg['RUNAS'])   ? htmlspecialchars($apache_cfg['RUNAS'])   : 'nobody';
$apache_port    = (isset($apache_cfg['PORT']) && is_numeric($apache_cfg['PORT']) && $apache_cfg['PORT'] > 0 && $apache_cfg['PORT'] < 65535 ) ? intval($apache_cfg['PORT']) : 8088;
$apache_running = (intval(trim(shell_exec( "[ -f /proc/`cat /var/run/httpd.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" ))) === 1 );
$httpd_version = shell_exec( "/usr/sbin/httpd -v | grep version | sed -e 's/^Server version: Apache\///'" );
if (is_dir($apache_docroot)) {
    $apache_datasize = trim(shell_exec("du -shm '$apache_docroot' | cut -f1 | sed 's/[^0-9]*//g'"));
    if (trim(shell_exec("stat -f -c '%T' '$apache_docroot'")) == 'tmpfs' ){
        $errorMsg = 'Your directory WILL NOT survive reboot!';
    };
} else
    $errorMsg = 'Your directory does not exist.';
$apache_name = htmlspecialchars($var['NAME']);
$apache_color = ($apache_running == 'yes') ? 'green' : 'orange';
$apache_version = "<b><font style='color:$apache_color;'>$httpd_version</font></b>";

exec("awk -F':' '{ if ( $3 >= 1000 ) print $1}' /etc/passwd", $apache_users); // get array of group users
?>