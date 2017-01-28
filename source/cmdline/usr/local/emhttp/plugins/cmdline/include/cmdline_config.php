<?
$cmdline_cfg         = parse_ini_file('/boot/config/plugins/cmdline/cmdline.cfg');
$shellinabox_ipaddr  = isset($cmdline_cfg['IPADDR'])      ? htmlspecialchars($cmdline_cfg['IPADDR']) : 'disable';
$shellinabox_hostip  = isset($var['IPADDR'])              ? htmlspecialchars($var['IPADDR'])         : $eth0['IPADDR:0'];
$shellinabox_host    = ($shellinabox_ipaddr == 'disable') ? htmlspecialchars($var['NAME'])           : $shellinabox_hostip;
$shellinabox_ssl     = isset($cmdline_cfg['SSL'])         ? htmlspecialchars($cmdline_cfg['SSL'])     : 'disable';
$shellinabox_port    = (isset($cmdline_cfg['PORT']) && is_numeric($cmdline_cfg['PORT']) && $cmdline_cfg['PORT'] > 0 && $cmdline_cfg['PORT'] < 65535 ) ? intval($cmdline_cfg['PORT']) : 4200;
$shellinabox_running = (intval(trim(shell_exec( "[ -f /proc/`cat /var/run/shellinaboxd.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" ))) === 1);
$status_running      = '<span class="green">Running</span>';
$status_stopped      = '<span class="orange">Stopped</span>';
$shellinabox_status  = ($shellinabox_running) ? $status_running : $status_stopped;
$shellinabox_http    = ($shellinabox_ssl == 'disable') ? 'http' : 'https';
?>