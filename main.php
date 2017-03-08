<?php
/**
 * Created by PhpStorm.
 * User: kevinzeidler
 * Date: 5/15/16
 * Time: 10:02 PM
 */
// combine the cronjobs into one

system('php5 /var/www/scraper/sandbox/extractProxyIPs.php');
sleep(30);
system('php5 /var/www/scraper/sandbox/curl_anonymously.php');
sleep(30);
system('php5 /var/www/scraper/sanitytest.php');
sleep(30);
system('php5 /var/www/scraper/scrape.php');
sleep(30);
system('php5 /var/www/scraper/filter_reviews.php');
