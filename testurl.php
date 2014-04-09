<?php
$status_code = 0;
$error_detail = "";
$elapsed_time = 0;

function status2name($status) {
    global $status_code;
    switch($status) {
        case 0: return '恢复正常';
        case 1: return '未检测到特征字符串';
        case 2: return '页面为空';
        case 3: return '连接超时';
        case 4: return 'HTTP状态码为'.($status_code ? $status_code : "不正常");
        case 5: return '无法连接';
        case 6: return '服务器ping不通';
        case 7: return 'DNS解析失败';
        default: return '未知内部错误';
    }
}

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function test_url($url, $includestr, $retry=0) {
    global $error_detail;
    $error_detail = ""; // initialize

    $ch_curl = curl_init();
    curl_setopt($ch_curl, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch_curl, CURLOPT_HEADER, false);
    curl_setopt($ch_curl, CURLOPT_RETURNTRANSFER, true );
    curl_setopt($ch_curl, CURLOPT_FAILONERROR, 1);
    curl_setopt($ch_curl, CURLOPT_URL, $url);
    curl_setopt($ch_curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
   
    $time_start = microtime_float();
    $page = curl_exec($ch_curl);
    $time_end = microtime_float();
    global $elapsed_time;
    $elapsed_time = $time_end - $time_start;

    if (!curl_errno($ch_curl)) {
        if ($includestr == '' || strstr($page, $includestr))
            return 0; // OK
        else if (strlen(trim($page)) == 0) {
            if ($retry < 2) // retry it
                return test_url($url, $includestr, $retry+1);
            else
                return 2; // empty page
        } else
            return 1; // STR not found
    } else {
        if ($retry < 2) // retry up to 3 times
            return test_url($url, $includestr, $retry+1);

        switch (curl_errno($ch_curl)) {
            case 22: // HTTP status code error
                global $status_code;
                $info = curl_getinfo($ch_curl);
                $status_code = $info['http_code'];
		        $error_detail = json_encode($info);
                return 4; 
            case 28: // CURLE_OPERATION_TIMEDOUT
                $real_url = curl_getinfo($ch_curl, CURLINFO_EFFECTIVE_URL);
                if (ping(getipfromurl($real_url)))
                    return 3; // Connection timeout
                else 
                    return 6; // ping failed
            case 6: // CURLE_COULDNT_RESOLVE_HOST
                $host = parse_url($url, PHP_URL_HOST); 
                $error_detail = shell_exec("dig +trace $host");
                return 7;
            case 7: // CURLE_COULDNT_CONNECT
                $real_url = curl_getinfo($ch_curl, CURLINFO_EFFECTIVE_URL);
                if (ping(getipfromurl($real_url)))
                    return 5; // cannot connect
                else 
                    return 6; // ping failed
            default:
		        $error_detail = "curl errno: ".curl_errno($ch_curl);
                return -1; // unknown error
        }
    }
}

function ping($host) {
    require_once("Net/Ping.php");
    $ping = Net_Ping::factory();
    $ping->setArgs(array('count' => 3, 'timeout' => 1));
    if (PEAR::isError($ping)) {
        global $error_detail;
        $error_detail = $ping->getMessage();
        return false;
    } else {
        $result = $ping->ping($host);
        return ($result->_transmitted > $result->_loss);
    }
}

function getipfromurl($url) {
    $domain = parse_url($url, PHP_URL_HOST);
    return gethostbyname($domain);
}
