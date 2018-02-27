<?php

set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/Constants.php';

//use \MysqlConnection;

/////// CONFIG ///////
$debug = false;
$truncatedDebug = false;
//////

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
//$db = new \MysqlConnection();

try {
	$ig->setUser(Constants::I_USERNAME, Constants::I_PASS);
	$ig->login();
} catch (\Exception $e) {
	echo 'Something went wrong: ' . $e->getMessage() . "\n";
	exit(0);
}
try {
	sleep(rand(5, 10));
	$feed = $ig->getTimelineFeed();
	$items = $feed->getFeedItems();
	$items_id = [];
	foreach ($items as $item) {
		$items_id[] = $item->getId();
	}
	foreach ($items_id as $media_id) {
		if (!empty($media_id)) {
			sleep(rand(5, 10));
			$ig->like($items_id[]);
		}
	}

} catch (\Exception $e) {
	echo 'Something went wrong: ' . $e->getMessage() . "\n";
}