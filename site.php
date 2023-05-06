<?php
require_once("config.php");
// We don't have the password or surname info stored in sessions, so instead, we can get the results from the database.
$stmt = $link->prepare('SELECT status FROM tableau WHERE ID = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['ID']);
$stmt->execute();
$stmt->bind_result($status);
$user = $stmt->fetch();


// if(isset($_POST['add_user'])) { //modifier car ça ne fonctionne pas !!!! 
//   // Exécutez la fonction add_finger() ici
//   $response = file_get_contents("http://192.168.4.1/add_finger");
//   echo "Utilisateur ajouté avec succès !";
// }
// if(isset($_POST['match_user'])) {
//   // Exécutez la fonction match_finger() ici
//   $response = file_get_contents("http://192.168.4.1/match_finger");
//   echo "Utilisateur match avec succès !";
// }
// if(isset($_POST['delete_user'])) {
//   // Exécutez la fonction add_finger() ici
//   $response = file_get_contents("http://192.168.4.1/delete_finger");
//   echo "Utilisateur supprimé avec succès !";
$stmt->close();
?>

<script src="scripts.js"></script>

<!DOCTYPE html>
<html>

  <head>
    <meta charset="UTF-8" />
    <title>Projet BAB3</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="style.css" /> 
    <link href="https://fonts.googleapis.com/css?family=Montserrat:900|Work+Sans:300" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer">
  </head>


  <body class="loggedin">
		<nav class="navtop">
			<div>
        <h1 class="font1">Projet BAB 3</h1>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profil</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Se déconnecter</a>
			</div>
		</nav>
      <body>
        <?php if($status=="boss") : ?> <!-- Case 1 : Si on est login en tant que patron-->
          <div class="flex-parent jc-center">
            <a onclick="document.getElementById('id03').style.display='block'" class="my_button" type="submit"><i class="fa-solid fa-pen"></i> Modifier un utilisateur</a>
            <a onclick="document.getElementById('id04').style.display='block'" class="my_button" type="submit" value="delete_user"><i class="fa-solid fa-trash"></i> Delete a user</a>
            <a class="my_button" type="submit" value="match_user"><i class="fa-solid fa-fingerprint"></i> Match test a fingerprint</a>
          </div>

    <div id="id03" class="modal"> <!-- 1. Modifier le statut d'un user -->
    
      <form class="modal-content animate" method="post">
    
        <div class="container">
            <?php
              // Connexion à la base de données
              $servername = "localhost";
              $username = "root";
              $password = "";
              $dbname = "projetbab3";

              $conn = new mysqli($servername, $username, $password, $dbname);
              if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
              }

              // Récupération des données de la base de données
              $sql = "SELECT ID, surname, status FROM tableau";
              $result = $conn->query($sql);

              // Affichage du formulaire
              echo "<form method='post'>";
              echo "<label for='user' class='font1'><i class='fa-solid fa-user'></i><b> Surname : </b></label>";
              echo "<select id='user' name='user'>";
              while($row = $result->fetch_assoc()) {
                echo "<option value='".$row["ID"]."'>".$row["surname"]."</option>";
              }
              echo "</select><br>";
              echo "<label for='status' class='font1'><i class='fa-solid fa-briefcase'></i><b> Status : </b></label>";
              echo "<select id='status' name='status'>";
              echo "<option value='guest'>Guest</option>";
              echo "<option value='employee'>Employee</option>";
              echo "<option value='boss'>Boss</option>";
              echo "</select><br>";
              echo "</form>";

              // Traitement du formulaire
              if (isset($_POST['submit'])) {
                $id = $_POST['user'];
                $status = $_POST['status'];
                $sql = "UPDATE tableau SET status='".$status."' WHERE ID=".$id;
                if ($conn->query($sql) === TRUE) {
                } else {
                  echo "Erreur lors de la modification du statut : " . $conn->error;
                }
              }

              // Fermeture de la connexion à la base de données
              $conn->close();
            ?>
            <div class="flex-parent jc-center">
              <button type="submit" name='submit' value='Modifier le statut' class="login_button">Changer le statut</button>
            </div>
            <div class="container">
              <button type="button" onclick="document.getElementById('id03').style.display='none'" class="cancelbtn">Annuler</button>
            </div>
        </div>
      </form>
    </div>

  <div id="id04" class="modal"> <!-- 2. Supprimer un user et son empreinte-->
    
    <form class="modal-content animate" method="post">
    
    <div class="container">

      <?php
          // Connexion à la base de données
          $host = "localhost";
          $user = "root";
          $password = "";
          $database = "projetbab3";

          $conn = mysqli_connect($host, $user, $password, $database);

          // Vérifier la connexion
          if (!$conn) {
              die("La connexion à la base de données a échoué : " . mysqli_connect_error());
          }

          // Supprimer l'utilisateur
          if (isset($_POST['delete'])) {
            $user_id = $_POST['user_id'];

            $sql = "DELETE FROM tableau WHERE ID='$user_id'";
            if (mysqli_query($conn, $sql)) {
            } else {
              echo "Erreur : " . mysqli_error($conn);
            }
          }

          // Afficher la liste des utilisateurs
          $sql = "SELECT * FROM tableau";
          $result = mysqli_query($conn, $sql);

          if (mysqli_num_rows($result) > 0) {
              echo "<form action='' method='POST'>";
              echo "<label for='user' class='font1'><i class='fa-solid fa-user'></i><b> Surname : </b></label>";
              echo "<select name='user_id'>";
              while ($row = mysqli_fetch_assoc($result)) {
                  echo "<option value='" . $row['ID'] . "'>" . $row['surname'] . "</option>";
              }
              echo "</select>";
              echo "</form>";
          } else {
              echo "Aucun utilisateur trouvé.";
          }


          // Fermer la connexion
          mysqli_close($conn);
        ?>
        <div class="flex-parent jc-center">
          <button type="submit" name='delete' value='delete user' class="login_button">Supprimer un utilisateur</button>
        </div>
        <div class="container">
          <button type="button" onclick="document.getElementById('id04').style.display='none'" class="cancelbtn">Annuler</button>
        </div>
  </div>
  </form>
    </div>
          
    <?php elseif ( $status == "guest" ) : ?> <!-- Case 2 : Si on est login en tant qu'invité-->
      <div class="flex-parent jc-center">
        <a onclick="document.getElementById('id05').style.display='block'" class="my_button" target="myIframe" type="submit" value="delete_account"><i class="fa-solid fa-fingerprint"></i> Delete the account</a>
      </div>
    
    <div id="id05" class="modal"> <!--Supprimer un user et son empreinte-->
    
    <form class="modal-content animate" method="post">
    
    <div class="container">
      <?php
          // Connexion à la base de données
          $host = "localhost";
          $user = "root";
          $password = "";
          $database = "projetbab3";

          $conn = mysqli_connect($host, $user, $password, $database);

          // Vérifier la connexion
          if (!$conn) {
              die("La connexion à la base de données a échoué : " . mysqli_connect_error());
          }

          // Supprimer l'utilisateur
          if (isset($_POST['delete'])) {
            $user_id = $_SESSION['ID'];

            $sql = "DELETE FROM tableau WHERE ID='$user_id'";
            if (mysqli_query($conn, $sql)) {
              header('Location: http://localhost/projet-bab3-website/logout.php'); //ça fonctionne pas (redirection vers la page de connexion)
            } else {
              echo "Erreur : " . mysqli_error($conn);
            }
          }

          // Fermer la connexion
          mysqli_close($conn);
        ?>
        <div class="flex-parent jc-center">
          <button type="submit" name='delete' value='delete user' class="login_button">Delete user</button>
        </div>
        <div class="container">
          <button type="button" onclick="document.getElementById('id05').style.display='none'" class="cancelbtn">Cancel</button>
        </div>
  </div>

    <?php elseif ( $status == "employee" ) : ?> <!-- Case 3 : Si on est login en tant qu'employé--><!--Il faut ajouter à l'ID qu'on souhaite sur le capteur -->
      <div class="flex-parent jc-center">
	      <a class="my_button" target="myIframe" type="submit" value="add_user"><i class="fa-solid fa-fingerprint"></i> Add a fingerprint</a>
        <a class="my_button" target="myIframe" type="submit" value="match_user"><i class="fa-solid fa-fingerprint"></i> Match test a fingerprint</a>
	      <a onclick="document.getElementById('id05').style.display='block'" class="my_button" target="myIframe" type="submit" value="delete_account"><i class="fa-solid fa-trash"></i> Delete the account</a>
      </div>

      <div id="id05" class="modal"> <!--Supprimer un user et son empreinte-->
    
    <form class="modal-content animate" method="post">
    
    <div class="container">
      <?php
          // Connexion à la base de données
          $host = "localhost";
          $user = "root";
          $password = "";
          $database = "projetbab3";

          $conn = mysqli_connect($host, $user, $password, $database);

          // Vérifier la connexion
          if (!$conn) {
              die("La connexion à la base de données a échoué : " . mysqli_connect_error());
          }

          // Supprimer l'utilisateur
          if (isset($_POST['delete'])) {
            $user_id = $_SESSION['ID'];

            $sql = "DELETE FROM tableau WHERE ID='$user_id'";
            if (mysqli_query($conn, $sql)) {
              header('Location: http://localhost/projet-bab3-website/logout.php'); //ça fonctionne pas (redirection vers la page de connexion)
            } else {
              echo "Erreur : " . mysqli_error($conn);
            }
          }

          // Fermer la connexion
          mysqli_close($conn);
        ?>
        <div class="flex-parent jc-center">
          <button type="submit" name='delete' value='delete user' class="login_button">Delete user</button>
        </div>
        <div class="container">
          <button type="button" onclick="document.getElementById('id05').style.display='none'" class="cancelbtn">Cancel</button>
        </div>
  </div>

        <?php endif; ?>
      </body>
    </div>
  </body>
</html>
