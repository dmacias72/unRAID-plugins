<?
// determine the number of tabs for scheduler
$scheduler_tab = (($var['shareUser']!='-')&&(isset($disks['cache']))&&($disks['cache']['status']!='DISK_NP')&&($var['shareCacheEnabled']=='yes')) ? 1 : 0;
exec("find /usr/local/emhttp/* -type f -maxdepth 2 | xargs grep 'Menu=\"Scheduler' -sl | xargs -L 1 basename", sort($output));
sort($output);
$scheduler_tab = $scheduler_tab + array_search('SpeedtestSchedule.page', $output);
?>