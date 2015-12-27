<?php

$fh = fopen(__DIR__ . '/list.csv', 'w');
fputcsv($fh, array(
    '姓名',
    '選區',
    '政黨',
));
$json = json_decode(file_get_contents(__DIR__ . '/2.json'), true);
foreach ($json['全國不分區及僑居國外國民立委公報'] AS $c) {
    fputcsv($fh, array(
        $c['candidatename'],
        '不分區',
        $c['recpartyname'],
    ));
}

$json = json_decode(file_get_contents(__DIR__ . '/3.json'), true);
foreach ($json['區域立委公報'] AS $c) {
    fputcsv($fh, array(
        $c['candidatename'],
        "{$c['cityname']}{$c['sessionname']}",
        $c['recpartyname_1'],
    ));
}