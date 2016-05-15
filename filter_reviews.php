<?php
/**
 * Created by PhpStorm.
 * User: kevinzeidler
 * Date: 4/28/16
 * Time: 1:14 AM
 *
 *  Have: a JSON object that contains approximately 20 reviews.
 *
 *  Want: A filtered JSON object that contains 3 reviews, each of which satisfies the following constraints:
 *         1. Length: short enough to fit within the bounding box (fewer than 550 chars or so)
 *         2. Rating: it's a 5 star review
 *         3. Sanity: the review object passes some heuristic sanity checks defined below (e.g., no blank fields)
 *         3. Recency: indexically speaking, each review is fresher than the one after it
 */


$f = file_get_contents('results.json');
$maxLength = 550;
$filtered = [];
$json = json_decode($f,true);

$ctr = 0;


function shortEnough($review) {
    return (strlen($review) < 750);
}

function isFiveStars($rating) {
    return (strcmp($rating, "5.0") !== 1);
}

function getHDAvatar($currentURI)
{
    // the HD avatar is located in the same place as the low-res avatar, but
    // called "ls.jpg" instead of "60s.jpg"

    if (substr($currentURI, -18) == 'user_60_square.png') {
        // One exception: if the user doesn't have an avatar, the URI will
        // end in "user_60_square.png" and have no HD version. in that case,
        // route to a local HD copy of the anonymous avatar
        return "./img/anon.jpg";
    }

    $hdURI = "http://" . substr($currentURI, 2, -7) . "ls.jpg";
    return $hdURI;
}


function build_sorter($key) {
    return function ($a, $b) use ($key) {
        return strnatcmp($b[$key], $a[$key]);
    };
}

for ($i = 0; $i < 20; $i++) {
//foreach ($json as $value) {

    // Use $field and $value here

//
//    foreach ($json[$i] as $field) {


    $review = $json[$i]['content'];
    $rating = $json[$i]['rating'];
    $date = $json[$i]['date'];
    $avatar = $json[$i]['avatar'];
    $city = $json[$i]['city'];
    $name = $json[$i]['name'];
    $HDAvatar = getHDAvatar($avatar);

    if (!$review || !$rating || !$date || !$avatar || !$city) {
        echo "o shit there a problem wit yo shit";
    }


    if (shortEnough($review) && isFiveStars($rating)) {
        array_push($filtered, array('date' => $date,
            'review' => $review,
            'name' => $name,
            'avatar' => $avatar,
            'city' => $city,
            'HDAvatar' => $HDAvatar));

    } else {

    }
}






usort($filtered, build_sorter('date'));

$out = fopen('mostrecent.json', 'w');
fwrite($out, json_encode(array_slice($filtered,0,3), JSON_PRETTY_PRINT));
fclose($out);
?>
