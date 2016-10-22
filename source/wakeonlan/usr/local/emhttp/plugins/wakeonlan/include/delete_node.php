<?
$file = '/boot/config/plugins/wakeonlan/wakeonlan.xml';

if (file_exists($file)) {
    if ($_POST['mac'] == 'all'){
        $xml = new SimpleXMLElement('<?xml version="1.0"?>'.
        '<?xml-stylesheet href="file:///usr/bin/../share/nmap/nmap.xsl" type="text/xsl"?>'.
        '<hosts/>');
        $xml->asXML($file);
    }else {
        $xml = simplexml_load_file($file);
        unset($xml->xpath("//*/address[@addr='{$_POST["mac"]}']/parent::*")[0][0]);
        $xml->asXML($file);
    }
}
?>
