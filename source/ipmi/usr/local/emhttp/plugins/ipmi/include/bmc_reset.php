<?
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_options.php';

$cmd = "bmc-device --warm-reset $netopts";
exec($cmd, $output, $return_var=null);

if($return_var)
    $return = ['error' => $output];
else
    $return = ['success' => true];

echo json_encode($return);
?>