<?php

set_time_limit(0);
date_default_timezone_set('UTC');

ini_set('xdebug.var_display_max_depth', 64);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 8024);

require __DIR__.'/../vendor/autoload.php';

$username = 'josergm86';
$password = 'josergm2';
$debug = false;
$truncatedDebug = true;

///opt/lampp/htdocs/leads/worker/vendor/mgp25/instagram-php/src/Response/Model/Location.php
///opt/lampp/htdocs/leads/worker/vendor/mgp25/instagram-php/src/Request/Location.php

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

try {
    $ig->login($username, $password);
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
    exit(0);
}

try {
    
    $rankToken = \InstagramAPI\Signatures::generateUUID();
    $locationId='481100643';
    $maxId = null;
    do {
        $response = $ig->location->getFeed($locationId, $rankToken, $maxId);
        //var_dump($response);
        
        foreach ($response->getItems() as $item) {
            var_dump($item);
//            $location = $item->getLocation();
//            $excludeList[] = $location->getFacebookPlacesId();
//            // Let's print some details about the item.
//            printf("%s (%.3f, %.3f)\n", $item->getTitle(), $location->getLat(), $location->getLng());
        }
        $maxId = $response->getNextMaxId();
        //$rankToken = $response->getRankToken();
        $rankToken = \InstagramAPI\Signatures::generateUUID();
        sleep(5);        
    } while ($response->getHasMore());
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
}






/*try {
    // Let's list locations that match "milan".
    $query = 'milan';

    // Initialize the state.
    $rankToken = null;
    $excludeList = [];
    
    do {
        // Request the page.
        $response = $ig->location->findPlaces($query, $excludeList, $rankToken);
        // In this example we're simply printing the IDs of this page's items.
        foreach ($response->getItems() as $item) {
            $location = $item->getLocation();            
            // Add the item ID to the exclusion list, to tell Instagram's server
            // to skip that item on the next pagination request.
            $excludeList[] = $location->getFacebookPlacesId();
            // Let's print some details about the item.
            printf("%s (%.3f, %.3f)\n", $item->getTitle(), $location->getLat(), $location->getLng());
        }
        // Now we must update the rankToken variable.
        $rankToken = $response->getRankToken();
        // Sleep for 5 seconds before requesting the next page. This is just an
        // example of an okay sleep time. It is very important that your scripts
        // always pause between requests that may run very rapidly, otherwise
        // Instagram will throttle you temporarily for abusing their API!
        echo "Sleeping for 5s...\n";
        sleep(5);
    } while ($response->getHasMore());
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
}*/
