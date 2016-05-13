<?
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_options.php';
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_settings_display.php';
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_helpers.php';

function format_ipmi_temp($reading, $unit, $dot) {
  return (($reading == 0) ? ($unit=='F' ? round(9/5*$reading+32) : str_replace('.',$dot,$reading)) : '##')."<small>&deg;$unit</small>";
}

$disp_sensors = [$disp_sensor1, $disp_sensor2, $disp_sensor3, $disp_sensor4];

if (!empty($disp_sensors)){
    if(($mod) || ($netsvc == 'enable'))
        $readings = ipmi_get_readings();
    $displays = [];
    foreach($disp_sensors as $disp_sensor){
        if ($readings[$disp_sensor]){
            $disp_name    = $readings[$disp_sensor]['Name'];
            $disp_id      = $readings[$disp_sensor]['ID'];
            $disp_reading = $readings[$disp_sensor]['Reading'];
            $displays[]   = ($readings[$disp_sensor]['Type'] == 'Temperature') ? 
            "<img src='/plugins/ipmi/icons/cpu.png' title='$disp_name ($disp_id)' class='icon'>"
                .format_ipmi_temp(floatval($disp_reading), $_GET['unit'], $_GET['dot']): 
            "<img src='/plugins/ipmi/icons/fan.png' title='$disp_name ($disp_id)' class='icon'>"
                .floatval($disp_reading)."<small>&thinsp;rpm</small>";
        }
    }
}
if ($displays)
    echo "<span id='temps' style='margin-right:16px'>".implode('&nbsp;', $displays)."</span>";
?>