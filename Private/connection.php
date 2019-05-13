<?php

$dsn = 'mysql:host=localhost;dbname=webchess;charset=utf8';
$user = 'user';
$password = 'qwe123';//Skaffa bättra lösenord
$options = [
	PDO::ATTR_EMULATE_PREPARES	 => false,
	//Kasta exception för varje fel
	PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
	//Använd associativa arrayer när svar hämtas
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];
	
try {
	$dbh = new PDO($dsn, $user, $password, $options);
} catch (PDOException $e) {
	echo 'Connection failed: '.$e->getMessage();
	$dbh = false;
} 