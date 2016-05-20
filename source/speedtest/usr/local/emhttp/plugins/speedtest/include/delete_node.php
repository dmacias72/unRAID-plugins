<?
$file = '/boot/config/plugins/speedtest/speedtest.xml';
if (file_exists($file)) {
    if ($_GET['id'] == 'all'){
        $xml = new SimpleXMLElement("<tests></tests>");
        $xml->asXML($file);
    }else {
        $name = $_GET['id'];
        $xml = simplexml_load_file($file);
        unset($xml->xpath("test[@name=$name]")[0]->{0});
        $xml->asXML($file);
    }
}
?>
