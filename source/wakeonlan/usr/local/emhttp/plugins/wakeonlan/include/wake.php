<?
$cmd = "etherwake -b -i {$_POST['ifname']} {$_POST['mac']}";
exec($cmd);
?>