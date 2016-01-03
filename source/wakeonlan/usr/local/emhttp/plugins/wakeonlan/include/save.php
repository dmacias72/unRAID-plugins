<?php
$filename =  "/boot/config/plugins/wakeonlan/wakeonlan.xml";
$data = $_POST["data"];
file_put_contents($filename, $data);
?>
