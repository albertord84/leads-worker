<?php

set_time_limit(0);
date_default_timezone_set('UTC');

require __DIR__.'/../externals/vendor/autoload.php';


$username = 'thiagokurt';
$password = 'thiaguinho8078812421br';

$debug = false; //true;
$truncatedDebug = true; //false;

\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

try {
    echo $username.'<br><br>';    
    $ig->login($username, $password);
    echo 'user logged<br><br>';
    die();
    //var_dump($ig);
} catch (\Exception $e) {
    echo 'Exception<br>';
    echo 'Something went wrong: '.$e->getMessage()."\n";
    exit(0);
}

try {    
    $userId = $ig->people->getUserIdForName('natgeo'); // Get the UserPK ID for "natgeo" (National Geographic).
    $maxId = null; // Starting at "null" means starting at the first page.
    do {        
        //$response = $ig->timeline->getUserFeed($userId, $maxId); // Request the page corresponding to maxId.
        $rankToken = \InstagramAPI\Signatures::generateUUID();
        $response = $ig->people->getFollowers($userId, $rankToken, null, $maxId);
        foreach($response->getUsers() as $item) {
            
            $a = $item->getUsername();            
            $profileInfo = $ig->people->getInfoByName($a);
            $user=$profileInfo->getUser();
            
            $c = $item->getPk();
        }
        // Now we must update the maxId variable to the "next page".
        // This will be a null value again when we've reached the last page!
        // And we will stop looping through pages as soon as maxId becomes null.
        $maxId = $response->getNextMaxId();
        // Sleep for 5 seconds before requesting the next page. This is just an
        // example of an okay sleep time. It is very important that your scripts
        // always pause between requests that may run very rapidly, otherwise
        // Instagram will throttle you temporarily for abusing their API!
        echo "Sleeping for 5s...\n";
        sleep(5);
    } while ($maxId !== null); // Must use "!==" for comparison instead of "!=".
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
}
