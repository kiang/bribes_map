<?php

require_once __DIR__ . '/libs.php';
/*
 * from https://gist.github.com/ronnywang/a652451c3687a9f1cf0d
 */
date_default_timezone_set('Asia/Taipei');
$courts = array(
    /*
      'TPC' => '司法院－刑事補償',
      'TPU' => '司法院－訴願決定',
      'TPJ' => '司法院職務法庭',
      'TPA' => '最高行政法院',
      'TPP' => '公務員懲戒委員會',
      'IPC' => '智慧財產法院',
      'PTE' => '屏東簡易庭',
      'KSY' => '臺灣高雄少年及家事法院',
      'TPE' => '臺北簡易庭',
      'STE' => '新店簡易庭',
      'SLE' => '士林簡易庭',
      'NHE' => '內湖簡易庭',
      'PCE' => '板橋簡易庭',
      'SJE' => '三重簡易庭',
      'TYE' => '桃園簡易庭',
      'CLE' => '中壢簡易庭',
      'SCD' => '新竹簡易庭',
      'CPE' => '竹北簡易庭(含竹東)',
      'MLD' => '苗栗簡易庭',
      'TCE' => '臺中簡易庭',
      'SDE' => '沙鹿簡易庭',
      'FYE' => '豐原簡易庭',
      'CHE' => '彰化簡易庭',
      'OLE' => '員林簡易庭',
      'PDE' => '北斗簡易庭',
      'NTE' => '南投簡易庭(含埔里)',
      'TLE' => '斗六簡易庭',
      'HUE' => '虎尾簡易庭',
      'CYE' => '嘉義簡易庭(含朴子)',
      'PKE' => '北港簡易庭',
      'TNE' => '臺南簡易庭',
      'SYE' => '柳營簡易庭',
      'SSE' => '新市簡易庭',
      'KSE' => '高雄簡易庭',
      'GSE' => '岡山簡易庭',
      'CSE' => '旗山簡易庭',
      'FSE' => '鳳山簡易庭',
      'CCE' => '潮州簡易庭',
      'TTE' => '臺東簡易庭',
      'HLE' => '花蓮簡易庭(含鳳林,玉里)',
      'ILE' => '宜蘭簡易庭',
      'LTE' => '羅東簡易庭',
      'KLD' => '基隆、瑞芳簡易庭',
      'MKE' => '馬公簡易庭',
      'KME' => '金城簡易庭',
     * 
     */
    'TPS' => '最高法院',
    'TPH' => '臺灣高等法院',
    'TPB' => '臺北高等行政法院',
    'TCB' => '臺中高等行政法院',
    'KSB' => '高雄高等行政法院',
    'TCH' => '臺灣高等法院 臺中分院',
    'TNH' => '臺灣高等法院 臺南分院',
    'KSH' => '臺灣高等法院 高雄分院',
    'HLH' => '臺灣高等法院 花蓮分院',
    'KMH' => '福建高等法院 金門分院',
    'TPD' => '臺灣臺北地方法院',
    'SLD' => '臺灣士林地方法院',
    'PCD' => '臺灣新北地方法院',
    'ILD' => '臺灣宜蘭地方法院',
    'KLD' => '臺灣基隆地方法院',
    'TYD' => '臺灣桃園地方法院',
    'SCD' => '臺灣新竹地方法院',
    'MLD' => '臺灣苗栗地方法院',
    'TCD' => '臺灣臺中地方法院',
    'CHD' => '臺灣彰化地方法院',
    'NTD' => '臺灣南投地方法院',
    'ULD' => '臺灣雲林地方法院',
    'CYD' => '臺灣嘉義地方法院',
    'TND' => '臺灣臺南地方法院',
    'HLD' => '臺灣花蓮地方法院',
    'PHD' => '臺灣澎湖地方法院',
    'KMD' => '福建金門地方法院',
    'LCD' => '福建連江地方法院',
);

$tmpPath = dirname(__DIR__) . '/tmp/2016';
if (!file_exists($tmpPath)) {
    mkdir($tmpPath, 0777);
}

$fh = fopen(__DIR__ . '/list.csv', 'r');
$lineCount = 0;
fgetcsv($fh, 2048);
$blockCount = 0;
$proxy = 'proxy.hinet.net:80';
while ($line = fgetcsv($fh, 2048)) {
    ++$lineCount;
    $cachePath = $tmpPath . '/' . $line[0] . '_cache';
    if (!file_exists($cachePath)) {
        mkdir($cachePath);
    }
    $keyword = $line[0];
    foreach ($courts as $court_id => $court) {
        $url = "http://jirs.judicial.gov.tw/FJUD/FJUDQRY02_1.aspx";
        if ($court_id[2] == 'E') {
            $param = "sel_v_court={$court_id}+" . urlencode($court) . "&v_court={$court_id}+" . urlencode($court) . "&v_sys=M&jud_year=&jud_case=&jud_no=&jud_title=&jt=&keyword=" . urlencode($keyword) . "&sdate=&edate=&page=&searchkw=&sel_judword=" . urlencode('常用字別');
        } else {
            $param = "v_court={$court_id}+" . urlencode($court) . "&v_sys=M&jud_year=&jud_case=&jud_no=&jud_title=&jt=&keyword=" . urlencode($keyword) . "&sdate=&edate=&page=&searchkw=";
        }
        $urlDecoded = urldecode($url . '?' . $param);
        $md5 = md5($urlDecoded);
        $cachedFile = $cachePath . '/list_' . $md5;
        if (!file_exists($cachedFile)) {
            error_log($urlDecoded . " ( {$lineCount} / 535 )");
            $listFetched = false;
            while (false === $listFetched) {
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_REFERER, $url);
                curl_setopt($curl, CURLOPT_PROXY, $proxy);
                curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $param);
                curl_setopt($curl, CURLOPT_COOKIESESSION, true);
                curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36');
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HEADER, 1);
                $response = curl_exec($curl);
                $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
                $header = substr($response, 0, $header_size);
                $content = substr($response, $header_size);
                if (empty($header) || false !== strpos($content, 'Object moved')) {
                    if (++$blockCount >= 5) {
                        error_log("blocked more than 5 times, die");
                        exit();
                    } else {
                        error_log("block detected in list! sleep for 2 sec.");
                        sleep(2);
                    }
                } else {
                    $blockCount = 0;
                    echo $header;
                    file_put_contents($cachedFile, $content);
                    $listFetched = true;
                }
            }
        } else {
            $content = file_get_contents($cachedFile);
        }

        //curl_close($curl);
        if (!preg_match('#(\d+)\s+筆 / 每頁\s+20\s+筆 / 共\s+\d+\s+頁 / 現在第#m', $content, $matches)) {
            //print_r($content);
            continue;
            throw new Exception('test');
        }
        $count = $matches[1];
        if(preg_match('#本次查詢結果共([0-9]*)筆#', $content, $matches)) {
            $count = $matches[1];
        }
        if (!preg_match('#FJUDQRY03_1\.aspx\?id=[0-9]*&([^"]*)#', $content, $matches)) {
            continue;
            var_dump($content);
            throw new Exception('test2');
        }
        $param = $matches[1];
        for ($j = 1; $j <= $count; $j ++) {
            $caseFetched = false;
            while (false === $caseFetched) {
                $case_url = "http://jirs.judicial.gov.tw/FJUD/FJUDQRY03_1.aspx";
                $urlDecoded = urldecode($case_url . "?id={$j}&{$param}");
                $md5 = md5($urlDecoded);
                $cachedFile = $cachePath . '/case_' . $md5;
                if (!file_exists($cachedFile)) {
                    $curl = curl_init($case_url);
                    error_log("{$j}/{$count} / {$keyword} ( {$lineCount} / 535 )");
                    curl_setopt($curl, CURLOPT_PROXY, $proxy);
                    curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
                    curl_setopt($curl, CURLOPT_VERBOSE, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, "id={$j}&{$param}");
                    curl_setopt($curl, CURLOPT_URL, $case_url);
                    curl_setopt($curl, CURLOPT_REFERER, 'http://jirs.judicial.gov.tw/FJUD/FJUDQRY02_1.aspx');
                    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HEADER, 1);
                    $response = curl_exec($curl);
                    $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
                    $header = substr($response, 0, $header_size);
                    $content = substr($response, $header_size);
                    if (empty($header) || false !== strpos($content, 'Object moved') || false !== strpos($header, 'Service Unavailable')) {
                        if (++$blockCount >= 5) {
                            error_log("blocked more than 5 times, die");
                            exit();
                        } else {
                            error_log("block detected in case! sleep for 2 sec.");
                            sleep(2);
                        }
                    } else {
                        $blockCount = 0;
                        echo $header;
                        $caseFetched = true;
                        file_put_contents($cachedFile, $content);

                        if (!preg_match('#href="([^"]*)">友善列印#', $content, $matches)) {
                            //do nothing
                        } else {
                            $print_url = $matches[1];
                            $query = parse_url($print_url, PHP_URL_QUERY);
                            parse_str($query, $ret);
                            $court = explode(' ', $ret['v_court'])[0];
                            $path = __DIR__ . "/raw/{$line[0]}";
                            if (!file_exists($path)) {
                                mkdir($path, 0777, true);
                            }
                            $caseFile = $path . "/{$court}-{$ret['v_sys']}-{$ret['jyear']}-{$ret['jcase']}-{$ret['jno']}-{$ret['jcheck']}.txt";
                            file_put_contents($caseFile, $content);
                            cleanFile($caseFile);
                        }
                    }
                } else {
                    $caseFetched = true;
                    $content = file_get_contents($cachedFile);

                    if (!preg_match('#href="([^"]*)">友善列印#', $content, $matches)) {
                        //do nothing
                    } else {
                        $print_url = $matches[1];
                        $query = parse_url($print_url, PHP_URL_QUERY);
                        parse_str($query, $ret);
                        $court = explode(' ', $ret['v_court'])[0];
                        $path = __DIR__ . "/raw/{$line[0]}";
                        if (!file_exists($path)) {
                            mkdir($path, 0777, true);
                        }
                        $caseFile = $path . "/{$court}-{$ret['v_sys']}-{$ret['jyear']}-{$ret['jcase']}-{$ret['jno']}-{$ret['jcheck']}.txt";
                        file_put_contents($caseFile, $content);
                        cleanFile($caseFile);
                    }
                }
            }
        }
    }
}
