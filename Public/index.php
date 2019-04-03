<?php

require "../Private/connection.php";

if(!$dbh)
	die();
?>
<!DOCTYPE html>
<html lang="sv">

<head>
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="../index.css"/>
</head>

<body>
	<?php
	session_start();
	if(isset($_SESSION['username'])){
		//Kolla om man är admin
	}
	else{
		echo '<a href="login.php">Logga in / Registrera</a>';
	}
	
	echo '<p>Pågående partier</p>';
	$sql = "SELECT ID, FEN, ImageURL, BlackUserID, WhiteUserID FROM Games WHERE Private=FALSE ORDER BY AverageELORating DESC";
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
		$sql = "SELECT Username, ELORating FROM Users WHERE ID=? OR ID=?";
		$playerstmt = $dbh->prepare($sql);
		$playerstmt->execute([$row['WhiteUserID'], $row['BlackUserID']]);
		$players = $playerstmt->fetchAll();
		
		
		echo "<p>{$row['ID']}</p>";
		echo '<p><a href="">Titta på</a></p>';
		
		echo "</div>";
		
		$counter++;
	}
	
	//Bilder på partier
	?>
</body>

</html>