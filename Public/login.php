<?php
//secret q!y?^}BA:5#(\!+f~:}9$j;>Q'BE6a&UPQG#r2J{Rn<.A8<fnz:($_N*2;D5"_9h
require "../Private/connection.php";

//Template för 'Tillbaka till huvudsidan'
if(!$dbh)
	die();

session_start();

function Validate($dbh, $token)
{
	$sql = "SELECT * FROM `nonactivatedusers` WHERE token = ? LIMIT 1";
	$stmt = $dbh->prepare($sql);
	$stmt->execute([$token]);
	if($row = $stmt->fetch())
	{
		if(date('Y-m-d H:i:s', time()) <= $row['activationvalid'])
		{
			//Man är i tid
			//Skapa användaren i 'users'
			$sql = "INSERT INTO `users` (`id`, `username`, `password`, `email`, `elorating`) VALUES (NULL, :username, :password, :email, '1200')";
			$stmt = $dbh->prepare($sql);
			$success = $stmt->execute(['username' => $row['username'], 'password' => $row['password'], 'email' => $row['email']]);
			
			//Kolla om man lyckades
			if(!$success)
			{
				echo "Misslyckades att skapa användare.";
				return;
			}
			echo "Lyckades att skapa användare.";
			
			//Ta bort användaren från 'nonactivatedusers'
			$sql = "DELETE FROM `nonactivatedusers` WHERE token = ?";
			$stmt = $dbh->prepare($sql);
			$stmt->execute([$token]); //Användaren behöver inte veta om detta lyckades eller ej
			
		}
		else
		{
			echo "Tiden har tyvärr gått ut. Du behöver registrera dig igen.";
			//Det har gått mer än 15 minuter
			//Ta bort användaren från 'nonactivatedusers'
			$sql = "DELETE FROM `nonactivatedusers` WHERE token = ?";
			$stmt = $dbh->prepare($sql);
			$stmt->execute([$token]); //Användaren behöver inte veta om detta lyckades eller ej
		}
	}
}

function Login($dbh, $username, $password)
{
	/* Denna delen behövs nog inte
	
	//Kolla om användarnamnet finns över huvud taget
	$sql = "SELECT 1 FROM `users` WHERE `username` = ? LIMIT 1";
	$stmt = $dbh->prepare($sql);
	$stmt->execute([$username]);
	if(!$stmt->fetchColumn())//Det finns ingen
	{
		echo "Användarnamnet du angav finns ej.";
		return;
	}
	*/
	
	$sql = "SELECT password FROM `users` WHERE username = ?";
	$stmt = $dbh->prepare($sql);
	$stmt->execute([$username]);
	$hashedPassword = $stmt->fetchColumn();
	if(!$hashedPassword)
	{
		echo "Felaktigt användarnamn";
		return;
	}
	if(password_verify($password, $hashedPassword))
	{
		echo "Inloggningen lyckades";
		$_SESSION['username'] = $username;
	}
	else
	{
		echo "Fel lösenord.";
	}
}

function Register($dbh, $username, $password, $email)
{
	//Kolla om det redan finns någon med samma användarnamn i users, sedan nonactivatedusers
	$sql = "SELECT 1 FROM `users` WHERE `username` = ? LIMIT 1";
	$stmt = $dbh->prepare($sql);
	$stmt->execute([$username]);
	if($stmt->fetchColumn())//Det finns redan någon
	{
		echo "Detta användarnamnet är redan i bruk!";
		return;
	}
	
	$sql = "SELECT 1 FROM `nonactivatedusers` WHERE `username` = ? LIMIT 1";
	$stmt = $dbh->prepare($sql);
	$stmt->execute([$username]);
	if($stmt->fetchColumn())//Det finns redan någon
	{
		echo "Detta användarnamnet är redan i bruk!";
		return;
	}
	
	//Kolla om emailen redan används i users, sedan nonactivatedusers
	$sql = "SELECT 1 FROM `users` WHERE `email` = ? LIMIT 1";
	$stmt = $dbh->prepare($sql);
	$stmt->execute([$email]);
	if($stmt->fetchColumn())//Det finns redan någon
	{
		echo "Emailen som angavs är redan i bruk!";
		return;
	}
	
	$sql = "SELECT 1 FROM `nonactivatedusers` WHERE `email` = ? LIMIT 1";
	$stmt = $dbh->prepare($sql);
	$stmt->execute([$email]);
	if($stmt->fetchColumn())//Det finns redan någon
	{
		echo "Emailen som angavs är redan i bruk!";
		return;
	}
	
	$token = md5($email.time());
	
	$sql = "INSERT INTO `nonactivatedusers` (`id`, `username`, `password`, `email`, `activationvalid`, `token`) VALUES (NULL, :username, :password, :email, :activationValid, :token)";
	$activationValid = date('Y-m-d H:i:s', time() + 900); //15 minuter
	$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
	$stmt = $dbh->prepare($sql);
	$success = $stmt->execute(['username' => $username, 'password' => $hashedPassword, 'email' => $email, 'activationValid' => $activationValid, 'token' => $token]);
	
	if($success)
	{
		echo "Registreringen lyckades, du har nu 15 minuter på dig att bekräfta kontot med en länk som skickats till din email.";
		
		if(mail($email, "Activation", "Klicka på länken slutföra kontoregistreringen: http://localhost/login.php?intent=validate&token={$token}"))
			echo "Mail skickat";
		else
			echo "Misslyckades med att skicka mail";
	}
	else
		echo "Registreringen misslyckades!";
	
}

if(isset($_GET['intent']))
{
	echo "<p>";
	switch($_GET['intent'])
	{	
		case 'login':
			if(!empty($_POST['username']) && !empty($_POST['password']))
				Login($dbh, $_POST['username'], $_POST['password']);
			break;
		case 'register':
			if(!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['email']))
				Register($dbh, $_POST['username'], $_POST['password'], $_POST['email']);
			break;
		case 'validate':
			if(!empty($_GET['token']))
				Validate($dbh, $_GET['token']);
			break;
	}
	echo "</p>";
}

//Logga in-formulär
//Om man redan är inloggad så ska session rensas och ett nytt startas

//Registrera-formulär
?>
<!DOCTYPE html>
<html lang="sv">

<head>
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="CSS/login.css"/>
</head>

<body>
	<a href="index.php">Tillbaka till huvudsidan</a>
	<p>Logga in</p>
	<form method="post" action="login.php?intent=login">
		<label for="username">Användarnamn</label><br>
		<input type="text" name="username" required /><br>
		<label for="password">Lösenord</label><br>
		<input type="password" name="password" required /><br>
		<input type="submit" value="Logga In" />
	</form>
	<!--Glömt lösenord?-->
	<p>Registrera</p>
	<form method="post" action="login.php?intent=register">
		<label for="username">Användarnamn</label><br>
		<input type="text" name="username" required /><br>
		<label for="password">Lösenord</label><br>
		<input type="password" name="password" required /><br>
		<label for="email">Email</label><br>
		<input type="text" name="email" required /><br>
		<input type="submit" value="Registrera" />
	</form>
</body>

</html>