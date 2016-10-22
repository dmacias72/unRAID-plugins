<?
$cmd = "etherwake -i {$_POST['ifname']} {$_POST['mac']}";
exec($cmd);
?>
