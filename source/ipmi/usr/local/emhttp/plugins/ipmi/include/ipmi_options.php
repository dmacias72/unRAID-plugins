<?php
/* read config files */
$ipmi_cfg_file = '/boot/config/plugins/ipmi/ipmi.cfg';
$fan_cfg_file  = '/boot/config/plugins/ipmi/fan.cfg';
if (is_file($ipmi_cfg_file))
	$ipmi_cfg   = parse_ini_file($ipmi_cfg_file);
if (is_file($fan_cfg_file))
	$fan_cfg    = parse_ini_file($fan_cfg_file);

/* ipmi network options */
$ipmi_network  = isset($ipmi_cfg['NETWORK'])   ? $ipmi_cfg['NETWORK']   : 'disable';
$ipmi_ipaddr   = isset($ipmi_cfg['IPADDR'])    ? $ipmi_cfg['IPADDR']    : '';
$ipmi_user     = isset($ipmi_cfg['USER'])      ? $ipmi_cfg['USER']      : '';
$ipmi_password	= isset($ipmi_cfg['PASSWORD'])  ? $ipmi_cfg['PASSWORD']  : '';

/* fan control */
$fancontrol    = isset($fan_cfg['FANCONTROL']) ? $fan_cfg['FANCONTROL'] : "disable";
$fanpoll       = isset($fan_cfg['FANPOLL'])    ? $fan_cfg['FANPOLL']    : 1;
$fanip         = isset($fan_cfg['FANIP'])      ? $fan_cfg['FANIP']      : 'None';

/* check if local ipmi driver is loaded */
if($ipmi_network == "disable")
	$ipmi_mod = shell_exec("modprobe ipmi_si --first-time 2>&1 | grep -q 'Module already in kernel' && echo 1 || echo 0 2> /dev/null");

/* options for network access */
$ipmi_options = ($ipmi_network == 'enable') ? "--always-prefix -h $ipmi_ipaddr -u $ipmi_user -p ".
	base64_decode($ipmi_password)." --session-timeout=5000 --retransmission-timeout=1000" : '';
$fan_options = ($ipmi_network == 'enable') ? "-h $fanip -u $ipmi_user -p ".
	base64_decode($ipmi_password)." --session-timeout=5000 --retransmission-timeout=1000" : '';

?>