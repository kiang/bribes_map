<?php

function cleanFile($file) {
    $fh = fopen($file, 'r');
    fgets($fh, 5);
    if (trim(fgets($fh, 5)) === '<!DO') {
        fclose($fh);
        $info = pathinfo($file);
        $data = array();
        $content = file_get_contents($file);
        $pos = strpos($content, 'PrintFJUD03_0.aspx');
        $posEnd = strpos($content, '"', $pos);
        $data['友善列印'] = 'http://jirs.judicial.gov.tw/FJUD/' . substr($content, $pos, $posEnd - $pos);
        $pos = strpos($content, 'HISTORYSELF.aspx');
        $posEnd = strpos($content, '\'', $pos);
        $data['歷審裁判'] = 'http://jirs.judicial.gov.tw/FJUD/' . substr($content, $pos, $posEnd - $pos);
        $pos = strpos($content, 'transPdf.aspx');
        $posEnd = strpos($content, '"', $pos);
        $data['匯出PDF'] = 'http://jirs.judicial.gov.tw/FJUD/' . substr($content, $pos, $posEnd - $pos);
        $lines = explode('</tr>', $content);
        foreach ($lines AS $lineNo => $line) {
            switch ($lineNo) {
                case 3:
                    $cols = explode('</td>', $line);
                    $data['裁判字號'] = trim(strip_tags($cols[1]));
                    break;
                case 4:
                    $cols = explode('</td>', $line);
                    $data['裁判日期'] = trim(strip_tags($cols[1]));
                    break;
                case 5:
                    $cols = explode('</td>', $line);
                    $data['裁判案由'] = trim(strip_tags($cols[1]));
                    break;
                case 7:
                    $data['裁判全文'] = trim(strip_tags($line));
                    break;
            }
        }
        $query = parse_url($data['友善列印'], PHP_URL_QUERY);
        parse_str($query, $ret);
        foreach ($ret AS $k => $v) {
            $data[$k] = $v;
        }
        $content = '';
        foreach ($data AS $k => $v) {
            $content .= "[*{$k}*]{$v}\n";
        }
        file_put_contents($file, $content);
    } else {
        fclose($fh);
    }
}