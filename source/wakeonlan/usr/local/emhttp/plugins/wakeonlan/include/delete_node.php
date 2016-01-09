<?php
$filename = "/boot/config/plugins/wakeonlan/wakeonlan.xml";

if (is_file($filename)) {
	if ($_POST["mac"] == "all"){
		$xml = new SimpleXMLElement('<?xml version="1.0"?>'.
		'<?xml-stylesheet href="file:///usr/bin/../share/nmap/nmap.xsl" type="text/xsl"?>'.
		'<hosts/>');
		$xml->asXML($filename);
		echo true;
	}else {
		$xml = simplexml_load_file($filename);
		unset($xml->xpath("//*/address[@addr='{$_POST["mac"]}']/parent::*")[0][0]);
		$xml->asXML($filename);
		echo true;
	}
}
?>
