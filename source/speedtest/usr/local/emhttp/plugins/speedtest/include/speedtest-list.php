<?
$cmd = '/usr/sbin/speedtest --list 2>/dev/null';
$select  = $_GET['select'];
exec($cmd, $output);

$options = '';
$size = sizeof($output);
for ($i = 2; $i < $size; $i++) {
    $server = explode(') ', trim($output[$i]),2);
    $id = $server[0];

    $options .= '<option ';
    if ($id == $select)
        $options .= 'selected="" ';
    $options .= "value='$id'>".str_pad($id, 4, '0', STR_PAD_LEFT)." - ${server[1]}</option>";
}

echo json_encode($options);
?>
