<?php
$filename = $_POST["filename"];
$content = $_POST["content"];
$file = fopen($filename, "w");
fwrite($file, $content);
fclose($file);
?>
