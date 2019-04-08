<?php

require "../Private/connection.php";

//Template för 'Tillbaka till huvudsidan'

//Logga in-formulär
//Om man redan är inloggad så ska session rensas och ett nytt startas

//Registrera-formulär
?>
<!DOCTYPE html>
<html lang="sv">

<head>
	<meta charset="utf-8"/>
	<link rel="stylesheet" href="../login.css"/>
</head>

<body>
	<a href="index.php">Tillbaka till huvudsidan</a>
	<p>Logga in</p>
	<form>
	</form>
	<p>Registrera</p>
	<form>
		<label for="username">Användarnamn</label><br>
		<input type="text" name="username" required /><br>
		<label for="password">Lösenord</label><br>
		<input type="password" name="password" required /><br>
		<label for="email">Email</label><br>
		<input type="text" name="email" required /><br>
		<input type="submit" />
	</form>
</body>

</html>