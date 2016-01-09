<?php
	$filename = '/var/log/wakeonlan/scan.xml';
	if (is_file($filename))
		unlink($filename);
?>
