<?
// determine the number of tabs for scheduler
$scheduler_tab = (($var['shareUser']!='-')&&(isset($disks['cache']))&&($disks['cache']['status']!='DISK_NP')&&($var['shareCacheEnabled']=='yes')) ? 3 : 2;
if(file_exists('/boot/config/plugins/dynamix.schedules.plg'))
   $scheduler_tab++;
if(file_exists('/boot/config/plugins/dynamix.smart.drivedb.plg'))
   $scheduler_tab++;
?>