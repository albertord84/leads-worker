<?php

//require_once 'class/system_config.php';
//
////$url = "'http://www.google.com/'";
//$url = "https://www.instagram.com/";
//$cookie = "/home/albertord/cookies.txt";
//$ch = curl_init($url);
////curl_setopt($ch, CURLOPT_URL, $url);
////curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
////curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//curl_setopt($ch, CURLINFO_HEADER_OUT, true);
//curl_setopt($ch, CURLINFO_COOKIELIST, true);
//curl_setopt($ch, CURLOPT_HEADERFUNCTION, "curlResponseHeaderCallback");
//$response = curl_exec($ch);
////var_dump($cookies);
////TODO: recursive function to load cookie variavels properly
//echo $cookies[1][1];
//$csrftoken = explode("=", $cookies[1][1]);
//$csrftoken = $csrftoken[1];
//var_dump($csrftoken);
//
////$cur_info = curl_getinfo($ch);
////var_dump($cur_info);
////if (curl_errno($ch)) die(curl_error($ch));
////$dom = new DomDocument();
////$dom->loadHTML($response);
////print_r($dom);
//echo "<br><br>Secound petition<br>";
//$username = \dumbu\cls\system_config::SYSTEM_USER_LOGIN;
//$password = \dumbu\cls\system_config::SYSTEM_USER_PASS;
//$postinfo = "username=$username&password=$password";
//
//$headers  = array();
//$headers[] = "Host: www.instagram.com";
//$headers[] = "User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:49.0) Gecko/20100101 Firefox/49.0";
//$headers[] = "Accept: application/json";
//$headers[] = "Accept-Language: en-US,en;q=0.5, ";
//$headers[] = "Accept-Encoding: gzip, deflate, br";
////$headers[] = "--compressed ";
//$headers[] = "Referer: https://www.instagram.com/";
//$headers[] = "X-CSRFToken: $csrftoken";
//$headers[] = "X-Instagram-AJAX: 1";
////$headers[] = "Content-Type: application/x-www-form-urlencoded";
//$headers[] = "Content-Type: application/json";
//$headers[] = "X-Requested-With: XMLHttpRequest";
//$headers[] = "Cookie: csrftoken=$csrftoken";
//$url = "https://www.instagram.com/accounts/login/ajax/";
//curl_setopt($ch, CURLOPT_URL, $url);
////curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
////curl_setopt($ch, CURLOPT_POST, true);
//curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
//curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
//curl_setopt($ch, CURLOPT_HEADER, 1);
//curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($ch, CURLOPT_HEADERFUNCTION, "curlResponseHeaderCallback");
//global $cookies;
//$cookies = array();
//var_dump($cookies);
//$html = curl_exec($ch);
//$info = curl_getinfo($ch);
//var_dump($cookies);
//
//print_r($info);
//echo "<br><br>";
//var_dump($html);
//if (curl_errno($ch))
//    print curl_error($ch);
//curl_close($ch);
//
$ref_prof = "albertoreyesd84";
$content = file_get_contents("https://www.instagram.com/web/search/topsearch/?context=blended&query=$ref_prof");
$users = json_decode($content)->users;
var_dump($users);

$User = NULL;
foreach ($users as $key => $user) {
    if ($user->user->username == $ref_prof) {
        $User = $user->user;
        echo "User found: <br>";
        var_dump($user->user);
    }
}

return $User;


$content = file_get_contents("https://www.instagram.com/$ref_prof/");

$doc = new DOMDocument();
//$doc->loadXML($content);
$doc->loadHTML($content);
//var_dump($doc);

$search = "\"follows\": {\"count\": ";
$start = strpos($doc->textContent, $search);

$substr1 = substr($doc->textContent, $start);
$substr2 = substr($substr1, strlen($search), strpos($substr1, "}") - strlen($search));
echo "<br><br><br><br>" . $substr1;
echo "<br><br><br><br>" . $substr2;

//echo "<br><br><br><br>" . substr($doc->textContent, $start + count($search));

//die("7");

$aTags = $doc->getElementsByTagName("a");
$searchText = "following";
$found = FALSE;

var_dump($aTags);


//for ($i = 0; $i < $aTags.length; $i++) {
//    var_dump($aTags[i]);
//  if ($aTags[$i].textContent == $searchText) {
//    $found = $aTags[i];
//    break;
//  }
//}


// get cookie
// multi-cookie variant contributed by @Combuster in comments
function curlResponseHeaderCallback($ch, $headerLine) {
    global $cookies;
    if (preg_match('/^Set-Cookie:\s*([^;]*)/mi', $headerLine, $cookie) == 1)
        $cookies[] = $cookie;
//        $cookies[] = $headerLine;
    return strlen($headerLine); // Needed by curl
}
