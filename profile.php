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

$ID = isset($ID) ? $ID : '';
$surname = isset($surname) ? $surname : '';
$firstname = isset($firstname) ? $firstname : '';
$status = isset($status) ? $status : '';
$psw = isset($psw) ? $psw : '';

// We don't have the password or surname info stored in sessions, so instead, we can get the results from the database.
$stmt = $link->prepare('SELECT psw, surname FROM tableau WHERE ID = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['ID']);
$stmt->execute();
$stmt->bind_result($psw, $surname);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>Projet BAB 3</h1>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Profile Page</h2>
			<div>
				<p>Your account details are below:</p>
				<table>
        <tr>
						<td>ID:</td>
						<td><?=$ID?></td>
					</tr>
					<tr>
						<td>Surname:</td>
						<td><?=$_SESSION['surname']?></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><?=$psw?></td>
					</tr>
					<tr>
						<td>Firstname:</td>
						<td><?=$firstname?></td>
					</tr>
          <tr>
						<td>Status:</td>
						<td><?=$status?></td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>