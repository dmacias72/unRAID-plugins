<?php
$command = "speedtest --list 2>/dev/null";
exec($command, $output);
$array = array();
for ($i = 2; $i < sizeof($output); $i++) {
	$value = explode(") ", $output[$i],2);
	$array[] = $value;
}
echo json_encode($array);
?>
