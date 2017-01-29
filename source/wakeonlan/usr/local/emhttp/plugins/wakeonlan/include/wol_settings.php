<?
$net_cfg   = parse_ini_file('/boot/config/network.cfg');
$excludeip = is_array($net_cfg['IPADDR']) ? $net_cfg['IPADDR'][0] : $net_cfg['IPADDR'];
$ifname    = (filter_var($excludeip, FILTER_VALIDATE_IP)) ?
    exec("ifconfig | awk 'BEGIN { ifname = 0 } /".escapeshellarg($excludeip)."/ { ifname = 1 } /^($|[^\t])/ { if(ifname) print buffer; buffer = $1; ifname = 0 } END { if(ifname) print buffer }' |  sed s/\:/\/g"): 
    'eth0';
?>
