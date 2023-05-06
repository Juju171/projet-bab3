<?php
session_start();

$conn = mysqli_connect('localhost', 'root', '', 'projet_dc');

if (isset($_POST['utilisateur'])){

  $username = stripslashes($_REQUEST['utilisateur']);
  $username = mysqli_real_escape_string($conn, $username);
  $password = stripslashes($_REQUEST['mdp']);
  $password = mysqli_real_escape_string($conn, $password);
  $query = "SELECT * FROM `utilisateur` WHERE NomUtilisateur='$username' and MotDePasse='$password'";
  $result = mysqli_query($conn,$query) or die(mysqli_error($conn));
  $rows = mysqli_num_rows($result);
  if($rows==1){
      $_SESSION['username'] = $username;
      header("Location: index.php");
  }else{
    $message = "Le nom d'utilisateur ou le mot de passe est incorrect.";
	echo $message;
  }
}
?>