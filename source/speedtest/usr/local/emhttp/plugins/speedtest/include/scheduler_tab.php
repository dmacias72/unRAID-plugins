<?

// check for cache disk
$cache_tab  = (($var['shareUser']!='-')&&(isset($disks['cache']))&&($disks['cache']['status']!='DISK_NP')&&($var['shareCacheEnabled']=='yes')) ? 1 : 0;

// check for parity disk
$parity_tab = ($disks['parity']['status']!='DISK_NP_DSBL') ? 1 : 0;

// get an array of all page files for Scheduler tab minus parity and mover pages
$schedule_cmd = "find /usr/local/emhttp/* -type f \( -name '*.page' ! -name 'ParityCheck.page' ! -name 'MoverSettings.page' \) -maxdepth 2 | xargs grep 'Menu=\"Scheduler' -sl | xargs -L 1 basename";
exec($schedule_cmd, $output);
sort($output);

// determine the position of the speedtest schedule tab relative to other plugins
$speedtest_tab = array_search('SpeedtestSchedule.page', $output) + 1;

// determine the tab posistion for speedtest schedule tab
$scheduler_tab = $cache_tab + $parity_tab + $speedtest_tab;
?>