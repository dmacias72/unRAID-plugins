<?php
require_once '/usr/local/emhttp/plugins/cmdline/include/cmdline_config.php';

$shellinabox_service = isset($cmdline_cfg['SERVICE']) ? $cmdline_cfg['SERVICE']	: "disable";
$cmdline_screen = isset($cmdline_cfg['SCREEN'])			? $cmdline_cfg['SCREEN']	: "disable";
$cmdline_backup = isset($cmdline_cfg['BACKUP'])			? $cmdline_cfg['BACKUP']	: "disable";
$shellinabox_ssl = isset($cmdline_cfg['SSL'])			? $cmdline_cfg['SSL']		: "disable";
$shellinabox_runas = isset($cmdline_cfg['RUNAS'])		? $cmdline_cfg['RUNAS']		: "nobody";
$shellinabox_cert = isset($cmdline_cfg['CERT'])			? $cmdline_cfg['CERT']		:  "certificate.pem";
$shellinabox_version = shell_exec( "/usr/sbin/shellinaboxd --version 2>&1 | grep ShellInABox | sed -e 's/^ShellInABox version //;s/(.*//'" );
$shellinabox_port_status = ($shellinabox_running) ? 
	"<a target='_blank' href='http://".$shellinabox_host.":".$shellinabox_port."' title='Click on link then accept security, then /Tools/CommandLine will work'><b><font color='green'>$shellinabox_port</font></b></a>":
	"<b><font color='orange';'>$shellinabox_port</font></b>";
exec("awk -F':' '{ if ( $3 >= 1000 ) print $1}' /etc/passwd", $shellinabox_users); // get array of group users
?>