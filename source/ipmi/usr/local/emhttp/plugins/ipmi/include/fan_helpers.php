<?
/* get fan and temp sensors array */
function ipmi_get_fantemp() {
    global $ipmi, $fanopts, $hdd_temp;

    if(!$ipmi && empty($fanopts))
        return [];

    $cmd = "/usr/sbin/ipmi-sensors --comma-separated-output --no-header-output --interpret-oem-data $fanopts 2>/dev/null";
    exec($cmd, $output, $return_var=null);

    if ($return_var)
        return []; // return empty array if error

    // add highest hard drive temp sensor
    $output[] = "99,HDD Temperature,Temperature, $hdd_temp,C,Ok";

    // key names for ipmi sensors output
    $keys = ['ID', 'Name', 'Type', 'Reading', 'Units', 'Event'];
    $sensors = [];

    foreach($output as $line){

        // add sensor keys as keys to ipmi sensor output
        $sensor_raw = explode(",", $line);
        $size_raw = sizeof($sensor_raw);
        $sensor = ($size_raw < 6) ? []: array_combine($keys, array_slice($sensor_raw,0,6,true));

        if ($sensor['Type'] == 'Temperature' || $sensor['Type'] == 'Fan')
            $sensors[$sensor['ID']] = $sensor;
    }
    return $sensors; // sensor readings
    unset($sensors);
}

/* get all fan options for fan control */
function get_fanctrl_options(){
    global $fantemp, $fancfg, $board;
    if($board == 'ASRock' || $board == 'ASRockRack') {
        $i = 0;
        foreach($fantemp as $key => $sensor){
            if ($sensor['Type'] == 'Fan'){
                $fan_temp = 'FANTEMP'.$i;
                $fan_sensor = $fantemp[$fancfg[$fan_temp]];
                $templo = 'TEMPLO'.$i;
                $temphi = 'TEMPHI'.$i;
                $fan_min = 'FANMIN'.$i;

                // hidden fan id
                echo '<input type="hidden" name="FAN'.$i.'" value="'.$key.'"/>';

                // fan name: reading => temp name: reading
                echo '<dl><dt>'.$sensor['Name'].' ('.floatval($sensor['Reading']).' '.$sensor['Units'].'):</dt><dd><span class="fanctrl-basic">';
                if ($fan_sensor['Name'])
                    echo $fan_sensor['Name'].' ('.floatval($fan_sensor['Reading']).' '.$fan_sensor['Units'].'), '.
                    $fancfg[$templo].', '.$fancfg[$temphi].', '.$fancfg[$fan_min];
                else
                    echo 'Auto';
                echo '</span><span class="fanctrl-settings">&nbsp;</span></dd></dl>';

                // temperature sensor
                echo '<dl class="fanctrl-settings">'.
                '<dt><dl><dd>Temperature sensor:</dd></dl></dt><dd>'.
                '<select name="'.$fan_temp.'" class="fanctrl-temp fanctrl-settings">'.
                '<option value="0">Auto</option>';
                echo get_temp_options($fancfg[$fan_temp], true);
                echo '</select></dd></dl>';

                // low temperature threshold
                echo '<dl class="fanctrl-settings">'.
                '<dt><dl><dd>Low temperature threshold (&deg;C):</dd></dl></dt>'.
                '<dd><select name="'.$templo.'" class="'.$fan_temp.' fanctrl-settings">'.
                '<option value="0">Auto</option>';
                echo get_temp_range('LO', $fancfg[$templo]);
                echo '</select></dd></dl>';

                // high temperature threshold
                echo '<dl class="fanctrl-settings">'.
                '<dt><dl><dd>High temperature threshold (&deg;C):</dd></dl></dt>'.
                '<dd><select name="'.$temphi.'" class="'.$fan_temp.' fanctrl-settings">'.
                '<option value="0">Auto</option>';
                echo	get_temp_range('HI', $fancfg[$temphi]);
                echo '</select></dd></dl>';

                echo '<dl class="fanctrl-settings">'.
                // fan control minimum speed
                '<dt><dl><dd>Fan speed minimum (1-64):</dd></dl></dt><dd>'.
                '<select name="'.$fan_min.'" class="'.$fan_temp.' fanctrl-settings">';
                echo get_min_options($fancfg[$fan_min]);
                echo '</select></dd></dl>&nbsp;';

                $i++;
            }
        }
    } else {
        echo '<dl><dt>&nbsp;</dt><dd><p><font class="red">Your board is not currently supported</font></p></dd></dl>';
    }
}

/* get select options for temp & fan sensor types from fan ip*/
function get_temp_options($selected=null){
    global $fantemp, $fanip;
    $options = "";
    foreach($fantemp as $id => $sensor){
        if (($sensor['Type'] == 'Temperature') || ($sensor['Name'] == 'HDD Temperature')){
            $name = $sensor['Name'];
            $options .= "<option value='$id'";

            // set saved option as selected
            if ($selected == $id)
                $options .= " selected";

        $options .= ">$name</option>";
        }
    }
    return $options;
}

/* get options 1 - 64 for fan minimum speed */
function get_min_options($limit){
    $options = '';
        for($i = 1; $i <= 64; $i++){
            $options .= '<option value="'.$i.'"';
            if($limit == $i)
                $options .= ' selected';

            $options .= '>'.$i.'</option>';
        }
    return $options;
}

/* get network ip options for fan control */
function get_fanip_options(){
    global $ipaddr, $fanip;
    $ips = 'None,'.$ipaddr;
    $ips = explode(',',$ips);
        foreach($ips as $ip){
            $options .= '<option value="'.$ip.'"';
            if($fanip == $ip)
                $options .= ' selected';

            $options .= '>'.$ip.'</option>';
        }
    echo $options;
}
?>