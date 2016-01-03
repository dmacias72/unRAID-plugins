<?php
	$filename = '/usr/local/emhttp/plugins/wakeonlan/scan.xml';
	if (is_file($filename))
		unlink($filename);
	$ip = $_POST["ip"];
	$net = substr_replace($ip ,"",-1)."0/24";
	$command = "/usr/bin/nmap -sP -oX $filename --exclude $ip $net";
	exec($command);
?>
