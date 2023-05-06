<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: login.php');
	exit;
}

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'projetbab3';
$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}


// We don't have the password or surname info stored in sessions, so instead, we can get the results from the database.
$stmt = $link->prepare('SELECT psw, surname,firstname,status FROM tableau WHERE ID = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['ID']);
$ID = intval($_SESSION['ID']);
$stmt->execute();
$stmt->bind_result($psw, $surname,$firstname,$status);
$user = $stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profil</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link href="https://fonts.googleapis.com/css?family=Montserrat:900|Work+Sans:300" rel="stylesheet">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1 class="font1"><b>Projet BAB 3</b></h1>
				<a href="site.php"><i class="fa-solid fa-house"></i>Page d'acceuil</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Se déconnecter</a>
			</div>
		</nav>
		<div class="content">
			<h2 class="font1"><b>Profil</b></h2>
			<div>
				<p class="font1">Vos détails : </p>
					<table class="font1">
						<tr>
							<td>ID : </td>
							<td><?=$ID?></td><br>
						</tr>
						<tr>
							<td>Nom : </td>
							<td><?=$surname?></td><br>
						</tr>
						<tr>
							<td>Prénom : </td>
							<td><?=$firstname?></td><br>
						</tr>
						<tr>
							<td>Statut : </td>
							<td><?=$status?></td><br>
						</tr>
						<tr>
							<td>Mot de passe : </td>
							<td><?=$psw?></td><br>
						</tr>
				</table>
			</div>
		</div>
	</body>
</html>
