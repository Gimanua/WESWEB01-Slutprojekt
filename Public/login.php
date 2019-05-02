<?php
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
			$sql = "INSERT INTO `users` (`id`, `username`, `password`, `email`, `elorating`, `passwordresettoken`) VALUES (NULL, :username, :password, :email, '1200', '')";
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
	
	$sql = "SELECT id, password FROM `users` WHERE username = ?";
	$stmt = $dbh->prepare($sql);
	$success = $stmt->execute([$username]);
	if(!$success)
	{
		echo "Felaktigt användarnamn";
		return;
	}
	$row = $stmt->fetch();
	$hashedPassword = $row['password'];
	$userid = $row['id'];
	
	if(password_verify($password, $hashedPassword))
	{
		$_SESSION['username'] = $username;
		$_SESSION['userid'] = $userid;
		header("Location: index.php");
		die();
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
		
		if(mail($email, "Aktivering", "Klicka på länken slutföra kontoregistreringen: http://localhost/login.php?intent=validate&token={$token}"))
			echo "Mail skickat";
		else
			echo "Misslyckades med att skicka mail";
	}
	else
		echo "Registreringen misslyckades!";
	
}

function SetPassword($dbh, $newPassword, $token){
	$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
	$sql = "UPDATE `users` SET `password` = :password WHERE `users`.`passwordresettoken` = :token";
	$stmt = $dbh->prepare($sql);
	$success = $stmt->execute(['password' => $hashedPassword, 'token' => $token]);
	if($success)
		echo "Nytt lösenord är satt.";
}

function ResetPassword($dbh, $email){

	$passwordresettoken = md5($email.time());
	$sql = "UPDATE `users` SET `passwordresettoken` = :passwordresettoken WHERE `users`.`email` = :email LIMIT 1";
	$stmt = $dbh->prepare($sql);
	$stmt->execute(['passwordresettoken' => $passwordresettoken, 'email' => $email]);
	
	if(mail($email, "Återställning av lösenord", "Klicka på länken för att återställa ditt lösenord: http://localhost/login.php?intent=enter-new-password&token={$passwordresettoken}"))
		echo "Mail skickat";
	else
		echo "Misslyckades med att skicka mail";
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
		case 'password-reset':
			if(!empty($_POST['email']))
				ResetPassword($dbh, $_POST['email']);
			break;
		case 'set-password':
			if(!empty($_POST['token']) && !empty($_POST['password']))
				SetPassword($dbh, $_POST['password'], $_POST['token']);
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
	<?php
		if(isset($_GET['intent']) && $_GET['intent'] === 'enter-new-password' && isset($_GET['token'])){
			echo "<p>Formulär för återställande av lösenord</p>
				<form method=\"post\" action=\"login.php?intent=set-password\">
					<label for=\"password\">Nytt lösenord</label><br>
					<input type=\"password\" name=\"password\" required /><br>
					<input type=\"hidden\" name=\"token\" value=\"{$_GET['token']}\" />
					<input type=\"submit\" value=\"Byt lösenord\" />
				</form>";
		}
		else{
			echo <<<STANDARD
				<p>Logga in</p>
				<form method="post" action="login.php?intent=login">
					<label for="username">Användarnamn</label><br>
					<input type="text" name="username" required /><br>
					<label for="password">Lösenord</label><br>
					<input type="password" name="password" required /><br>
					<input type="submit" value="Logga In" />
				</form>
				<p>Glömt lösenord?</p>
				<form method="post" action="login.php?intent=password-reset">
					<label for="email">Email-address för kontot</label><br>
					<input type="text" name="email" required /><br>
					<input type="submit" value="Skicka mail" />
				</form>
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
STANDARD;
		}
	?>
</body>

</html>