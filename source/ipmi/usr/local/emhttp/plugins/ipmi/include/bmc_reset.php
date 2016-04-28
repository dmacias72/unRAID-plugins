<?
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_options.php';
$cmd = "bmc-device --warm-reset $netopts";
shell_exec($cmd);
?>