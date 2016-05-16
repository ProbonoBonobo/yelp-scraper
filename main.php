<?php
/**
 * Created by PhpStorm.
 * User: kevinzeidler
 * Date: 5/15/16
 * Time: 10:02 PM
 */
// combine the cronjobs into one

system('php sandbox/extractProxyIPs.php');
sleep(30);
system('php sandbox/curl_anonymously.php');
sleep(30);
system('php sanitytest.php');
sleep(30);
system('php scrape.php');
sleep(30);
system('php filter_reviews.php');