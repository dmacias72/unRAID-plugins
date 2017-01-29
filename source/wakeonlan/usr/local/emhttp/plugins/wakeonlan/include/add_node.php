<?
$file = '/boot/config/plugins/wakeonlan/wakeonlan.xml';
if (file_exists($file))
    $xml = simplexml_load_file($file);
else
    $xml = new SimpleXMLElement('<?xml version="1.0"?>'.
    '<?xml-stylesheet href="file:///usr/bin/../share/nmap/nmap.xsl" type="text/xsl"?>'.
    '<hosts/>');

$ip   = htmlspecialchars($_POST['ip']);
$mac  = htmlspecialchars($_POST['mac']);
$name = htmlspecialchars($_POST['name']);

$host = $xml->addChild('host');
$IPv4 = $host->addChild('address');
$IPv4->addAttribute('addr', $ip);
$IPv4->addAttribute('addrtype', 'ipv4');
$Mac = $host->addChild('address');
$Mac->addAttribute('addr', $mac);
$Mac->addAttribute('addrtype', 'mac');
$hostames = $host->addChild('hostnames');
$hostame = $hostames->addChild('hostname');
$hostame->addAttribute('name', $name);

$xml->asXML($file);
?>
