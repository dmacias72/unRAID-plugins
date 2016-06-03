<?
$speedtest_cfg_file = '/boot/config/plugins/speedtest/speedtest.cfg';
$speedtest_cfg      = file_exists($speedtest_cfg_file) ?parse_ini_file($speedtest_cfg_file) : [];
$speedtest_filename = '/boot/config/plugins/speedtest/speedtest.xml';
$speedtest_secure   = isset($speedtest_cfg['SECURE'])  ? $speedtest_cfg['SECURE']  : 'no';
$speedtest_share    = isset($speedtest_cfg['SHARE'])   ? $speedtest_cfg['SHARE']   : 'share';
$speedtest_units    = isset($speedtest_cfg['UNITS'])   ? $speedtest_cfg['UNITS']   : 'bits';
$speedtest_server   = isset($speedtest_cfg['SERVER'])  ? $speedtest_cfg['SERVER']  : 'none';
$speedtest_list     = isset($speedtest_cfg['LIST'])    ? $speedtest_cfg['LIST']    : 'auto';
$speedtest_timeout  = isset($speedtest_cfg['TIMEOUT']) ? $speedtest_cfg['TIMEOUT'] : '10';
$speedtest_version  = isset($speedtest_cfg['VERSION']) ? $speedtest_cfg['VERSION'] : '0.3.4';

if (!file_exists($speedtest_filename)) {
    $xml = new SimpleXMLElement("<tests></tests>");
    $xml->asXML($speedtest_filename);
}

?>