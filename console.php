#!/usr/bin/env php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use \Symfony\Component\Console\Application;

set_time_limit(0);
date_default_timezone_set('UTC');

$application = new Application;
$application->setName('Instagram Liker');
$application->setVersion('0.0.1');

// instantiate and add all commands from src/Command folder to Application instance
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/src/Command')) as $file) {
	if (!$file->isFile() || $file->getFilename() == 'Command.php') {
		continue;
	}

	/** @var \BuilderAPI\Command\Command $command */
	$cleanPathname = str_replace(__DIR__ . '/src/Command/', '', $file->getPathname());
	$commandClassName = 'BuilderAPI\\Command\\' . substr(str_replace('/', '\\', $cleanPathname), 0, -4);
	$command = new $commandClassName;
	$command->setContainer($container);
	$application->add($command);
}

$application->run();
