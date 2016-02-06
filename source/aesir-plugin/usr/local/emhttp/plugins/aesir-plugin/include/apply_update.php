<?php
$file  = '/boot/config/plugins/aesir-plugin/aesir-master.tar.gz';
$temp  = '/tmp/aesir-master.tar.gz';

if(is_file($temp)){
	rename($temp, $file);
	shell_exec("tar -zxf $file --strip=1 -C '{$_GET['DOCROOT']}'/");
}

echo json_encode(!is_file($temp));
?>
