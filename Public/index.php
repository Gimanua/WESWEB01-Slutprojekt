<?php

require "../Private/connection.php";

if(!$dbh)
	die();

session_start();
?>
<!DOCTYPE html>
<html lang="sv">

<head>
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="/CSS/index.css"/>
</head>

<body>
	<?php
	if(isset($_SESSION['username'])){
		//Kolla om man är admin
		$htmlUsername = htmlspecialchars($_SESSION['username']);
		echo "<p class=\"userinfo\">Inloggad som: <a href=\"http://localhost/account.php\">{$htmlUsername}</a></p>";
		echo '<a href="http://localhost/manage-challenges.php" class="userinfo">Hantera utmaningar</a>';
		echo '<a href="http://localhost/saved-games.php" class="userinfo">Sparade partier</a>';
	}
	else{
		echo '<a href="login.php" class="login">Logga in / Registrera</a>';
	}
	?>
	<p class="ongoing">Pågående partier</p>;
	<?php
	$sql = "SELECT FEN, ImageURL, BlackUserID, WhiteUserID FROM Games WHERE Private=FALSE ORDER BY AverageELORating DESC";
	$stmt = $dbh->query($sql);
	if(!$stmt)
		die();
	
	$counter = 0;
	foreach($stmt as $row){
		if($counter >= 3)
			break;
		echo "<div>";
		
		$imageURL = htmlspecialchars($row['ImageURL']);
		$fen = htmlspecialchars($row['FEN']);
		echo "<img src=\"{$imageURL}\" alt=\"FEN-Sträng av partiet: {$fen}\"/>";
		$sql = "SELECT Username, ELORating FROM Users WHERE ID=?";
		
		$playerstmt = $dbh->prepare($sql);
		$playerstmt->execute([$row['WhiteUserID']]);
		$whitePlayer = $playerstmt->fetch();
		
		$playerstmt = $dbh->prepare($sql);
		$playerstmt->execute([$row['BlackUserID']]);
		$blackPlayer = $playerstmt->fetch();
		
		echo '<p>'.htmlspecialchars($whitePlayer['Username']).' (ELO '.htmlspecialchars($whitePlayer['ELORating']).
		') VS '.htmlspecialchars($blackPlayer['Username']).' (ELO '.htmlspecialchars($blackPlayer['ELORating']).')</p>';
		echo '<p class="watch"><a href="">Titta på</a></p>';
		
		echo "</div>";
		
		$counter++;
	}
	?>
</body>

</html>