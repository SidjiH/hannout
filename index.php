<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Accueil</title>
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="themes.css">
</head>
<body>
<a href="index.php">
  <img class="logo" src="img/Logo.png" alt="Logo Hannout">
</a>
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

<label class="switch">
  <input type="checkbox" id="theme-switch">
  <span class="slider round"></span>
</label>

<label class="recherche">
  <form action="index.php" method="get">
    <input type="text" name="search" placeholder="Rechercher...">
    <input type="submit" value="Rechercher">
  </form>
</label>


<?php

$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');


$search = isset($_GET['search']) ? $_GET['search'] : '';


$stmt = $db->prepare("SELECT * FROM articles WHERE Nom LIKE :search OR Déscription LIKE :search OR Vendeur LIKE :search ORDER BY Prix DESC LIMIT 100");


$stmt->execute([
  'search' => '%' . $search . '%'
]);

echo "<div class='articles'>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  echo "<div class='article'>";
  $nom = urlencode(htmlspecialchars($row['Nom']));
  echo "<a href='article.php?nom=" . $nom . "' style='text-decoration: none; color: inherit;'>";
  echo "<div class='info'>";
  echo "<h2>" . htmlspecialchars($row['Nom']) . "</h2>";
  echo "<p>" . htmlspecialchars($row['Déscription']) . "</p>";
  echo "<p>Prix : " . htmlspecialchars($row['Prix']) . "€</p>";
  echo "</div>";
  echo "<img src='" . htmlspecialchars($row['Photo']) . "' alt='Image de l\'article'>";
  echo "</div>";
  echo "</a>";
}
?>
</div>
<script src="them-switch.js"></script>
</body>
</html>
