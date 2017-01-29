<?
$log = '/var/log/wakeonlan/scan.xml';
if (file_exists($log))
    $xml = new SimpleXMLElement('<nmaprun></nmaprun>');
    $xml->asXML($log);
?>
