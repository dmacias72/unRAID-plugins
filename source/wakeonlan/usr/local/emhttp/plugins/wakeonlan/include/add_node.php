<?php
$filename = "/boot/config/plugins/wakeonlan/wakeonlan.xml";
if (is_file($filename)) {
	$xml = simplexml_load_file($filename);
}else{
	$xml = new SimpleXMLElement('<?xml version="1.0"?>'.
	'<?xml-stylesheet href="file:///usr/bin/../share/nmap/nmap.xsl" type="text/xsl"?>'.
	'<hosts/>');
}	

$host = $xml->addChild('host');
$IPv4 = $host->addChild('address');
$IPv4->addAttribute('addr', $_POST["ip"]);
$IPv4->addAttribute('addrtype', "ipv4");
$Mac = $host->addChild('address');
$Mac->addAttribute('addr', $_POST["mac"]);
$Mac->addAttribute('addrtype', "mac");
$hostames = $host->addChild('hostnames');
$hostame = $hostames->addChild('hostname');
$hostame->addAttribute('name', $_POST["name"]);

$xml->asXML($filename);
?>
