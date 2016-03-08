<?
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_helpers.php';

function format_ipmi_temp($reading, $unit, $dot) {
  return ($reading>0 ? ($unit=='F' ? round(9/5*$reading+32) : str_replace('.',$dot,$reading)) : '##')."&thinsp;$unit";
}

if ($ipmi_disp_temp1 || $ipmi_disp_temp2 || $ipmi_disp_fan1 || $ipmi_disp_fan2){
	$ipmi_readings = ipmi_get_readings($ipmi_options);
	$ipmi_temps = [];

	if ($ipmi_readings[$ipmi_disp_temp1])
		$ipmi_temps[] = "<img src='/plugins/ipmi/icons/cpu.png' title='".$ipmi_readings[$ipmi_disp_temp1]['Name']
			." (".$ipmi_readings[$ipmi_disp_temp1]['ID'].")' class='icon'>"
			.floatval(format_ipmi_temp($ipmi_readings[$ipmi_disp_temp1]['Reading'], $_GET['unit'], $_GET['dot']));

	if ($ipmi_readings[$ipmi_disp_temp2])
		$ipmi_temps[] = "<img src='/plugins/ipmi/icons/mb.png' title='".$ipmi_readings[$ipmi_disp_temp2]['Name']
			." (".$ipmi_readings[$ipmi_disp_temp2]['ID'].")' class='icon'>"
			.floatval(format_ipmi_temp($ipmi_readings[$ipmi_disp_temp2]['Reading'], $_GET['unit'], $_GET['dot']));

	if ($ipmi_readings[$ipmi_disp_fan1])
		$ipmi_temps[] = "<img src='/plugins/ipmi/icons/fan.png' title='".$ipmi_readings[$ipmi_disp_fan1]['Name']
			." (".$ipmi_readings[$ipmi_disp_fan1]['ID'].")' class='icon'>"
			.floatval($ipmi_readings[$ipmi_disp_fan1]['Reading'])."&thinsp;rpm";

	if ($ipmi_readings[$ipmi_disp_fan2])
		$ipmi_temps[] = "<img src='/plugins/ipmi/icons/fan.png' title='".$ipmi_readings[$ipmi_disp_fan2]['Name']
			." (".$ipmi_readings[$ipmi_disp_fan2]['ID'].")' class='icon'>"
			.floatval($ipmi_readings[$ipmi_disp_fan2]['Reading'])."&thinsp;rpm";
}
if ($ipmi_temps)
	echo "<span id='temps' style='margin-right:16px'>".implode('&nbsp;', $ipmi_temps)."</span>";
?>
