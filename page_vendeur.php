<?php

@include 'lien.php';

session_start();

if(!isset($_SESSION['vendeur_nom'])){
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

?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vendeur</title>

  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="themes.css">

  <style>
    /* Styles existants */
    body{
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    body.light-theme {
      background-color: #FFF !important;
      color: #121010 !important;
    }

    /* Styles pour le thème sombre */
    body.dark-theme {
      background-color: #000 !important;
      color: #d3d3d3 !important;
    }

    .menu {
      display: flex;
      justify-content: space-between;
      list-style-type: none;
      margin: 0;
      padding: 0;
      background-color: #333;
    }
    .logo {
      width: 100px;
      height: auto;
    }

    .menu li {
      display: inline;
    }

    .menu li a {
      display: block;
      color: #938a8a;
      text-align: center;
      padding: 14px 16px;
      text-decoration: none;
    }

    .menu li a:hover {
      background-color: #111;
    }

    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background-color: #f9f9f9;
      min-width: 160px;
      box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
      z-index: 1;
    }

    .dropdown-content a {
      color: black;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      text-align: left;
    }

    .dropdown-content a:hover {background-color: #f1f1f1}

    .dropdown:hover .dropdown-content {
      display: block;
    }
    /* Styles pour la barre de recherche */
    form {
      margin-bottom: 20px;
    }

    input[type="text"] {
      padding: 10px;
      border: none;
      border-radius: 5px;
      width: 80%;
    }

    input[type="submit"] {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      background-color: #333;
      color: white;
      cursor: pointer;
    }


    /* Style des onglets */
    .tab {
      display: flex;
      justify-content: space-between;
      margin: 0;
      padding: 0;
      background-color: #f1f1f1;
      border-bottom: 1px solid #ccc;
    }

    /* Style des boutons d'onglets */
    .tab button {
      background-color: inherit;
      border: none;
      outline: none;
      cursor: pointer;
      padding: 14px 16px;
      transition: 0.3s;
      font-size: 1em;
      flex-grow: 1;
      text-align: center;
    }

    /* Change la couleur de fond des boutons d'onglets lorsqu'ils sont survolés */
    .tab button:hover {
      background-color: #ddd;
    }

    /* Style de l'onglet actif */
    .tab button.active {
      background-color: #ccc;
      border-bottom: 2px solid #333;
    }

    /* Style du contenu de l'onglet */
    .tabcontent {
      display: none;
      padding: 20px;
      border: 1px solid #ccc;
      border-top: none;
      animation: fadeEffect 1s; /* Fading effect takes 1 second */
    }

    /* Fading animation */
    @keyframes fadeEffect {
      from {opacity: 0;}
      to {opacity: 1;}
    }

    /* Style pour les articles */
    .article-card {
      border: 1px solid #ccc;
      border-radius: 5px;
      margin: 10px;
      padding: 10px;
      width: 250px;
      box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
      transition: 0.3s;
    }

    .article-card:hover {
      box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
    }

    .article-card img {
      width: 100%;
      height: auto;
    }

    .article-card h3 {
      margin: 10px 0;
    }

    .article-card p {
      color: #d3d3d3;
    }
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
        // Si l'utilisateur est connecté
        if($_SESSION['row'] !== null){
          if($_SESSION['row']['type_uti'] === 'vendeur'){
            echo '<a href="page_vendeur.php">Profil</a>';
          } else {
            echo '<a href="page_acheteur.php">Profil</a>';
          }
        }
        echo '<a href="deconnexion.php">Déconnexion</a>';
      } else {
        // Si l'utilisateur n'est pas connecté
        echo '<a href="connexion.php">Connexion</a>';
        echo '<a href="inscription.php">Inscription</a>';
      }


      $vendeur_id = $_SESSION['id'];


      function getArticlesByVendeurId($vendeur_id) {
        $db = connectToDatabase();
        $stmt = $db->prepare('SELECT * FROM articles WHERE Vendeur_id =?');
        $stmt->execute([$vendeur_id]);
        return $stmt->fetchAll();
      }

      function getCommandesByUtilisateur_id($vendeur_id) {
        $db = connectToDatabase();
        $stmt = $db->prepare('SELECT * FROM commande WHERE Utilisateur_ID =?');
        $stmt->execute([$vendeur_id]);
        return $stmt->fetchAll();
      }

      function getAchatVendeur_ID($vendeur_id) {
        $db = connectToDatabase();
        $stmt = $db->prepare('SELECT * FROM achats WHERE Vendeur_id =?');
        $stmt->execute([$vendeur_id]);
        return $stmt->fetchAll();
      }

      $articles = getArticlesByVendeurId($vendeur_id);
      $commandes = getCommandesByUtilisateur_id($vendeur_id);
      $ventes = getAchatVendeur_ID($vendeur_id);
      ?>
    </div>
  </li>
</ul>



<div class="container">
  <div class="content">
    <h3>Bonjour Vendeur</h3>
    <h1>Bienvenue <span><?php echo $_SESSION['vendeur_nom']?></span></h1>
    <a href="index.php" class="btn">logout</a>
    <a href="ajouter_article.php" class="btn">Ajouter un article</a>
    <div class="tab">
      <button class="tablinks" onclick="openTab(event, 'Vente')">Objets en vente</button>
      <button class="tablinks" onclick="openTab(event, 'Historique')">Historique des commandes</button>
      <button class="tablinks" onclick="openTab(event, 'Vendus')">Objets vendus</button>
    </div>

    <div id="Vente" class="tabcontent">
      <h2>Mes articles en vente:</h2>
      <div class="articles-container">
        <?php foreach ($articles as $article) {?>
          <div class="article-card">
            <h3><?php echo $article['Nom']?> <img src="<?php echo $article['Photo']?>" alt="<?php echo $article['Nom']?>"></h3>
            <p>Prix: <?php echo $article['Prix']?> €</p>
            <p>Stock: <?php echo $article['Stock']?></p>
          </div>
        <?php }?>
      </div>
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
    <div id="Vendus" class="tabcontent">
      <h2>Mes ventes:</h2>
      <div class="articles-container">
        <?php foreach ($ventes as $vente) {?>
          <div class="article-card">
            <h3><?php echo $vente['Nom']?> <img src="<?php echo $vente['Photo']?>" alt="<?php echo $vente['Nom']?>"></h3>
            <p>Prix: <?php echo $vente['Prix']?> €</p>
            <p>Quantité: <?php echo $vente['Quantité']?></p>
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

  // Obtenez l'élément avec la classe 'tablinks' et cliquez dessus
  document.getElementsByClassName("tablinks")[0].click();
</script>
<script src="them-switch.js"></script>



</body>
</html>
