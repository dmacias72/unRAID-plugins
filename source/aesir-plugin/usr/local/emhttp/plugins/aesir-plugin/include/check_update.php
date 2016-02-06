<?php
$repo = 'https://api.github.com/repos/linuxserver/Aesir/tarball/master';
$file = '/boot/config/plugins/aesir-plugin/aesir-master.tar.gz';
$temp  = '/tmp/aesir-master.tar.gz';

//Only download repo update if the current one is 1 hour old or more
if (!is_file($aesir_file) || (filemtime($aesir_file) < (time() - 3600)))
	get_content_from_github($repo, $temp);

if (is_file($file) && is_file($temp)){
	if (sha1_file($file) == sha1_file($temp))
		unlink($temp);
}
echo json_encode(is_file($temp));

// get file from github
function get_content_from_github($repo, $file) {
	$context = stream_context_create(array('http' => array(
   	'header' => 'User-Agent: unRAID',
	)));
	$download = file_get_contents($repo, false, $context);
	if (!empty($download))
			file_put_contents($file, $download);
	unset($download);
}
?>
