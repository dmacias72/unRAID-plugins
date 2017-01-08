<?
$file = '/boot/config/plugins/speedtest/speedtest.xml';
if (file_exists($file)) {
    if ($_POST['id'] == 'all'){
        $xml = new SimpleXMLElement("<tests></tests>");
        $xml->asXML($file);
    }else {
        $name = $_POST['id'];
        $xml = simplexml_load_file($file);
        $node = $xml->xpath("test[@name=$name]")[0];
        unset($node[0]);
        $xml->asXML($file);
    }
}
?>
