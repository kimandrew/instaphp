<?php

set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__ . '/vendor/autoload.php';
require_once('Db.php');
require_once('Constants.php');

use InstagramAPI\InstagramID;

/////// CONFIG ///////
$debug = false;
$truncatedDebug = false;
//////

$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);
$db = new \MysqlConnection();

try {
	$ig->setUser(Constants::I_USERNAME, Constants::I_PASS);
	$ig->login();
	$mediaId = InstagramID::fromCode('BY8L-PUHHY5');
	$response = $ig->getMediaLikers($mediaId);
	$likers = [];
	foreach ($response->users as $liker) {
		$likers[$liker->username] = $liker->full_name;
	}
	$cursor = $db->connection();
	$sql = $cursor->prepare("INSERT INTO to_follow (user, description) VALUES (?,?)");
	$sql->bind_param('ss', $user, $descr);
	foreach ($likers as $username => $description) {
		$user = $username;
		$descr = $description;
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
	exit(0);
}