<?php

set_time_limit(0);
date_default_timezone_set('UTC');
require __DIR__ .'/vendor/autoload.php';
require __DIR__ . '/Constants.php';
require __DIR__ .'/Db.php';

/////// CONFIG ///////
$debug = false;
$truncatedDebug = false;
//////
$db = new \MysqlConnection();
$ig = new \InstagramAPI\Instagram($debug, $truncatedDebug);

try {
    $ig->setUser(Constants::I_USERNAME, Constants::I_PASS);
    $ig->login();
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
    exit(0);
}
try {
	$competitor = 'vivafitnesskld';
    $maxId = null;
    do {
		$userId = $ig->getUsernameId($competitor, $maxId);
		$users = $ig->getUserFollowers($userId)->getFullResponse();
		sleep(rand(1,3));
	} while($maxId !== null);
	$array = [];
	foreach ($users->users as $user) {
		if (!is_array($user)) {
			$array[$user->username] = $user->full_name;
		}
	}
	$cursor = $db->connection();
	$sql = $cursor->prepare("INSERT INTO competitor_followers (user, description, competitor) VALUES (?,?,?)");
	$sql->bind_param('sss', $user, $descr, $competitor);
	$competitor = 'figurka.kld';
	foreach ($array as $key=>$value) {
		$user = $key;
		$descr = $value;
		if ($sql->execute() === TRUE) {
			echo "New record created successfully"."\n";
		} else {
			try {
				$descr = '';
				$sql->execute();
			} catch (\Exception $e){
				echo "Error: ". $cursor->error. "\n";
			}

		}
	}
	echo count($array);
} catch (\Exception $e) {
    echo 'Something went wrong: '.$e->getMessage()."\n";
}