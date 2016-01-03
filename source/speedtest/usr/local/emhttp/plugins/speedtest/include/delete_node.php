<?php
$speedtest_filename = "/boot/config/plugins/speedtest/speedtest.xml";
if (is_file($speedtest_filename)) {
	if ($_POST["id"] == "all"){
		$xml = new SimpleXMLElement("<tests></tests>");
		$xml->asXML($speedtest_filename);
		echo true;
	}else {
		$name = $_POST["id"];
		$xml = simplexml_load_file($speedtest_filename);
		unset($xml->xpath("test[@name=$name]")[0]->{0});
		$xml->asXML($speedtest_filename);
		echo true;
	}
}
?>
