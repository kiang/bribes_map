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
);

foreach ($items AS $area => $item) {
    $rawFile = $rawPath . '/' . $item . '.xml';
    if (!file_exists($rawFile)) {
        file_put_contents($rawFile, file_get_contents('https://mapsengine.google.com/map/u/0/kml?forcekml=1&mid=' . $item));
    }
}