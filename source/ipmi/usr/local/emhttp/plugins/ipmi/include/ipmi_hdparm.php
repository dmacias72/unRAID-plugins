<?php
/* debug */
function debug($m){
  global $prog, $DEBUG;
  if($DEBUG){
    $STDERR = fopen('php://stderr', 'w+');
    fwrite($STDERR, $m."\n");
    fclose($STDERR);
  }
}

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
  debug("Highest temp is ${highest_temp}ºC");
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
?>