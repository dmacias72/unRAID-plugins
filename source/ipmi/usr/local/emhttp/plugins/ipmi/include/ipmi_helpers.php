<?php
/* ipmi tool variables*/
$ipmi_cfg_file = "/boot/config/plugins/ipmi/ipmi.cfg";
if (is_file($ipmi_cfg_file))
	$ipmi_cfg	= parse_ini_file($ipmi_cfg_file);
$ipmiseld		= isset($ipmi_cfg['IPMISELD']) ? $ipmi_cfg['IPMISELD']	: "disable";
$ipmipoll		= isset($ipmi_cfg['IPMIPOLL']) ? $ipmi_cfg['IPMIPOLL']	: "60";
$ipmifan			= isset($ipmi_cfg['IPMIFAN'])	 ? $ipmi_cfg['IPMIFAN'] 	: "disable";
$ipmi_network	= isset($ipmi_cfg['NETWORK'])	 ? $ipmi_cfg['NETWORK']		: "disable";
$ipmi_local		= isset($ipmi_cfg['LOCAL'])	 ? $ipmi_cfg['LOCAL']		: "disable";

//check running status
$ipmiseld_running = trim(shell_exec( "[ -f /proc/`cat /var/run/ipmiseld.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" ));
$ipmifan_running = trim(shell_exec( "[ -f /proc/`cat /var/run/ipmifan.pid 2> /dev/null`/exe ] && echo 1 || echo 0 2> /dev/null" ));
$ipmi_running = "<span class='green'>Running</span>";
$ipmi_stopped = "<span class='orange'>Stopped</span>";
$ipmiseld_status = ($ipmiseld_running) ? $ipmi_running : $ipmi_stopped;
$ipmifan_status = ($ipmifan_running) ? $ipmi_running : $ipmi_stopped;

// use save ip address or use local ipmi address
$ipmi_ipaddr = isset($ipmi_cfg['IPADDR']) ? $ipmi_cfg['IPADDR'] : '';
//$ipmi_ipaddr = preg_match('/^(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)(?:[.](?:25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)){3}$/', $ipmi_cfg['IPADDR']) ? 

$ipmi_disp_temp1 = isset($ipmi_cfg['DISP_TEMP1']) ? $ipmi_cfg['DISP_TEMP1'] : ""; // cpu temp display name
$ipmi_disp_temp2  = isset($ipmi_cfg['DISP_TEMP2'])  ? $ipmi_cfg['DISP_TEMP2']  : ""; // mb temp display name
$ipmi_disp_fan1 = isset($ipmi_cfg['DISP_FAN1']) ? $ipmi_cfg['DISP_FAN1'] : ""; // fan speed display name
$ipmi_disp_fan2 = isset($ipmi_cfg['DISP_FAN2']) ? $ipmi_cfg['DISP_FAN2'] : ""; // fan speed display name
$ipmi_user     = isset($ipmi_cfg['USER'])     ? $ipmi_cfg['USER']     : ""; // user for network access
$ipmi_password = isset($ipmi_cfg['PASSWORD']) ? $ipmi_cfg['PASSWORD'] : ""; // password for network access

// options for network access or not
$ipmi_options = ($ipmi_network == 'enable') ? "--always-prefix -h '$ipmi_ipaddr' -u $ipmi_user -p ".
	base64_decode($ipmi_password)." --session-timeout=5000 --retransmission-timeout=1000" : '';

// Get sensor info and check connection 
$ipmi_sensors = ipmi_sensors($ipmi_options);
$ipmi_fans = ipmi_get_fans($ipmi_sensors);
if($ipmi_network == 'enable'){
	$ipmi_conn = ($ipmi_sensors) ? "Connection successful" : "Connection failed";
}

$ipmi_board = "ipmi-fru | grep 'Board Manufacturer' | awk -F ':' '{print $2}'";

/* get an array of all sensors and their values */
function ipmi_sensors($options=null) {
	$cmd= "/usr/sbin/ipmi-sensors --output-sensor-thresholds --comma-separated-output --output-sensor-state --ignore-not-available-sensors --non-abbreviated-units --no-header-output --interpret-oem-data $options 2>/dev/null";
	exec($cmd, $output, $return);

	if ($return)
		return []; // return empty array if error

	// key names for ipmi sensors output
	$keys = ['ID','Name','Type','State','Reading','Units','LowerNR','LowerC','LowerNC','UpperNC','UpperC','UpperNR','Event'];
	$sensors = [];

	foreach($output as $line){

		$sensor_raw = explode(",", $line);
		// add sensor keys as keys to ipmi sensor output
		$size_raw = sizeof($sensor_raw);
		$sensor = ($size_raw < 13) ? []: array_combine($keys, array_slice($sensor_raw,0,13,true));
		  /*array_combine(array_slice($keys,0,$size_raw,true), $sensor_raw)*/

		if (empty($options)){
			$sensors[$sensor['ID']] = $sensor;
		}else{
			//split id into host and id
			$id = explode(':',$sensor['ID']);
			$sensor['IP'] = trim($id[0]);
			$sensor['ID'] = trim($id[1]);
			if ($sensor['IP'] == 'localhost')
				$sensor['IP'] = '127.0.0.1';

			// add sensor to array of sensors
			$sensors[ip2long($sensor['IP']).'-'.$sensor['ID']] = $sensor;
	}
}
	return $sensors;
}

/* get array of events and their values */
function ipmi_events($options=null){
	$cmd = "/usr/sbin/ipmi-sel --comma-separated-output --output-event-state --no-header-output --interpret-oem-data $options 2>/dev/null";
	exec($cmd, $output, $return); 

	//if ($return)
		//return []; // return empty array if error

	// key names for ipmi event output
	$keys = ['ID','DATE','Time','Name','Type','State','Event'];
	$events = [];

	foreach($output as $line){

		// add event keys as keys to ipmi event output
		$event_raw = explode(",", $line);
		$size_raw = sizeof($event_raw);
		$event = ($size_raw < 7) ? []: array_combine($keys, array_slice($event_raw,0,7,true));

		if (empty($options)){
			$events[$event['ID']] = $event;
		}else{
		//split id into host and id
		$id = explode(':',$event['ID']);
		$event['IP'] = trim($id[0]);
		$event['ID'] = trim($id[1]);
		if ($event['IP'] == 'localhost')
			$event['IP'] = '127.0.0.1';

		// add event to array of events
		$events[ip2long($event['IP']).'-'.$event['ID']] = $event;
	}
}
	return $events;
}

/* get select options for a given sensor type */
function ipmi_get_options($sensors, $type, $selected=null, $hdd=null){
	if ($hdd)
		$sensors[] = ['IP' => '', 'ID' => 'HDD', 'Name' => 'HDD Temperature', 'Type' => 'Temperature', 'State' => 'Nominal'];
	$options = "";
	foreach($sensors as $id => $sensor){
		if ($sensor["Type"] == $type && $sensor["State"] != "N/A"){ //ns
			$name = $sensor['Name'];
			$ip = (empty($sensor['IP'])) ? '' : " (${sensor['IP']})";
			$options .= "<option value='$id'";

			// set saved option as selected
			if ($selected == $id)
				$options .= " selected";

		$options .= ">$name$ip</option>";
		}
	}
	return $options;
}

// get options for high or low temp thresholds
function temp_get_options($range, $selected=null){
	$temps = [20,25,30,35,40,45,50,55,60,65,70,75,80];
	if ($range == 'HIGH')
	  rsort($temps);
 $options = "";
 foreach($temps as $temp){
			$options .= "<option value='$temp'";

			// set saved option as selected
			if ($selected == $temp)
				$options .= " selected";

		$options .= ">$temp</option>";

 	}
 	return $options;
	}

/* get reading for a given sensor by name */
function ipmi_get_readings($options=null) {
	$cmd = "/usr/sbin/ipmi-sensors --comma-separated-output --ignore-not-available-sensors --no-header-output --no-sensor-type-output --interpret-oem-data $options 2>/dev/null";
	exec($cmd, $output, $return);

	if ($return)
		return []; // return empty array if error

	// key names for ipmi sensors output
	$keys = ['ID', 'Name', 'Reading', 'Units', 'Event'];
	$sensors = [];

	foreach($output as $line){

		// add sensor keys as keys to ipmi sensor output
		$sensor_raw = explode(",", $line);
		$size_raw = sizeof($sensor_raw);
		$sensor = ($size_raw < 5) ? []: array_combine($keys, array_slice($sensor_raw,0,5,true));

		if (empty($options)){
			$sensors[$sensor['ID']] = $sensor;
		}else{
			//split id into host and id
			$id = explode(':',$sensor['ID']);
			$sensor['IP'] = trim($id[0]);
			$sensor['ID'] = trim($id[1]);
			if ($sensor['IP'] == 'localhost')
				$sensor['IP'] = '127.0.0.1';

			// add sensor to array of sensors
			$sensors[ip2long($sensor['IP']).'-'.$sensor['ID']] = $sensor;
	}
}
	return $sensors; // sensor readings
}

function ipmi_get_fans($sensors){
	foreach($sensors as $sensor){
		if ($sensor['Type'] == 'Fan' && $sensor['State'] != 'N/A')
			$fans[] = $sensor['Name']; 
	}
	return $fans;
}
?>