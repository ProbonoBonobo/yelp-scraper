<?php
/**
 * Created by PhpStorm.
 * User: kevinzeidler
 * Date: 5/15/16
 * Time: 8:59 PM
 */

$ptr = fopen('./cached/pointer.txt', 'r');
$filepath = fread($ptr, 1000);
fclose($ptr);

$filesize = filesize($filepath);

echo "The filesize of the extracted HTML is: " . filesize($filepath);
if ($filesize > 400000 && $filesize < 600000) {
    echo "\nLooks legit.";
    echo "\nCopying to the canonical location.";
    copy($filepath, './cached/yelp.html');
}