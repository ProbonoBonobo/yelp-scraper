<?php
/**
 * Created by PhpStorm.
 * User: kevinzeidler
 * Date: 5/7/16
 * Time: 10:06 PM
 *
 * Diff the old vs. new outputs and send an email notification if they're different. (i.e., a new review has been
 * posted)
 */

//



function init() {
    $old = file_get_contents('./old.json');
    $new = file_get_contents('./results.json');
    if ($new == $old) {
        mail('kevinzeidler@gmail.com', "No new reviews.", "Beacon.php ran, but found no new reviews. Here's the raw JSON: \n\n" . $new . "\n\nPublic key: ExG%:u'LFfuj-9~*%YH!N#^FET}z93hF");
        return;
        }
    else {
        $recent = file_get_contents('mostrecent.json', 'r');
        mail('sandiegophonerepairs@gmail.com', "You have a new review on Yelp!", "To view, go to http://kevinzeidler.com/vagrant/default. You could also read the raw JSON below*. \n(Note: If the review is less than 5 stars, it won't appear below -- or on the site.)\n\n\n" . $new . "\n\n\nPublic key: ExG%:u'LFfuj-9~*%YH!N#^FET}z93hF");
        mail('kzeidler@gmail.com', "You have a new review on Yelp!", "To view, go to http://kevinzeidler.com/vagrant/default. You could also read the raw JSON below.\n\n\n========== Top 3: ==========\n" . $recent . "\n===========================\n\n\n========== Scraped: ==========\n" . $new . "\n===========================\n\n\nPublic key: ExG%:u'LFfuj-9~*%YH!N#^FET}z93hF");
        $stale = fopen('./old.json', 'w');
        fwrite($stale, $new);
        fclose($stale);
    }

}
init();
