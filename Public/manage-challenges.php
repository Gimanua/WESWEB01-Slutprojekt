<?php
require "../Private/connection.php";

if(!$dbh)
	die();

session_start();
if(!isset($_SESSION['username']) || !isset($_SESSION['userid'])){
	header("Location: login.php");
	die();
}

function EchoPendingChallenges($dbh){
	//Hämta skickade
	$sql = "SELECT * FROM challenges WHERE senderuserid = ?";

	//Hämta fådda
	$sql = "SELECT * FROM challenges WHERE receiveruserid = ?";
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
	?>
	<form>
		<label for="receiverUsername">Användarnamn på den du vill skicka utmaningen till</label>
		<input type="text" name="receiverUsername" required /><br>
	</form>
</body>

</html>