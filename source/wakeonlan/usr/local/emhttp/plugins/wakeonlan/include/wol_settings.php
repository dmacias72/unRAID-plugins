<?
if ($var['version']  < 6.2)
    $excludeip = $var['IPADDR'];
else
    $excludeip = empty($eth0['IPADDR:0']) ? $eth1['IPADDR:0'] : $eth0['IPADDR:0'];

$ifname    = (filter_var($excludeip, FILTER_VALIDATE_IP)) ?
    exec("ifconfig | awk 'BEGIN { ifname = 0 } /".escapeshellarg($excludeip)."/ { ifname = 1 } /^($|[^\t])/ { if(ifname) print buffer; buffer = $1; ifname = 0 } END { if(ifname) print buffer }' |  sed s/\:/\/g"):
    'eth0';
?>
