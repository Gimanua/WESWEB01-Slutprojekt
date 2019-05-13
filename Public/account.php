<?php

require "../Private/connection.php";

if(!$dbh)
	die();

session_start();
if(!isset($_SESSION['username']) || !isset($_SESSION['userid'])){
	header("Location: login.php");
	die();
}

?>
<!DOCTYPE html>
<html lang="sv">

<head>
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="CSS/account.css"/>
</head>

<body>
	<a href="index.php">Tillbaka till huvudsidan</a>
	<?php
		$sql = "SELECT `username`, `email`, `elorating` FROM `users` WHERE `id` = ? LIMIT 1";
		$stmt = $dbh->prepare($sql);
		$stmt->execute([$_SESSION['userid']]);
		$row = $stmt->fetch();
		
		$htmlUsername = htmlspecialchars($row['username']);
		$htmlEmail = htmlspecialchars($row['email']);
		$htmlEloRating = htmlspecialchars($row['elorating']);
		echo "<p>Användarnamn: {$htmlUsername}</p>";
		echo "<p>Email: {$htmlEmail}</p>";
		echo "<p>Elo-rating: {$htmlEloRating}</p>";
	?>
	<p>Ändra uppgifter:</p>
	<form method="post" action="account.php?intent=update-info">
		<label for="email">Ny email</label><br>
		<input type="text" /><br>
		<label for="password">Nytt lösenord</label><br>
		<input type="password" /><br>
		<input type="submit" value="Ändra" />
	</form>
</body>

</html>