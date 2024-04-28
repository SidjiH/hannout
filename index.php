<!DOCTYPE html>
<html>
<head>
  <title>Mon site Web en PHP</title>
  <style>
    .menu {
      display: flex;
      justify-content: space-between;
      list-style-type: none;
      margin: 0;
      padding: 0;
      background-color: #333;
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

    /* Styles pour les articles */
    .article {
      margin: 20px 0;
      padding: 20px;
      border: 1px solid #333;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .article h2 {
      margin-top: 0;
    }

    .article img {
      max-width: 100%;
      height: auto;
    }
    
  </style>
</head>
<body>
<ul class="menu">
  <li><a href="index.php#home">Accueil</a></li>
  <li><a href="contact.php">Contact</a></li>
  <li class="dropdown">
    <a href="#" class="dropbtn">Profil</a>
    <div class="dropdown-content">
      <a href="connexion.php">Connexion</a>
      <a href="inscription.php">Inscription</a>
      <a href="profil.php">Profil</a>
    </div>
  </li>
</ul>

<h1>Bienvenue sur mon site Web en PHP</h1>

<!-- Formulaire de recherche -->
<form action="index.php" method="get">
  <input type="text" name="search" placeholder="Rechercher...">
  <input type="submit" value="Rechercher">
</form>

<?php
// Connexion à la base de données
$db = new PDO('mysql:host=localhost;dbname=mydb', 'root', '');

// Récupération de la valeur de recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Préparation de la requête SQL
$stmt = $db->prepare("SELECT * FROM articles WHERE Nom LIKE :search OR Déscription LIKE :search ORDER BY Prix DESC LIMIT 10");

// Exécution de la requête SQL avec la valeur de recherche
$stmt->execute([
  'search' => '%' . $search . '%'
]);

// Affichage des articles
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  echo "<div class='article'>";
  echo "<h2>" . htmlspecialchars($row['Nom']) . "</h2>";
  echo "<p>" . htmlspecialchars($row['Déscription']) . "</p>";
  echo "<p>Prix : " . htmlspecialchars($row['Prix']) . "€</p>";
  echo "<img src='" . htmlspecialchars($row['Photo']) . "' alt='Image de l\'article'>";
  echo "</div>";
}
?>
</body>
</html>
