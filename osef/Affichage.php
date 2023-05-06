<?php
  // Initialiser la session
  session_start();
  // Vérifiez si l'utilisateur est connecté, sinon redirigez-le vers la page de connexion
  if(!isset($_SESSION["username"])){
    header("Location: login.html");
    exit(); 
  }
?>

<html lang="fr">

	<head>
		<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Roboto'><link rel="stylesheet" href="./style.css">
		<style>
			ul {
			  list-style-type: none;
			  margin: 0;
			  padding: 0;
			  overflow: hidden;
			  background-color: #333;
			}

			li {
			  float: left;
			}

			li a {
			  display: block;
			  color: white;
			  text-align: center;
			  padding: 14px 16px;
			  text-decoration: none;
			}

			li a:hover:not(.active) {
			  background-color: #111;
			}

			.active {
			  background-color: #04AA6D;
			}
		</style>

		<header>
			<ul>
			  <li><a href="index.php">Home</a></li>
			  <li><a href="Affichage.php">Affichage</a></li>
			  <li><a href="Ajout.php">Ajout</a></li>
			  <li><a href="Suppression.php">Suppression</a></li>
			  <li><a href="Modification.php">Modification</a></li>
			  <li><a href="Assignation.php">Assignation</a></li>
			  <li><a href="Recherche.php">Recherche</a></li>
			  <li style="float:right"><a class="active" href='logout.php'>Déconnexion</a></li>
			</ul>
		</header>
		<center> <h1 style = middle> Affichage </h1> </center>
	</head>
	
	<body>
	
		<pre>					
			<form action = 'affichageEmploye.php' method = 'post'>     
				 <h3> Employés actifs        <input type = 'submit' value = ' Afficher '> </h3> 
			</form>
			<form action = 'affichagePasActif.php' method = 'post'>     
				 <h3> Employés non actifs    <input type = 'submit' value = ' Afficher '> </h3> 
			</form>
			<form action = 'affichageVehicule.php' method = 'post'>     
				 <h3> Véhicules              <input type = 'submit' value = ' Afficher '> </h3> 
			</form>
			<form action = 'affichageChantier.php' method = 'post'>     
				 <h3> Chantiers              <input type = 'submit' value = ' Afficher '> </h3> 
			</form>
			<form action = 'affichageBat.php' method = 'post'>     
				 <h3> Bâtiments              <input type = 'submit' value = ' Afficher '> </h3> 
			</form>	
			<form action = 'affichageMetier.php' method = 'post'>     
				 <h3> Métiers             <input type = 'submit' value = ' Afficher '> </h3> 
			</form>	
		</pre>
		<pre>
			<h3>Afficher les employés sur un chantier </h3>
			<form action = 'affichEmployeChantier.php' method = 'post'>   
			Chantier désiré :   <?php
												$base = new PDO('mysql:host=localhost;dbname=projet_dc','root','');
													
												$sql = "SELECT * FROM chantier";
												$resultat=$base->query($sql);

												echo '<select name="idchantier">';
												while ($ligne = $resultat->fetch(PDO::FETCH_ASSOC)){
													$tmp_id = $ligne["IdChantier"];
													$tmp_name = "rue de/du " . $ligne['Rue'] . " n° " . $ligne['NRue'] . " " . $ligne['Ville'];
													echo "<option value='$tmp_id'>$tmp_name</option>";
													echo"Hello";
												}

												echo '</select>';
											?> 
						</br>
						<input type = 'submit' value = '  Affichage '>
			</form>
			</pre>

			<pre>
			<h3>Afficher les employés sur un batiment </h3>
			<form action = 'affichEmployeBatiment.php' method = 'post'>   
			Bâtiment désiré :      <?php
													$base = new PDO('mysql:host=localhost;dbname=projet_dc','root','');
														
													$sql = "SELECT * FROM batiment";
													$resultat=$base->query($sql);

													echo '<select name="idbat">';
													while ($ligne = $resultat->fetch(PDO::FETCH_ASSOC)){
														$tmp_id = $ligne["IdBatiment"];
														$tmp_name = $ligne['IdBatiment'] . "  |  " . $ligne['nbreEtages'] . " étages";
														echo "<option value='$tmp_id'>$tmp_name</option>";
														echo"Hello";
													}

													echo '</select>';
												?> 
							</br>
							<input type = 'submit' value = '  Affichage '>
			</form>
		</pre>
	
	</body>

</html>