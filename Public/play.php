<?php

require "../Private/connection.php";

if(!$dbh)
	die();

session_start();
if(!isset($_SESSION['username']) || !isset($_SESSION['userid'])){
	header("Location: login.php");
	die();
}

function ActiveColor($fen){
	$activeColor = substr($fen, strpos($fen, " ") + 1, 1);
	if($activeColor == 'b')
		return 'black';
	else
		return 'white';
}

function EchoGame($dbh){
	$sql = "SELECT * FROM games WHERE id=?";
	$stmt = $dbh->prepare($sql);
	$success = $stmt->execute([$_GET['gameid']]);
	if(!$success)
		die("Partiet finns inte.");
	
	$userColor;
	$game = $stmt->fetch();
	if($game['whiteuserid'] == $_SESSION['userid']){
		$userColor = 'white';
	}
	elseif($game['blackuserid'] == $_SESSION['userid']){
		$userColor = 'black';
	}
	else{
		die("Du är inte en deltagare av partiet.");
	}
		
	
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
	
	$whosTurn = 'motståndarens';
	$activeColor = ActiveColor($game['fen']);
	if($activeColor == $userColor){
		$whosTurn = 'ditt';
		//Formulär ska finnas
	}
	
	echo "
	<div>
		<img src=\"{$htmlReadyImageURL}\" alt=\"{$htmlReadyFEN}\" />
		<p>{$htmlReadyPGN}</p>
		<p>Det är {$whosTurn} drag.</p>
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