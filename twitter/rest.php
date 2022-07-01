<?php
/**
 * Created by JetBrains PhpStorm.
 * User: lukewilson
 * Date: 12/03/2014
 * Time: 17:27:26
 * To change this template use File | Settings | File Templates.
 *
 */


ini_set('display_errors', 1);
require_once('TwitterAPIExchange.php');


$settings = array(
	'oauth_access_token' => "1510116535-0QAYVBqwHvlDVPk5lkXU0uTTQ6ngKLrSubgxd5q",
	'oauth_access_token_secret' => "f6myLDEECfkaxLmwI2MK18GFhSG94hdXKZ255Gbhe4",
	'consumer_key' => "C2wjeZBhDM8XJKYxTzfsyw",
	'consumer_secret' => "oBEgDoECDmKHsyc2ojNPxebuMjBZc7ZNzYwtJ0XQ"
);

$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
$getfield = '?screen_name=SitRobotSit';
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
$response = $twitter->setGetfield($getfield)
	->buildOauth($url, $requestMethod)
	->performRequest();

$user_array = json_decode($response);
$user = $user_array[0]->user;



$file = 'cache.txt';
// Open the file to get existing content
/*$current = file_get_contents($file);*/
// Append a new person to the file
$current = json_encode($user);
// Write the contents back to the file
file_put_contents($file, $current);


// echo json_encode($user);


#echo '{"id":2562,"content":"Hello, World!"}';

	//	var_dump(json_decode($response));