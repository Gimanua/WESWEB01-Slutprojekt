<?php

require "../Private/connection.php";

if(!$dbh)
	die();

if(!isset($_GET['gameid'])){
	header("Location: index.php");
	die();
}

function EchoGame($dbh){
	$sql = "SELECT * FROM games WHERE id=?";
	$stmt = $dbh->prepare($sql);
	$success = $stmt->execute([$_GET['gameid']]);
	if(!$success)
		die("Partiet finns inte.");
	
	$game = $stmt->fetch();
	if($game['private'])
		die("Partiet är privat.");
	
	$whiteUserName;
	$whiteEloRating;
	
	$blackUserName;
	$blackEloRating;
	//SQL-fråga för att få användarnamnen på svart och vit
	$sql = "SELECT username, elorating FROM users WHERE id=?";
	$stmt = $dbh->prepare($sql);
	$stmt->execute([$game['whiteuserid']]);
	$whitePlayer = $stmt->fetch();
	$whiteUserName = $whitePlayer['username'];
	$whiteEloRating = $whitePlayer['elorating'];
	
	$sql = "SELECT username, elorating FROM users WHERE id=?";
	$stmt = $dbh->prepare($sql);
	$stmt->execute([$game['blackuserid']]);
	$blackPlayer = $stmt->fetch();
	$blackUserName = $blackPlayer['username'];
	$blackEloRating = $blackPlayer['elorating'];
	
	$htmlReadyFEN = htmlspecialchars($game['fen']);
	$htmlReadyImageURL = htmlspecialchars($game['imageurl']);
	$htmlReadyPGN = htmlspecialchars($game['pgn']);
	
	$htmlReadyWhiteUserName = htmlspecialchars($whiteUserName);
	$htmlReadyWhiteEloRating = htmlspecialchars($whiteEloRating);
	
	$htmlReadyBlackUserName = htmlspecialchars($blackUserName);
	$htmlReadyBlackEloRating = htmlspecialchars($blackEloRating);
	
	echo "
	<div>
		<img src=\"{$htmlReadyImageURL}\" alt=\"{$htmlReadyFEN}\" />
		<p>{$htmlReadyPGN}</p>
		<p>{$htmlReadyWhiteUserName} ({$htmlReadyWhiteEloRating}) VS {$htmlReadyBlackUserName} ({$htmlReadyBlackEloRating})</p>
		<a href=\"\">Spara partiet</a>
	</div>";
}
?>
<!DOCTYPE html>
<html lang="sv">

<head>
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="CSS/login.css"/>
</head>

<body>
	<?php 
		require "../Private/header.php";
		EchoGame($dbh);
	?>
</body>

</html>