<?php
require "../Private/connection.php";

if(!$dbh)
	die();

session_start();
if(!isset($_SESSION['username']) || !isset($_SESSION['userid'])){
	header("Location: login.php");
	die();
}

if(isset($_GET['intent']) && $_GET['intent'] == 'send-challenge' && !empty($_POST['receiverUsername']) && !empty($_POST['private'])){
	
	//Få tag i userid på receiverUserEloRating
	$sql = "SELECT id FROM users WHERE username=?";
	$stmt = $dbh->prepare($sql);
	$success = $stmt->execute([$_POST['receiverUsername']]);
	$receiverUserId = $stmt->fetchColumn();
	
	$sql = "INSERT INTO `challenges` (`senderuserid`, `receiveruserid`, `private`, `sendtime`) VALUES ( :senderuserid, :receiveruserid, :private, :sendtime)";
	$stmt = $dbh->prepare($sql);
	$privateValue;
	if($_POST['private'] == 'false')
		$privateValue = 0;
	else
		$privateValue = 1;
	$success = $stmt->execute(['senderuserid' => $_SESSION['userid'], 'receiveruserid' => $receiverUserId, 'private' => $privateValue, 'sendtime' => date('Y-m-d H:i:s', time())]);
	if($success){
		echo "<p>Utmaningen skickades!</p>";
	}
	else{
		echo "<p>Utmaningen kunde inte skickas.</p>";
	}
}

if(isset($_GET['intent']) && $_GET['intent'] == 'withdraw' && !empty($_GET['challengeid'])){
	$sql = "DELETE FROM `challenges` WHERE `challenges`.`id` = ?";
	$stmt = $dbh->prepare($sql);
	$success = $stmt->execute([$_GET['challengeid']]);
	if($success)
		echo "<p>Utmaningen drogs tillbaka</p>";
	else
		echo "<p>Utmaningen kunde inte dras tillbaka</p>";
}

if(isset($_GET['intent']) && $_GET['intent'] == 'decline' && !empty($_GET['challengeid'])){
	//Ta bort utmaningen
	$sql = "DELETE FROM `challenges` WHERE `challenges`.`id` = ?";
	$stmt = $dbh->prepare($sql);
	$success = $stmt->execute([$_GET['challengeid']]);
	if($success)
		echo "<p>Utmaningen nekades.</p>";
	else
		echo "<p>Utmaningen kunde inte nekas.</p>";
}

function EchoPendingChallenges($dbh){
	
	//Inkommande
	echo "<div>";
	echo "<p>Inkommande</p>";
	$sql = "SELECT * FROM challenges WHERE receiveruserid = ?";
	$stmt = $dbh->prepare($sql);
	$success = $stmt->execute([$_SESSION['userid']]);
	$receivedChallenges = $stmt->fetchAll();
	foreach($receivedChallenges as $receivedChallenge){
		if($receivedChallenge['private']){
			echo "<p>Privat Match</p>";
		}
		
		//Få tag i ELO-Rating och användarnamn på motståndaren
		$sql = "SELECT elorating, username FROM users WHERE id=?";
		$stmt = $dbh->prepare($sql);
		$success = $stmt->execute([$receivedChallenge['senderuserid']]);
		$senderData = $stmt->fetch();
		$senderUserEloRating = $senderData['elorating'];
		$senderUsername = $senderData['username'];
		
		$htmlReadySenderEloRating = htmlspecialchars($senderUserEloRating);
		$htmlReadySenderUsername = htmlspecialchars($senderUsername);
		$htmlReadySendTime = htmlspecialchars($receivedChallenge['sendtime']);
		
		echo "<p>Utmaning från {$htmlReadySenderUsername} (ELO {$htmlReadySenderEloRating}) skickades {$htmlReadySendTime}</p>";
		echo "<a href=\"manage-challenges.php?intent=decline&challengeid={$receivedChallenge['id']}\">Avböj</a>";
		echo "<a href=\"manage-challenges.php?intent=accept&challengeid={$receivedChallenge['id']}\">Acceptera</a>";
	}
	echo "</div>";
	
	//Utgående
	echo "<div>";
	echo "<p>Utgående</p>";
	$sql = "SELECT * FROM challenges WHERE senderuserid = ?";
	$stmt = $dbh->prepare($sql);
	$success = $stmt->execute([$_SESSION['userid']]);
	$sentChallenges = $stmt->fetchAll();
	
	foreach($sentChallenges as $sentChallenge){
		if($sentChallenge['private']){
			echo "<p>Privat Match</p>";
		}
		//Få tag i ELO-Rating och användarnamn på motståndaren
		$sql = "SELECT elorating, username FROM users WHERE id=?";
		$stmt = $dbh->prepare($sql);
		$success = $stmt->execute([$sentChallenge['receiveruserid']]);
		$receiverData = $stmt->fetch();
		$receiverUserEloRating = $receiverData['elorating'];
		$receiverUsername = $receiverData['username'];
		
		$htmlReadyReceiverEloRating = htmlspecialchars($receiverUserEloRating);
		$htmlReadyReceiverUsername = htmlspecialchars($receiverUsername);
		$htmlReadySendTime = htmlspecialchars($sentChallenge['sendtime']);
		
		echo "<p>Utmaning till {$htmlReadyReceiverUsername} (ELO {$htmlReadyReceiverEloRating}) skickades {$htmlReadySendTime}</p>";
		echo "<a href=\"manage-challenges.php?intent=withdraw&challengeid={$sentChallenge['id']}\">Dra tillbaka</a>";
	}
	echo "</div>";
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
		EchoPendingChallenges($dbh);
	?>
	<form method="post" action="manage-challenges.php?intent=send-challenge">
		<label for="receiverUsername">Användarnamn på den du vill skicka utmaningen till</label>
		<input type="text" name="receiverUsername" required /><br>
		<input type="radio" name="private" value="false" checked>Icke-Privat<br>
		<input type="radio" name="private" value="true">Privat<br>
		<input type="submit" value="Skicka"/>
	</form>
</body>

</html>