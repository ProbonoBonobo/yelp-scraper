<?php
/**
 * Created by PhpStorm.
 * User: kevinzeidler
 * Date: 5/14/16
 * Time: 7:07 PM
 */


function curl($URLServer,$proxyRetry, $postdata="", $cookieFile=null, $proxy=true)
{
    global $proxyCache;
    $agent = "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.2.12) Gecko/20101027 Ubuntu/10.10 (maverick) Firefox/3.6.12";
    $cURL_Session = curl_init();

    curl_setopt($cURL_Session, CURLOPT_URL, $URLServer);
    curl_setopt($cURL_Session, CURLOPT_USERAGENT, $agent);
    if ($postdata != "") {
        curl_setopt($cURL_Session, CURLOPT_POST, 1);
        curl_setopt($cURL_Session, CURLOPT_POSTFIELDS, $postdata);
    }
    curl_setopt($cURL_Session, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($cURL_Session, CURLOPT_FOLLOWLOCATION, 1);
    if ($cookieFile != null) {
        curl_setopt($cURL_Session, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($cURL_Session, CURLOPT_COOKIEFILE, $cookieFile);
    }

    if ($proxy == true) {
        if ($proxyCache == "") {
//            $c = curl("free-proxy-list.net", "", null, false);
//            preg_match_all("/([0-9]*).([0-9]*).([0-9]*).([0-9]*):([0-9]*)/", $c, $matches);
//            $matches = $matches[0];
            $proxyCache = file_get_contents('proxies.txt');
            $proxyCache = explode(",", $proxyCache);
//            foreach ($proxyCache as $proxyIP) {
//                echo $proxyIP;
//            }
            var_dump($proxyCache);
        }
        $proxyCount = count($proxyCache);
        $proxyChosen = 0;
        $myProxy = $proxyCache[$proxyChosen];
        echo "proxy:$myProxy<br>";
        system("wget -O sandbox/cached/yelp.html -e use_proxy=yes -e http_proxy=" . $myProxy . " " . $URLServer);

    }
}

curl("http://www.yelp.com/biz/mobile-iphone-ipad-screen-repair-san-diego",0);
