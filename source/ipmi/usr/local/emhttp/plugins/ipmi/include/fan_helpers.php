<?
/* get fan and temp sensors array */
function ipmi_fan_sensors($ignore=null) {
    global $ipmi, $fanopts, $hdd_temp;

    // return empty array if no ipmi detected or network options
    if(!($ipmi || !empty($fanopts)))
        return [];

    $ignored = (empty($ignore)) ? '' : "-R $ignore";
    $cmd = "/usr/sbin/ipmi-sensors --comma-separated-output --no-header-output --interpret-oem-data $fanopts $ignored 2>/dev/null";
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
    global $fansensors, $fancfg, $board, $board_json, $board_file_status, $board_asrock;
    if($board_asrock) {
        $i = 0;
        foreach($fansensors as $id => $fan){
            if($i > 7) break;
            if ($fan['Type'] == 'Fan'){
                $name    = $fan['Name'];
                $tempid  = 'TEMP_'.$name;
                $temp    = $fansensors[$fancfg[$tempid]];
                $templo  = 'TEMPLO_'.$name;
                $temphi  = 'TEMPHI_'.$name;
                $fanmin  = 'FANMIN_'.$name;

                // hidden fan id
                echo '<input type="hidden" name="FAN_',$name,'" value="',$id,'"/>';

                // fan name: reading => temp name: reading
                echo '<dl><dt>',$name,' (',floatval($fan['Reading']),' ',$fan['Units'],'):</dt><dd><span class="fanctrl-basic">';
                if ($temp['Name'])
                    echo $temp['Name'],' ('.floatval($temp['Reading']),' ',$temp['Units'].'), ',
                    $fancfg[$templo],', ',$fancfg[$temphi],', ',$fancfg[$fanmin];
                else
                    echo 'Auto';
                echo '</span><span class="fanctrl-settings">&nbsp;</span>';

                // check if board.json exists then if fan name is in board.json
                $noconfig = '<font class="red"><b><i> (fan is not configured!)</i></b></font>';
                if($board_file_status){
                    if(!array_key_exists($name, $board_json[$board]['fans']))
                        echo $noconfig;
                } else {
                    echo $noconfig;
                }

                echo '</dd></dl>';

                // temperature sensor
                echo '<dl class="fanctrl-settings">',
                '<dt><dl><dd>Temperature sensor:</dd></dl></dt><dd>',
                '<select name="',$tempid,'" class="fanctrl-temp fanctrl-settings">',
                '<option value="0">Auto</option>',
                get_temp_options($fancfg[$tempid], true),
                '</select></dd></dl>';

                // low temperature threshold
                echo '<dl class="fanctrl-settings">',
                '<dt><dl><dd>Low temperature threshold (&deg;C):</dd></dl></dt>',
                '<dd><select name="',$templo,'" class="',$tempid,' fanctrl-settings">',
                get_temp_range('LO', $fancfg[$templo]),
                '</select></dd></dl>';

                // high temperature threshold
                echo '<dl class="fanctrl-settings">',
                '<dt><dl><dd>High temperature threshold (&deg;C):</dd></dl></dt>',
                '<dd><select name="',$temphi,'" class="',$tempid,' fanctrl-settings">',
                get_temp_range('HI', $fancfg[$temphi]),
                '</select></dd></dl>';

                // fan control minimum speed
                echo '<dl class="fanctrl-settings">',
                '<dt><dl><dd>Fan speed minimum (1-64):</dd></dl></dt><dd>',
                '<select name="',$fanmin,'" class="',$tempid,' fanctrl-settings">',
                get_min_options($fancfg[$fanmin]),
                '</select></dd></dl>&nbsp;';

                $i++;
            }
        }
    } elseif($board == 'Supermicro'){
            // temperature sensor
            echo '<dl>',
            '<dt>Temperature sensor:</dt><dd>',
            '<select name="TEMP_FAN">',
            '<option value="0">Auto</option>',
            get_temp_options($fancfg['TEMP_FAN'], true),
            '</select></dd></dl>';

            // low temperature threshold
            echo '<dl>',
            '<dt>Low temperature threshold (&deg;C):</dt>',
            '<dd><select name="TEMPLO_FAN">',
            get_temp_range('LO', $fancfg['TEMPLO_FAN']),
            '</select></dd></dl>';

            // high temperature threshold
            echo '<dl>',
            '<dt>High temperature threshold (&deg;C):</dt>',
            '<dd><select name="TEMPHI_FAN">',
            get_temp_range('HI', $fancfg['TEMPHI_FAN']),
            '</select></dd></dl>';

    } else {
        echo '<dl><dt>&nbsp;</dt><dd><p><b><font class="red">Your board is not currently supported</font></b></p></dd></dl>';
    }
}

/* get select options for temp & fan sensor types from fan ip*/
function get_temp_options($selected=null){
    global $fansensors, $fanip;
    $options = '';
    foreach($fansensors as $id => $sensor){
        if (($sensor['Type'] == 'Temperature') || ($sensor['Name'] == 'HDD Temperature')){
            $name = $sensor['Name'];
            $options .= "<option value='$id'";

            // set saved option as selected
            if ($selected == $id)
                $options .= ' selected';

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