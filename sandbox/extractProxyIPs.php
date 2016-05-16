<?php
/**
 * Created by PhpStorm.
 * User: kevinzeidler
 * Date: 5/14/16
 * Time: 6:20 PM
 */


system('wget -O ./cached/proxies.html https://incloak.com/proxy-list/?country=DENLUS&type=h#list');
echo("Fetched. Now scraping the IP addresses and port numbers from the document...\n\n");
$proxies = "";
    $dom = new DOMDocument("1.0");
    $dom->preserveWhiteSpace = false;
    $dom->loadHTMLFile("../cached/proxies.html");
    $scraped_xpath = new DOMXPath($dom); //load the DOM into a convenient database-like representation
    $IPAddresses = $scraped_xpath->query("//tr/td[@class='tdl']");
    $ports = $scraped_xpath->query("//tr/td[2]");
    for ($i = 0; $i < 20; $i++) {
        $proxies .= "http://" . $IPAddresses[$i]->nodeValue . ":";
        $proxies .= $ports[$i]->nodeValue . ",";
    }

$out = fopen("../proxies.txt","w");
fwrite($out, $proxies);
fclose($out);
