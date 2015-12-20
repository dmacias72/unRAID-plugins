<?php
require("/usr/local/emhttp/plugins/speedtest/include/parse_cfg.php");

shell_exec("echo -e 'Internet bandwidth test started' | logger -tspeedtest");

$options = "";
if ( $speedtest_list == "manual")
	$options .= " --server ".$speedtest_server;
if ($speedtest_secure == "secure")
	$options .= " --secure";
if ($speedtest_share == "share")
	$options .= " --share"; 
if ($speedtest_units == "bytes")
		$options .= " --bytes";

$command = "speedtest --simple".$options." 2>/dev/null";
exec($command, $output);

$array = array("Name" => round(microtime(true) * 1000));

for ($i = 0; $i < sizeof($output); $i++) {
	$value = explode(": ", $output[$i]);
	$key = ($i != 3) ? $value[0] : "Share"; 
	$array[$key] = $value[1];
}

if (sizeof($array) < 5)
	$array["Share"] = "";

$xml = simplexml_load_file($speedtest_filename);
$test = $xml->addChild('test');
$test->addAttribute('name',	  $array["Name"]);
$test->addAttribute('ping', 	  $array["Ping"]);
$test->addAttribute('download', $array["Download"]);
$test->addAttribute('upload',   $array["Upload"]);
$test->addAttribute('share',	  $array["Share"]);
$xml->asXML($speedtest_filename);

shell_exec("echo -e 'Internet bandwidth test completed' | logger -tspeedtest");

if ($_POST["show"]) 
 echo json_encode($array);
?>
