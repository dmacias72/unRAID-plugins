<?php
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_helpers.php';
echo json_encode(ipmi_sensors($ipmi_options));
?>