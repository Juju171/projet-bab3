<script src="scripts.js"></script>

<!DOCTYPE html>
  <html>
    <head>
      <meta charset="UTF-8" />
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <link rel="stylesheet" type="text/css" href="style.css" /> 
    </head>

  
    <div class="flex-parent jc-center"> <!--Boutons register et login-->
      <button onclick="document.getElementById('id02').style.display='block'" class="my_button">S'enregistrer</button>

      <button onclick="document.getElementById('id01').style.display='block'" class="my_button">Se connecter</button>
    </div>
  

    <div id="id02" class="modal"> <!--Si on est dans register-->
    
      <form class="modal-content animate" action="login.php" method="post">
        <div class="imgcontainer">
          <span onclick="document.getElementById('id02').style.display='none'" class="close" title="Close Modal">&times;</span>
          <img src="user icon.png" alt="Avatar" class="avatar">
        </div>
    
        <div class="container">
            <label class="font1" for="firstname"><b>Prénom</b></label>
            <input class="font1" type="text" id="firstname" placeholder="Entrez votre prénom" name="firstname" required>

            <label class="font1" for="surname"><b>Nom</b></label>
            <input class="font1" type="text" id="surname" placeholder="Entrez votre nom" name="surname" required>
    
            <label class="font1" for="status"><b>Statut</b></label><br>
            <select name="status" id="status">
              <option value="guest">Invité</option>
            </select><br>

            <label class="font1" for="psw"><b>Mot de passe</b></label>
            <input class="font1" type="password" id="password" placeholder="Entrez votre mot de passe" name="psw" required>
        </div>

        <div class="flex-parent jc-center">
          <button type="submit" class="login_button" onClick="auth(event)">S'enregistrer</button>
        </div>
    
        <label class="font1">
          <button type="reset" class="resetbtn">Réinitialiser</button>
        </label>
    
        <div class="container" style="background-color:#f1f1f1">
          <button type="button" onclick="document.getElementById('id02').style.display='none'" class="cancelbtn">Annuler</button>
        </div>
      </form>
    </div>

    <div id="id01" class="modal"> <!--Si on est dans login-->
    
      <form class="modal-content animate" action="authenticate.php" method="post">
        <div class="imgcontainer">
          <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
          <img src="user icon.png" alt="Avatar" class="avatar">
        </div>
    
        <div class="container">
          <label class="font1" for="surname"><b>Nom</b>
          <i class="fas fa-user"></i> </label>
          <input class="font1" type="text" id="surname" placeholder="Entrez votre nom" name="surname" required >
    
          <label class="font1" for="psw"><b>Mot de passe</b>
          <i class="fas fa-lock"></i></label>
          <input class="font1" type="password" id="password" placeholder="Entrez votre mot de passe" name="psw" required>
        </div>
        
        <div class="flex-parent jc-center">
          <button type="submit" class="login_button">Se connecter</button>
        </div>
    
        <label class="font1">
          <button type="reset" class="resetbtn">Réinitialiser</button>
        </label>
    
        <div class="container" style="background-color:#f1f1f1">
          <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Annuler</button>
        </div>
      </form>
    </div>

</html>


<?php
if($_POST['surname'] and $_POST['firstname'] and $_POST['status'] and $_POST['psw']) {
		
	$surname=$_POST['surname'];
  $firstname=$_POST['firstname'];
	$status=$_POST['status'];
	$psw=$_POST['psw'];

	$base = new PDO ('mysql:host=localhost;dbname=projetbab3', 'root', '');

	$sql = "INSERT INTO `tableau` (`ID`, `surname`, `firstname`, `status`, `psw`) VALUES (NULL, '$surname', '$firstname', '$status', '$psw')";
	$Resultat = $base->exec($sql);

}
?>
