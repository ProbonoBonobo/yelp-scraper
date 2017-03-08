<?php
/**
 * Created by PhpStorm.
 * User: kevinzeidler
 * Date: 5/14/16
 * Time: 7:07 PM
 */

error_reporting(E_ERROR | E_PARSE);
$url = "http://sandiego.craigslist.org/esd/web/5727179855.html";
$attempts= 0;
$GLOBALS['msg'] = "";
$goodEnough = false;
$content = "";
date_default_timezone_set("America/Los_Angeles");
$date = date_create();
echo date_format($date, 'Y-m-d H:i:s') . "\n";

//echo"\n\nNow fetching the updated list of proxy servers...\n";
//system('php extractProxyIPs.php');
echo"\nSuccessful. Now attempting to fetch the target HTML";


function getUrl($url,$curl_sess, $proxy_ip) {
    $header = array();
	    
   $header[0] = "REMOTE_ADDR: $proxy_ip";
   $header[] = "HTTP_X_FORWARDED_FOR: $proxy_ip";
   $header[] = "Accept: text/xml,application/xml,application/xhtml+xml, text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
    $header[] = "Cache-Control: max-age=0";
    $header[] = "Connection: keep-alive";
    $header[] = "Keep-Alive: 300";
    $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
    $header[] = "Accept-Language: en-us,en;q=0.5";

    curl_setopt($curl_sess, CURLOPT_URL, $url);
    curl_setopt($curl_sess, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; U; Linux x86_64; en-US) AppleWebKit/534.3 (KHTML, like Gecko) Ubuntu/10.04 Chromium/6.0.472.53 Chrome/6.0.472.53 Safari/534.3');
    curl_setopt($curl_sess, CURLOPT_HTTPHEADER, $header);
    curl_setopt($curl_sess, CURLOPT_ENCODING, 'gzip,deflate');
    curl_setopt($curl_sess, CURLOPT_PROXY, $proxy_ip);
    curl_setopt($curl_sess, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($curl_sess, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl_sess, CURLOPT_LOW_SPEED_LIMIT, 5);
    curl_setopt($curl_sess, CURLOPT_LOW_SPEED_TIME, 5);
    curl_setopt($curl_sess, CURLOPT_RETURNTRANSFER, true); // very important to set it to true, otherwise the content will be not be saved to string
    $date = date_create();
    $openedAt = date_format($date, 'Y-m-d H:i:s');
    $GLOBALS['msg'] .= "Opening connection to " . $proxy_ip . " at " . $openedAt . "...\n";
    $html = curl_exec($curl_sess); // execute theii curl command
    curl_close($curl_sess);
    $date = date_create();
    $closedAt = date_format($date, 'Y-m-d H:i:s');
   $GLOBALS['msg'] .= "Proxy returned a response of length " . (!empty($html) ? strlen($html) : 0) . ".\n";
    $GLOBALS['msg'] .= "Closing connection to " . $proxy_ip . " at " . $closedAt . "...\n";
    return $html;
}

function isCaptchaRoadblock($response) {
    $patt = "id=\"challenge-form\"";
    if (preg_match($patt, $response)) {
         return true;
    }
    return false;
}

function curl($URLServer,$proxyRetry, $postdata="", $cookieFile=null, $proxy=true)
{
    global $proxyCache;
    $agent = "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.12) Gecko/20101027 Ubuntu/10.10 (maverick) Firefox/3.6.12";
    $cURL_Session = curl_init();

//    curl_setopt($cURL_Session, CURLOPT_URL, $URLServer);
//    curl_setopt($cURL_Session, CURLOPT_USERAGENT, $agent);
//    if ($postdata != "") {
//        curl_setopt($cURL_Session, CURLOPT_POST, 1);
//        curl_setopt($cURL_Session, CURLOPT_POSTFIELDS, $postdata);
//    }
//    curl_setopt($cURL_Session, CURLOPT_RETURNTRANSFER, 1);
//    //curl_setopt($cURL_Session, CURLOPT_FOLLOWLOCATION, 1);
//    if ($cookieFile != null) {
//        curl_setopt($cURL_Session, CURLOPT_COOKIEJAR, $cookieFile);
//        curl_setopt($cURL_Session, CURLOPT_COOKIEFILE, $cookieFile);
//    }


    if ($proxy == true) {
        if ($proxyCache == "") {
//            $c = curl("free-proxy-list.net", "", null, false);
//            preg_match_all("/([0-9]*).([0-9]*).([0-9]*).([0-9]*):([0-9]*)/", $c, $matches);
//            $matches = $matches[0];
            $proxyCache = file_get_contents('/var/www/scraper/sandbox/cached/proxies.txt');
            $proxyCache = explode(",", $proxyCache);
//            foreach ($proxyCache as $proxyIP) {
//                echo $proxyIP;
//            }
        }
        $proxyCount = count($proxyCache);
        $proxyChosen = $proxyRetry;
        $myProxy = $proxyCache[$proxyChosen];
        $GLOBALS['msg'] += "Trying with " . $myProxy . "\n";
//        $GLOBALS['msg'] .= "proxy:$myProxy<br>";
//        $GLOBALS['msg'] .= system("wget -O nytimes.com  -t 1 --speed-limit=1.0 --speed-time=5 --no-cookies use_proxy=yes -e http_proxy=" . $myProxy . " " . $URLServer);
        $content = getURL($GLOBALS['url'], $cURL_Session, $myProxy);
        return $content;

    }
}

// keep trying proxies until one of them works
if (empty($content) || (strlen($content) < 10000) || isCaptchaRoadblock($content)) {
    while ((empty($content) || (strlen($content) < 10000)) && $attempts < 20) {
        $attempts += 1;
        $GLOBALS['msg'] .= "Attempt " . $attempts . "was unsuccsessful. Retrying...\n\n";
        $content = curl($url, $attempts);
       if ((!empty($content) || (strlen($content) > 10000))) {
         $GLOBALS['msg'] .= "Success!!!\n\n\n\n\n";
       }
    }
} 

// if it's still empty, then email me and quit
if (empty($content) || (strlen($content) < 10000) || isCaptchaRoadblock($content) ) {
    if (isCaptchaRoadblock($content)) {
        $GLOBALS['msg'] .= "Uh oh. It looks like we've been foiled by a captcha. Don't worry - the results aren't being pushed to the site. But this attempt failed to grab the new results.";
    }
    mail('kzeidler@gmail.com', "Curl failed.", "Hello Kevin, \n\ncurl_anonymously.php attempted to download " .
        $url .
        " but failed after " .
        $attempts .
        " attempts. You can view the error log below." .
        "\n\n\n===================================== Error Log ======================================\n\n\n " .
        $GLOBALS['msg']);
} else {
    // otherwise copy the new version to the canonical location
    mail('kzeidler@gmail.com', "Curl successful!", "Hello Kevin, \n\ncurl_anonymously.php successfully downloaded" .
        $url .
        " after " .
        $attempts .
        " attempts. Here's the output: " .
        "\n\n\n===================================== Success Log =====================================\n\n\n" .
       $GLOBALS['msg'] .
        "The final output is:\n" .
        $content);
    $ts = date_format($date, 'Y-m-d H:i:s' );
    $shorturl = substr($url,11);
    $slashesRemoved = str_replace("/", "+", $shorturl);
    $fp = '/var/www/scraper/sandbox/cached/' . $slashesRemoved . '_'  . $ts . '.html';
    file_put_contents('/var/www/scraper/sandbox/cached/fptr.txt', $fp);
    // we're going to do a sanity check on that before we use it. but in order to do that, we need the filename.
    // write it to a .txt file.
    fwrite($location, $fp);
    fclose($location);

    // and now write the file to the filename
    $dest = fopen("./cltest.txt", "w")
    fwrite($dest, $content);
    fclose($dest);
}

