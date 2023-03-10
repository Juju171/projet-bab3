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
      <button onclick="document.getElementById('id02').style.display='block'" class="my_button">Register</button>

      <button onclick="document.getElementById('id01').style.display='block'" class="my_button">Login</button>
    </div>
  

    <div id="id02" class="modal"> <!--Si on est dans register-->
    
      <form class="modal-content animate" action="login.php" method="post">
        <div class="imgcontainer">
          <span onclick="document.getElementById('id02').style.display='none'" class="close" title="Close Modal">&times;</span>
          <img src="achille.png" alt="Avatar" class="avatar">
        </div>
    
        <div class="container">
            <label class="font1" for="surname"><b>Surname</b></label>
            <input class="font1" type="text" id="surname" placeholder="Enter surname" name="surname" required>
  
            <label class="font1" for="firstname"><b>Firstname</b></label>
            <input class="font1" type="text" id="firstname" placeholder="Enter firstname" name="firstname" required>
    
            <label class="font1" for="status"><b>status</b></label><br>
            <select name="status" id="status">
              <option value="invité">Invité</option>
            </select><br>

            <label class="font1" for="psw"><b>Password</b></label>
            <input class="font1" type="password" id="password" placeholder="Enter Password" name="psw" required>
        </div>

        <div class="flex-parent jc-center">
          <button type="submit" class="login_button" onClick="auth(event)">Register</button> <!--Modifier la fonction auth(event) car ici on a besoin d'une fonction register-->
        </div>
    
        <label class="font1">
          <button type="reset" class="resetbtn">Reset</button>
        </label>
    
        <div class="container" style="background-color:#f1f1f1">
          <button type="button" onclick="document.getElementById('id02').style.display='none'" class="cancelbtn">Cancel</button>
        </div>
      </form>
    </div>

    <div id="id01" class="modal"> <!--Si on est dans login-->
    
      <form class="modal-content animate" action="authenticate.php" method="post"> <!--Faut changer l'action ici-->
        <div class="imgcontainer">
          <span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
          <img src="achille.png" alt="Avatar" class="avatar">
        </div>
    
        <div class="container">
          <label class="font1" for="surname"><b>Surname</b>
          <i class="fas fa-user"></i> </label>
          <input class="font1" type="text" id="surname" placeholder="Enter surname" name="surname" required >
    
          <label class="font1" for="psw"><b>Password</b>
          <i class="fas fa-lock"></i></label>
          <input class="font1" type="password" id="password" placeholder="Enter Password" name="psw" required>
        </div>
        
        <div class="flex-parent jc-center">
          <button type="submit" class="login_button">Login</button>
        </div>
    
        <label class="font1">
          <button type="reset" class="resetbtn">Reset</button>
        </label>
    
        <div class="container" style="background-color:#f1f1f1">
          <button type="button" onclick="document.getElementById('id01').style.display='none'" class="cancelbtn">Cancel</button>
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

//TEST