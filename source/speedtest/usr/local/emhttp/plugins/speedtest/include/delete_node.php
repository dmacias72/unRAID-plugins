<?
$file = '/boot/config/plugins/speedtest/speedtest.xml';
if (file_exists($file)) {
    $id = htmlspecialchars($_POST['id']);
    if ($id === 'all'){
        $xml = new SimpleXMLElement('<tests></tests>');
        $xml->asXML($file);
    }else {
        $xml = simplexml_load_file($file);
        $node = $xml->xpath("test[@name=$id]")[0];
        unset($node[0]);
        $xml->asXML($file);
    }
}
?>
