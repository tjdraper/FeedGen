<?php

if (! defined('FEEDGEN_NAME')) {
	define('FEEDGEN_NAME', 'FeedGen');
	define('FEEDGEN_VER', '0.0.5');
	define('FEEDGEN_AUTHOR', 'TJ Draper');
	define('FEEDGEN_AUTHOR_URL', 'http://buzzingpixel.com');
	define('FEEDGEN_DESC', 'Generate an RSS feed.');
	define('FEEDGEN_PATH', PATH_THIRD . 'feedgen/');
}

$config['name'] = FEEDGEN_NAME;
$config['version'] = FEEDGEN_VER;