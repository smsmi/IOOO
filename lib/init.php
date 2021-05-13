<?php
	define("__DIR__", "lib");
	define("__TPL__", "tpl");

	$GLOBALS['config'] = parse_ini_file($_SERVER['DOCUMENT_ROOT'] . '/../private/config.ini', true);

	$GLOBALS['config']['directory'] = '/projects/IOOO/';
	$GLOBALS['config']['mysql']['db'] = 'IOOO';

	session_start();
?>