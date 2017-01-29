<?
$base = '/boot/config/plugins/apache/httpd/';
$file = realpath($_GET['editfile']);
$editfile = 'Invalid File';

if(!strpos($file, $base) && file_exists($file))
    $editfile = file_get_contents($file);
echo json_encode($editfile);
?>