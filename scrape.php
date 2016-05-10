<?php
$success = array();
$fail = array();
$in=file_get_contents("queries.json");
$queryobj=json_decode($in);

// KEYRING is a 2d array with several xpath queries defined for each category that we need to get. Having multiple
// xpath query definitions makes the scraper less likely to fail, in the event that the target DOM structure changes
// at some point in the future (and easier to update, should all of the xpath queries for a particular category
// happen to fail).
$KEYRING = array();



// Iterate through the xpath queries imported from the doc and load them into keyring
foreach ($queryobj as $category => $xpathQueries) {
    $KEYRING[$category] = array();
    foreach ($xpathQueries as $maybeWorkingXpathQuery) {
        array_push($KEYRING[$category], $maybeWorkingXpathQuery);
    }
}


// This is what we're scraping. It's the output location of the wget cronjob. Don't change it.
$scraped = file_get_contents("./cached/new.html");


// instantiate the object model
if(!empty($scraped)) { //if any html is actually returned
    $DOM = new DOMDocument('1.0', 'UTF-8');
    $DOM->preserveWhiteSpace = true;
    error_reporting(E_ERROR | E_PARSE); // shut up about yelp's non-compliant html
    $DOM->loadHTMLFile("./cached/new.html"); //reconstitute the DOM from html
    $DOM->formatOutput = true;
    $DOM->encoding = 'UTF-8';
    $htm = $DOM->saveXML();
    $scraped_xpath = new DOMXPath($DOM); //load the DOM into a convenient database-like representation


    $json = array();

    foreach ($KEYRING as $category => $value) {
        foreach ($KEYRING[$category] as $querystring) {
            $xpath_results = $scraped_xpath->query($querystring);
            $reviewIndex = 0;
            if ($xpath_results->length == 20) {
                foreach ($xpath_results as $res) {
                    $json[$reviewIndex][$category] = $xpath_results->item($reviewIndex)->nodeValue;
                    $reviewIndex++;
                }
                break;

            }
        }
    }
}


$json = json_encode($json, JSON_PRETTY_PRINT);
$out = fopen('results.json', 'w');
fwrite($out, $json);
fclose($out);
?>