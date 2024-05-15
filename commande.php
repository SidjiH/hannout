<?php
session_start();

// Connexion à la base de données
$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');
// Récupération de l'ID de la commande à partir de l'URL
$id = isset($_GET['id']) ? $_GET['id'] : '';
// Préparation de la requête SQL
$stmt = $db->prepare("SELECT * FROM commande WHERE id = :id");
// Exécution de la requête SQL avec l'ID de la commande
$stmt->execute([
  'id' => $id
]);
// Récupération des détails de la commande
$commande = $stmt->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vendeur</title>

  <link rel="stylesheet" href="css/commande.css">
  <link rel="stylesheet" href="css/themes.css">

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

      ?>
    </div>
  </li>
</ul>




<?php
function getArticles($db, $commandeId) {
  // Préparation de la requête SQL
  $stmt = $db->prepare("SELECT * FROM achats WHERE Commande_ID = :commandeId");
  // Exécution de la requête SQL avec l'ID de la commande
  $stmt->execute([
    'commandeId' => $commandeId
  ]);
  // Récupération des articles associés à la commande
  return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$articles = getArticles($db, $id);

// Affichage des détails de la commande
if ($commande) {
  echo "<h1>Commande n°" . htmlspecialchars($commande['id']) . "</h1>";
  echo "<h3>Date : " . htmlspecialchars($commande['date_commande']) . "</h3>";
  echo "<h2>Articles:</h2>";
  echo "<ul>";
  foreach ($articles as $article) {
    echo "<div class='article'>";
    echo "<h3>" . htmlspecialchars($article['Nom']) . "</h3>";
    echo "<p>" . htmlspecialchars($article['Déscription']) . "</p>";
    echo "<p>Prix : " . htmlspecialchars($article['Prix']) . "€</p>";
    echo "<p>Quantité : " . htmlspecialchars($article['Quantité']) . "</p>";
    echo "<p>Vendeur : " . htmlspecialchars($article['Vendeur']) . "</p>";
    echo "<img src='" . htmlspecialchars($article['Photo']) . "' alt='Image de l\'article'>";
    echo "</div>";
  }
  echo "<h1>Prix total : " . htmlspecialchars($commande['Prix_Total']) . "€</h1>";
} else {
  echo "<p>Cette commande n'existe pas.</p>";
}
?>
<script src="js/them-switch.js"></script>

</body>
</html>

