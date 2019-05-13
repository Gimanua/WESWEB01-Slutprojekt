<?php

require "../Private/connection.php";

if(!$dbh)
	die();

session_start();
if(isset($_SESSION['username']) && isset($_SESSION['userid']))
	$loggedIn = true;
else
	$loggedIn = false;
?>
<!DOCTYPE html>
<html lang="sv">

<head>
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="/CSS/index.css"/>
</head>

<body>
	<?php
	if($loggedIn){
		//TODO Kolla om man är admin
		$htmlUsername = htmlspecialchars($_SESSION['username']);
		echo "<p class=\"userinfo\">Inloggad som: <a href=\"http://localhost/account.php\">{$htmlUsername}</a></p>";
		echo '<p class="userinfo"><a href="http://localhost/manage-challenges.php">Hantera utmaningar</a></p>';
		echo '<p class="userinfo"><a href="http://localhost/saved-games.php">Sparade partier</a></p>';
	}
	else{
		echo '<a href="login.php" class="login">Logga in / Registrera</a>';
	}
	?>
	<p class="ongoing">Pågående partier</p>;
	<?php
	$sql = "SELECT * FROM Games WHERE Private=FALSE ORDER BY AverageELORating DESC";
	$stmt = $dbh->query($sql);
	if(!$stmt)
		die();
	
	$counter = 0;
	foreach($stmt as $row){
		if($counter >= 3)
			break;
		echo "<div>";
		
		$imageURL = htmlspecialchars($row['imageurl']);
		$fen = htmlspecialchars($row['fen']);
		echo "<img src=\"{$imageURL}\" alt=\"FEN-Sträng av partiet: {$fen}\"/>";
		$sql = "SELECT Username, ELORating FROM Users WHERE ID=?";
		
		$playerstmt = $dbh->prepare($sql);
		$playerstmt->execute([$row['whiteuserid']]);
		$whitePlayer = $playerstmt->fetch();
		
		$playerstmt = $dbh->prepare($sql);
		$playerstmt->execute([$row['blackuserid']]);
		$blackPlayer = $playerstmt->fetch();
		
		echo '<p>'.htmlspecialchars($whitePlayer['Username']).' (ELO '.htmlspecialchars($whitePlayer['ELORating']).
		') VS '.htmlspecialchars($blackPlayer['Username']).' (ELO '.htmlspecialchars($blackPlayer['ELORating']).')</p>';
		$htmlReadyGameID = htmlspecialchars($row['id']);
		echo "<p class=\"watch\"><a href=\"view.php?gameid={$htmlReadyGameID}\">Titta på</a></p>";
		
		echo "</div>";
		
		$counter++;
	}
	echo '<br>';
	
	if($loggedIn)
	{
		echo '<p>Mina pågående partier:</p>';
		$sql = "SELECT * FROM games WHERE whiteuserid = ? OR blackuserid = ?";
		$stmt = $dbh->prepare($sql);
		$stmt->execute([$_SESSION['userid'], $_SESSION['userid']]);
		$games = $stmt->fetchAll();
		foreach($games as $row){
			echo '<div>';
			
			$imageURL = htmlspecialchars($row['imageurl']);
			$fen = htmlspecialchars($row['fen']);
			echo "<img src=\"{$imageURL}\" alt=\"FEN-Sträng av partiet: {$fen}\" />";
			
			$sql = "SELECT username, elorating FROM users WHERE id=?";
			$stmt = $dbh->prepare($sql);
			$stmt->execute([$row['whiteuserid']]);
			$whitePlayer = $stmt->fetch();
			
			$stmt = $dbh->prepare($sql);
			$stmt->execute([$row['blackuserid']]);
			$blackPlayer = $stmt->fetch();
			
			echo '<p>'.htmlspecialchars($whitePlayer['username']).' (ELO '.htmlspecialchars($whitePlayer['elorating']).
			') VS '.htmlspecialchars($blackPlayer['username']).' (ELO '.htmlspecialchars($blackPlayer['elorating']).')</p>';
			echo "<p class=\"play\"><a href=\"play.php?gameid={$row['id']}\">Spela</a></p>";
			
			echo '</div>';
		}
	}
	
	?>
</body>

</html>