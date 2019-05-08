<?php
require "../Private/connection.php";

if(!$dbh)
	die();

session_start();
if(!isset($_SESSION['username']) || !isset($_SESSION['userid'])){
	header("Location: login.php");
	die();
}

if(isset($_GET['intent']) && $_GET['intent'] == 'remove'){
	if(empty($_GET['gameid'])){
		header("Location: index.php");
		die();
	}
	
	$sql = "DELETE FROM `savedgames` WHERE `savedgames`.`gameid` = ?";
	$stmt = $dbh->prepare($sql);
	$success = $stmt->execute([$_GET['gameid']]);
	if($success){
		echo "<p>Partiet plockades bort från dina sparade partier.</p>";
	}
	else{
		echo "<p>Partiet kunde inte plockas bort från dina sparade partier av okänd anledning.</p>";
	}
}

function EchoGame($dbh, $gameId){
	$sql = "SELECT * FROM games WHERE id=?";
	$stmt = $dbh->prepare($sql);
	$success = $stmt->execute([$gameId]);
	
	$game = $stmt->fetch();
	
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
		<p>{$htmlReadyWhiteUserName} (ELO {$htmlReadyWhiteEloRating}) VS {$htmlReadyBlackUserName} (ELO {$htmlReadyBlackEloRating})</p>
		<a href=\"saved-games.php?intent=remove&gameid={$gameId}\">Ta bort</a>
	</div>";
}

function EchoGames($dbh){
	$sql = "SELECT gameid FROM savedgames WHERE userid = ?";
	$stmt = $dbh->prepare($sql);
	$success = $stmt->execute([$_SESSION['userid']]);
	
	$games = $stmt->fetchAll();
	foreach($games as $row){
		EchoGame($dbh, $row['gameid']);
	}
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
		EchoGames($dbh);
	?>
</body>

</html>