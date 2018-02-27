<?php

set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__ . '/vendor/autoload.php';
require_once('Db.php');
require_once('Constants.php');

/////// CONFIG ///////
$debug = false;
$truncatedDebug = false;
//////

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
$db = new \MysqlConnection();

try {
	$ig->setUser(Constants::I_USERNAME, Constants::I_PASS);
	$ig->login();
} catch (\Exception $e) {
	echo 'Something went wrong: ' . $e->getMessage() . "\n";
	exit(0);
}
try {
	$followers = [];
	// Starting at "null" means starting at the first page.
	$maxId = null;
	do {
		// Request the page corresponding to maxId.
		$response = $ig->getSelfUserFollowers($maxId);
		// In this example we're merging the response array, but we can do anything.
		$followers = array_merge($followers, $response->getUsers());
		// Now we must update the maxId variable to the "next page".
		// This will be a null value again when we've reached the last page!
		// And we will stop looping through pages as soon as maxId becomes null.
		$maxId = $response->getNextMaxId();
	} while ($maxId !== null); // Must use "!==" for comparison instead of "!=".
	$cursor = $db->connection();
	$array = [];
	foreach ($followers as $follower) {
		$array[$follower->getUsername()] = $follower->getFullName();
	}
	$sql = $cursor->prepare("INSERT INTO followers (user, description) VALUES (?,?)");
	$sql->bind_param('ss', $user, $descr);
	foreach ($array as $key => $value) {
		$user = $key;
		$descr = $value;
		if ($sql->execute() === TRUE) {
			echo "New record created successfully" . "\n";
		} else {
			try {
				$descr = '';
				$sql->execute();
			} catch (\Exception $e) {
				echo "Error: " . $cursor->error . "\n";
			}

		}
	}
} catch (\Exception $e) {
	echo 'Something went wrong: ' . $e->getMessage() . "\n";
}