<?php

// Environment
if (getenv('DATABASE_URL') 
	&& $_SERVER['SERVER_ADDR'] !== '127.0.0.1') 
{
	$url = parse_url(getenv('DATABASE_URL'));
	$server = $url['host'];
	$username = $url['user'];
	$password = $url['pass'];
	$db = substr($url['path'], 1);
}
else {
	$server = 'localhost';
        $username = 'blogdb';
        $password = 'blogdb';
        $db = 'blogdb';
}

// Establish connection to db
$pdo = new PDO("mysql:host=$server;
  dbname=$db;charset=utf8",
  $username,
  $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

unset($url, $server, $username, $password, $db);
