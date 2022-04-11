<?php

/*
 * data from https://www.ptt.cc/bbs/Gossiping/M.1450410800.A.F6D.html
 */
$tmpPath = dirname(__DIR__) . '/tmp';
$rawPath = dirname(__DIR__) . '/raw';

//https://mapsengine.google.com/map/u/0/kml?forcekml=1&mid=zy_xY-hQlid4.kerVOcjUyb_o
$items = array(
    '嘉義' => 'z02Cr4oNY_4s.kobS0giNo5co',
    '雲林' => 'zIwEIkbEDqyE.kaWm5XMDTaGA',
    '台南' => 'zke0w-9NW6_w.kKeqGbdxZP-c',
    '彰化' => 'zy_xY-hQlid4.kerVOcjUyb_o',
    '花蓮' => 'z02Cr4oNY_4s.kBkzM2-LU-IM',
);

$fc = [
    'type' => 'FeatureCollection',
    'features' => [],
];
foreach ($items as $area => $item) {
    $rawFile = $rawPath . '/' . $item . '.xml';
    if (!file_exists($rawFile)) {
        file_put_contents($rawFile, file_get_contents('https://mapsengine.google.com/map/u/0/kml?forcekml=1&mid=' . $item));
    }
    $xml = simplexml_load_file($rawFile, null, LIBXML_NOCDATA);
    foreach ($xml->Document->Folder as $folder) {
        foreach ($folder->Placemark as $placemark) {
            $title = (string) $placemark->name;
            if (strpos($title, '點') !== 0) {
                $location = $area;
                $year = '';
                $yearPos = strpos($title, '年');
                if (false !== $yearPos) {
                    if (preg_match('/[0-9][0-9]+/', $title, $matches)) {
                        if (strlen($matches[0]) === 4) {
                            $year = $matches[0] - 1911;
                        } else {
                            $year = $matches[0];
                        }
                    }
                }
                if (substr($title, 0, 1) === '(') {
                    $posEnd = strpos($title, ')');
                    if (false !== $posEnd) {
                        $location = substr($title, 1, $posEnd - 1);
                        $title = substr($title, $posEnd + 1);
                    }
                    if (!empty($year)) {
                        $yearPos = strpos($title, '年');
                        $title = substr($title, $yearPos + 3);
                    }
                } elseif (!empty($year)) {
                    $numPos = strpos($title, $year);
                    if ($numPos !== 0) {
                        $location = substr($title, 0, $numPos);
                        $title = substr($title, $yearPos + 3);
                    }
                }
                $coordinates = explode(',', (string) $placemark->Point->coordinates);
                $fc['features'][] = [
                    'type' => 'Feature',
                    'properties' => [
                        'area' => $area,
                        'location' => $location,
                        'year' => $year,
                        'title' => $title,
                        'description' => (string) $placemark->description,
                    ],
                    'geometry' => [
                        'type' => 'Point',
                        'coordinates' => [
                            floatval($coordinates[0]),
                            floatval($coordinates[1])
                        ],
                    ],
                ];
            }
        }
    }
}
file_put_contents(dirname(__DIR__) . '/json/points.json', json_encode($fc, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
