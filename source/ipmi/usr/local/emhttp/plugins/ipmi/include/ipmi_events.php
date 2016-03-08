<?php
require_once '/usr/local/emhttp/plugins/ipmi/include/ipmi_helpers.php';
echo json_encode(ipmi_events($ipmi_options));
?>