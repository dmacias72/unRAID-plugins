<?
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_settings.php';

function format_ipmi_temp($reading, $unit, $dot) {
  return ($reading>0 ? ($unit=='F' ? round(9/5*$reading+32) : str_replace('.',$dot,$reading)) : '##')."<small>&deg;$unit</small>";
}

if ($disp_temp1 || $disp_temp2 || $disp_fan1 || $disp_fan2){
	$readings = ipmi_get_readings();
	$temps = [];

	if ($readings[$disp_temp1])
		$temps[] = "<img src='/plugins/ipmi/icons/cpu.png' title='".$readings[$disp_temp1]['Name']
			." (".$readings[$disp_temp1]['ID'].")' class='icon'>"
			.format_ipmi_temp(floatval($readings[$disp_temp1]['Reading']), $_GET['unit'], $_GET['dot']);

	if ($readings[$disp_fan1])
		$temps[] = "<img src='/plugins/ipmi/icons/fan.png' title='".$readings[$disp_fan1]['Name']
			." (".$readings[$disp_fan1]['ID'].")' class='icon'>"
			.floatval($readings[$disp_fan1]['Reading'])."<small>&thinsp;rpm</small>";

	if ($readings[$disp_temp2])
		$temps[] = "<img src='/plugins/ipmi/icons/mb.png' title='".$readings[$disp_temp2]['Name']
			." (".$readings[$disp_temp2]['ID'].")' class='icon'>"
			.format_ipmi_temp(floatval($readings[$disp_temp2]['Reading']), $_GET['unit'], $_GET['dot']);

	if ($readings[$disp_fan2])
		$temps[] = "<img src='/plugins/ipmi/icons/fan.png' title='".$readings[$disp_fan2]['Name']
			." (".$readings[$disp_fan2]['ID'].")' class='icon'>"
			.floatval($readings[$disp_fan2]['Reading'])."<small>&thinsp;rpm</small>";
}
if ($temps)
	echo "<span id='temps' style='margin-right:16px'>".implode('&nbsp;', $temps)."</span>";
?>