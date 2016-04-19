<?
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_options.php';

$return = false;
if(array_key_exists('commit', $_POST) && !empty($_POST['ipmicfg'])){
	$config_file = "$plg_path/ipmi.config";
	file_put_contents($config_file, $_POST['ipmicfg']);
	$cmd = "ipmi-sensors-config --diff $netopts 2>&1";//--filename=$config_file --commit $netopts";
	exec($cmd, $output, $return);
}
if($return)
	$return = ['error' => $output];
else
	$return = ['success' => true];
echo json_encode($return);
?>