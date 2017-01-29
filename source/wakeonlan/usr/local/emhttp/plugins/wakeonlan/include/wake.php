<?
$ifname = escapeshellarg($_POST['ifname']);
$mac    = escapeshellarg($_POST['mac']);
$cmd = "etherwake -i $ifname $mac";
shell_exec($cmd);
?>
