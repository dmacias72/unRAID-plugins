<?
$file = $_GET['editfile'];
if(file_exists($file))
    $editfile = file_get_contents($file);
echo json_encode($editfile);
?>