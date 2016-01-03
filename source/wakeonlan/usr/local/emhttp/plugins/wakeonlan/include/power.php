<?php
$user = $_POST["user"];
$pass = $_POST["pass"];
$kodi = "curl -s --data-binary '{\"jsonrpc\": \"2.0\", \"method\": \"System.Suspend\", \"id\":1}' -H 'content-type: application/json;' http://$ipAddress/jsonrpc";
$windows = "net rpc shutdown -I $ipAddress -U $user%$pass";
$nix = "ssh -t $ipAddress 'sudo nohup &>/dev/null bash -c \"(sleep 1; echo -n mem >/sys/power/state) &\"'";
?>
