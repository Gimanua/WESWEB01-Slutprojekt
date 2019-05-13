<?php

require "../Private/connection.php";

if(!$dbh)
	die();

session_start();
if(!isset($_SESSION['username']) || !isset($_SESSION['userid'])){
	header("Location: login.php");
	die();
}

if(isset($_GET['intent']) && $_GET['intent'] == 'submitmove' && !empty($_POST['move'])){
	$move = $_POST['move'];
	$newFen = GetFen();
}

function GetFen($oldFen, $move){
	$from = substr($move, 0, 2);
	$to = substr($move, 3, 2);
	
	$rank = 0;
	$column = 0;
	$chessBoard = [ 0 => [], 1 => [], 2 => [], 3 => [], 4 => [], 5 => [], 6 => [], 7 => [] ];
	for($i = 0; $i < strlen($oldFen); $i++){
		$char = substr($oldFen, $i, 1);
		if($char == ' '){
			break;
		}
		else if($char == '/'){
			$rank++;
			$column = 0;
		}
		else if(is_numeric($char)){
			for($x = 0; $x < (int)$char; $x++){
				$chessBoard[$rank][$x] = ' ';
			}
		}
		else{
			$chessBoard[$rank][$column] = $char;
			$column++;
		}
	}
	
	
}

function ActiveColor($fen){
	$activeColor = substr($fen, strpos($fen, " ") + 1, 1);
	if($activeColor == 'b')
		return 'black';
	else
		return 'white';
}

function MovePiece

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
	}
	
	echo "
	<div>
		<img src=\"{$htmlReadyImageURL}\" alt=\"{$htmlReadyFEN}\" />
		<p>{$htmlReadyPGN}</p>
		<p>Det är {$whosTurn} drag.</p>";
		
	if($whosTurn == 'ditt'){
		echo "
			<form action=\"play.php?gameid={$_GET['gameid']}&intent=submitmove\" method=\"post\">
				<label for=\"move\">Ditt drag i formatet XN:XN</label><br>
				<input type=\"text\" name=\"move\" required /><br>
				<input type=\"submit\" value=\"Flytta pjäs\" />
			</form>
		";
	}
		
	echo "<p>{$htmlReadyWhiteUserName} ({$htmlReadyWhiteEloRating}) VS {$htmlReadyBlackUserName} ({$htmlReadyBlackEloRating})</p>
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