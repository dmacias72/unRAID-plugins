<?php
$filename = "/boot/config/plugins/speedtest/speedtest.xml";
$data = $_POST["data"];
file_put_contents($filename, $data);
?>
