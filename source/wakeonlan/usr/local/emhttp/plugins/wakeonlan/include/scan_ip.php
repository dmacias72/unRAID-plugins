<?php
	$ip = $_POST["ip"];
	$command = "/usr/bin/nmap -sP $ip | grep 'Host is up' && echo 'on' || echo 'blink'";
	$status = exec($command);
	echo $status;
?>
