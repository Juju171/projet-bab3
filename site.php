<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
 	header('Location: login.php');
	exit;
}
?>

<script src="scripts.js"></script>

<!DOCTYPE html>
<html>

    <head>
      <meta charset="UTF-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <link rel="stylesheet" type="text/css" href="style.css" /> 
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
    </head>


    <body class="loggedin">
		<nav class="navtop">
			<div>
        <h1 class="font1">Projet BAB 3</h1>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="flex-parent jc-center">
      <body>
        <br><a class="my_button" target="myIframe" href="add_finger">Add a fingerprint</a><a class="my_button" target="myIframe" href="match_finger">Match test a fingerprint</a><br>
      </body>
    </div>
    <div class="flex-parent jc-center">
      <body>
        Add a fingerprint <a href="add_finger" target="myIframe">Fingerprint added</a><br>
        Finger state :<iframe name="myIframe" width="100" height="25" frameBorder="0"><br>
        <hr>
      </body>
    </div>
	</body>

</html>