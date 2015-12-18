<?php

$json = json_decode(file_get_contents(dirname(__DIR__) . '/points.json'), true);
$data = array();

foreach ($json AS $point) {
    if (!isset($data[$point['area']])) {
        $data[$point['area']] = array();
    }
    $data[$point['area']][] = $point;
}
$xml = new DOMDocument('1.0', 'utf-8');
$kml = $xml->createElementNS('http://www.opengis.net/kml/2.2', 'kml');
$doc = $xml->createElement('Document');
$name = $xml->createElement('name', '台灣賄選實價登錄');
$desc = $xml->createElement('description');
$desc->appendChild($xml->createCDATASection('這是程式自動產生，請依據產生的欄位填寫資料'));
$doc->appendChild($name);
$doc->appendChild($desc);
foreach ($data AS $area => $points) {
    $folder = $xml->createElement('Folder');
    $name = $xml->createElement('name', $area);
    $folder->appendChild($name);
    foreach ($points AS $point) {
        $placemark = $xml->createElement('Placemark');
        $placemark->appendChild($xml->createElement('name', $point['title']));
        $lines = array();
        /*
         * id in http://k.olc.tw/elections/
         */
        $lines[] = '[id]';
        /*
         * prepared for using generator of http://judicial.ronny.tw/
         */
        $lines[] = '[case]';
        foreach ($point AS $k => $v) {
            if ($k !== 'longitude' && $k !== 'latitude') {
                $lines[] = "[{$k}]{$v}";
            }
        }
        $cdata = implode('<br>', $lines);
        $desc = $xml->createElement('description');
        $desc->appendChild($xml->createCDATASection(implode('<br>', $lines)));
        $placemark->appendChild($desc);
        $p = $xml->createElement('Point');
        $c = $xml->createElement('coordinates', implode(',', array(
            $point['longitude'], $point['latitude'], '0.0'
        )));
        $p->appendChild($c);
        $placemark->appendChild($p);
        $folder->appendChild($placemark);
    }
    $doc->appendChild($folder);
}
$kml->appendChild($doc);
$xml->appendChild($kml);
$xml->save(__DIR__ . '/export.kml');