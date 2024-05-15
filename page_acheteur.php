<?php

@include 'lien.php';

session_start();

if(!isset($_SESSION['acheteur_nom'])){
  header('location:connexion.php');
}
function connectToDatabase() {
  $host = 'localhost';
  $dbname = 'test';
  $username = 'root';
  $password = '';

  try {
    return new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
}

function getCommandesByUtilisateur_id($vendeur_id) {
  $db = connectToDatabase();
  $stmt = $db->prepare('SELECT * FROM commande WHERE Utilisateur_ID =?');
  $stmt->execute([$vendeur_id]);
  return $stmt->fetchAll();
}
$commandes = getCommandesByUtilisateur_id($_SESSION['id']);


?>

<!DOCTYPE html>
<html lang="fr">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Acheteur</title>

   <link rel="stylesheet" href="page_acheteur.css">
  <link rel="stylesheet" href="themes.css">


  <style>
    /* Styles existants */

  </style>
</head>
<body>

<ul class="menu">
  <li><a href="index.php#home">Accueil</a></li>
  <li><a href="validation.php">Panier</a></li>
  <li class="dropdown">
    <a href="#" class="dropbtn">Profil</a>
    <div class="dropdown-content">
      <?php
      if (isset($_SESSION['acheteur_nom']) || isset($_SESSION['vendeur_nom'])) {

        if($_SESSION['row'] !== null){
          if($_SESSION['row']['type_uti'] === 'vendeur'){
            echo '<a href="page_vendeur.php">Profil</a>';
          } else {
            echo '<a href="page_acheteur.php">Profil</a>';
          }
        }
        echo '<a href="deconnexion.php">Déconnexion</a>';
      } else {

        echo '<a href="connexion.php">Connexion</a>';
        echo '<a href="inscription.php">Inscription</a>';
      }

      ?>
    </div>
  </li>
</ul>

<div class="container">
  <div class="content">
    <h1>Bienvenue sur votre profil <span><?php echo $_SESSION['acheteur_nom']?></span></h1>
    <a href="index.php" class="btn">logout</a>
    <div class="tab">
      <button class="tablinks" onclick="openTab(event, 'Historique')">Historique des commandes</button>
    </div>

    <div id="Historique" class="tabcontent">
      <h2>Mes achats:</h2>
      <div class="articles-container">
        <?php foreach ($commandes as $commande) {?>
          <div class="article-card">
            <h3><a href="commande.php?id=<?php echo $commande['id']?>">Commande n°:<?php echo $commande['id']?></a></h3>
            <p>Date: <?php echo $commande['date_commande']?></p>
            <p>Prix: <?php echo $commande['Prix_Total']?> €</p>
          </div>
        <?php }?>
      </div>
    </div>

  </div>
</div>

<script>
  function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
  }

  document.getElementsByClassName("tablinks")[0].click();
</script>

<script src="them-switch.js"></script>

</body>
</html>
