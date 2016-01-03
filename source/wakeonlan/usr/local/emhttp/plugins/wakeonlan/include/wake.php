<?php
$command = "etherwake -i ".$_POST["ifname"]." ".$_POST["mac"];
exec($command);
?>
