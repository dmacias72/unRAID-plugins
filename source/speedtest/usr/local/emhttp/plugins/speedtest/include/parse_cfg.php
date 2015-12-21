<?php
require_once("/usr/local/emhttp/webGui/include/Wrappers.php");

$speedtest_cfg = parse_plugin_cfg("speedtest");
$speedtest_filename = "/boot/config/plugins/speedtest/speedtest.xml";
$speedtest_secure = isset($speedtest_cfg['SECURE']) ? $speedtest_cfg['SECURE'] 	: "no";
$speedtest_share = isset($speedtest_cfg['SHARE'])   ? $speedtest_cfg['SHARE'] 	: "share";
$speedtest_units = isset($speedtest_cfg['UNITS'])   ? $speedtest_cfg['UNITS'] 	: "bits";
$speedtest_server = isset($speedtest_cfg['SERVER']) ? $speedtest_cfg['SERVER'] 	: "none";
$speedtest_list = isset($speedtest_cfg['LIST']) ? $speedtest_cfg['LIST'] 	: "auto";

if (!is_file($speedtest_filename)) {
	$xml = new SimpleXMLElement("<tests></tests>");
	$xml->asXML($speedtest_filename);
}
?>
