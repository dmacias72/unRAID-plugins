<?php
//ipmi-sensors-config --filename=ipmi.config --checkout
//ipmi-sensors-config --filename=ipmi.config --commit
/* get ipmi config and network options */
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_options.php';

/* scan directory for type */
function scan_dir($dir, $type = ""){
  $out = array();
  foreach (array_slice(scandir($dir), 2) as $entry){
    $sep   = (preg_match("/\/$/", $dir)) ? "" : "/";
    $out[] = $dir.$sep.$entry ;
  }
  return $out;
}

/* get highest temp of hard drives */
function get_highest_temp($hdds){
  $highest_temp="0";
  foreach ($hdds as $hdd) {
    if (shell_exec("hdparm -C ${hdd} 2>/dev/null| grep -c standby") == 0){
      $temp = preg_replace("/\s+/", "", shell_exec("smartctl -A ${hdd} 2>/dev/null| grep -m 1 -i Temperature_Celsius | awk '{print $10}'"));
      $highest_temp = ($temp > $highest_temp) ? $temp : $highest_temp;
    }
  } 
  return $highest_temp;
}

/* get all hard drives except flash drive */
function get_all_hdds(){
  $hdds = array();
  $flash = preg_replace("/\d$/", "", realpath("/dev/disk/by-label/UNRAID"));
  foreach (scan_dir("/dev/") as $dev) {
    if(preg_match("/[sh]d[a-z]+$/", $dev) && $dev != $flash) {
      $hdds[] = $dev;
    }
  }
  return $hdds;
}

/* get an array of all sensors and their values */
function ipmi_sensors($options=null) {
	$cmd= "/usr/sbin/ipmi-sensors --output-sensor-thresholds --comma-separated-output --output-sensor-state --non-abbreviated-units --no-header-output --interpret-oem-data $options 2>/dev/null";
	exec($cmd, $output, $return);

	if ($return)
		return []; // return empty array if error

	/* get highest hard drive temp and add sensor */
	$hdds =  get_all_hdds();
	$hdd_temp = get_highest_temp($hdds);
	$output[] = "99,HDD Temperature,Temperature,Nominal,$hdd_temp,degrees C,N/A,N/A,N/A,N/A,N/A,N/A,Ok";

	// key names for ipmi sensors output
	$keys = ['ID','Name','Type','State','Reading','Units','LowerNR','LowerC','LowerNC','UpperNC','UpperC','UpperNR','Event'];
	$sensors = [];

	foreach($output as $line){

		$sensor_raw = explode(",", $line);
		$size_raw = sizeof($sensor_raw);

		// add sensor keys as keys to ipmi sensor output
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
			$sensors[ip2long($sensor['IP']).'_'.$sensor['ID']] = $sensor;
		}
	}
	return $sensors;
}

/* get array of events and their values */
function ipmi_events($options=null, $archive=null){
	if($archive) {
		$filename = "/boot/config/plugins/ipmi/archived_events.log";
		$output = file($filename, FILE_IGNORE_NEW_LINES);
	} else {
		$cmd = "/usr/sbin/ipmi-sel --comma-separated-output --output-event-state --no-header-output --interpret-oem-data $options 2>/dev/null";
		exec($cmd, $output, $return); 
	}
	//if ($return)
		//return []; // return empty array if error

	// key names for ipmi event output
	$keys = ['ID','Date','Time','Name','Type','State','Event'];
	$events = [];

	foreach($output as $line){

		$event_raw = explode(",", $line);
		$size_raw = sizeof($event_raw);

		// add event keys as keys to ipmi event output
		$event = ($size_raw < 7) ? []: array_combine($keys, array_slice($event_raw,0,7,true));

		// put time in sortable format and add unix timestamp
		$timestamp = $event['Date']." ".$event['Time'];
		if(strtotime($timestamp)) {
			if($date = Datetime::createFromFormat('M-d-Y H:i:s', $timestamp)) {
				$event['Date'] = $date->format('Y-m-d H:i:s');
				$event['Time'] = $date->format('U');
			}
		}

		if (empty($options)){

			if($archive)
				$events[$event['Time']."-".$event['ID']] = $event;
			else
				$events[$event['ID']] = $event;

		}else{

			//split id into host and id
			$id = explode(':',$event['ID']);
			$event['IP'] = trim($id[0]);
			if($archive)
				$event['ID'] = $event['Time'];
			else
				$event['ID'] = trim($id[1]);
			if ($event['IP'] == 'localhost')
				$event['IP'] = '127.0.0.1';

			// add event to array of events
			$events[ip2long($event['IP']).'_'.$event['ID']] = $event;
		}
	}
	return $events;
}

/* get select options for a given sensor type */
function ipmi_get_options($sensors, $type, $selected=null, $hdd=null){
	if ($hdd)	// add hard drive temp as option
		$sensors['99'] = ['IP' => '', 'ID' => '99', 'Name' => 'HDD Temperature', 'Type' => 'Temperature', 'State' => 'Nominal'];

	$options = "";
	foreach($sensors as $id => $sensor){
		if ($sensor["Type"] == $type){
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
	if ($range == 'HI')
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
	$cmd = "/usr/sbin/ipmi-sensors --comma-separated-output --no-header-output --no-sensor-type-output --interpret-oem-data $options 2>/dev/null";
	exec($cmd, $output, $return);

	if ($return)
		return []; // return empty array if error

	/* get highest hard drive temp and add sensor */
	$hdds =  get_all_hdds();
	$hdd_temp = get_highest_temp($hdds);
	$output[] = "99,HDD Temperature,$hdd_temp,C,Ok";

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
	foreach($sensors as $key => $sensor){
		if ($sensor['Type'] == 'Fan')
			$fans[] = $key; 
	}
	return $fans;
}

function ipmi_get_temps($sensors){
	foreach($sensors as $key => $sensor){
		if ($sensor['Type'] == 'Temperature')
			$temps[] = $key; 
	}
	return $temps;
}
?>