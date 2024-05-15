<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Validation du panier</title>
  <link rel="stylesheet" href="validation.css">

  <style>
   
  </style>
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
<div class="container">
<?php

$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');

if (!isset($_SESSION['id'])) {
  header('Location: connexion.php');
  exit();
}

$userId = $_SESSION['id'];

$stmt = $db->prepare("SELECT id FROM panier WHERE Utilisateur_ID = :userId");
$stmt->execute(['userId' => $userId]);
$panier = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$panier) {
  echo "Votre panier est vide.";
  exit();
}

$panierId = $panier['id'];


$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');


$stmt = $db->prepare("SELECT * FROM panier_article WHERE Panier_ID = :panierId");
$stmt->execute(['panierId' => $panierId]);

function retirer($panierId, $articleId){
  $db = new PDO('mysql:host=localhost;dbname=test', 'root', '');
  $delete = $db->prepare("DELETE FROM panier_article WHERE Panier_ID = :panierId AND Article_ID = :articleId");
  $delete->execute(['panierId' => $panierId, 'articleId' => $articleId]);

}
$somme = 0;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
  $stmtArticle = $db->prepare("SELECT * FROM articles WHERE id = :articleId");
  $stmtArticle->execute(['articleId' => $row['Article_ID']]);
  $article = $stmtArticle->fetch(PDO::FETCH_ASSOC);
  $somme += $article['Prix'] * $row['Quantité'];
  echo "<div class='cart-item'>";
  echo "<img src='" . htmlspecialchars($article['Photo']) . "' alt='Image de l\'article'>";
  echo "<div class='cart-item-info'>";
  echo "<p class='cart-item-title'>" . $article['Nom'] . "</p>";
  echo "<p class='cart-item-description'>" . $article['Déscription'] . "</p>";
  echo "<p class='cart-item-price'>Prix: " . $article['Prix'] . "€</p>";
  echo "<input class='cart-item-quantity' value ='" . $row['Quantité'] ."' type='number' min='1' max='" . $article['Stock'] . "'> . </input> "; echo "</div>";
  echo "<button class='retirer-button' data-article-id='" . $row['Article_ID'] . "'>Retirer du panier</button>";
  echo "</div>";
}
?>
  <a href="achat.php" class="validate-button">Valider le panier</a>
  <p>Total: <?php echo $somme; ?>€</p>
</div>
<script>
  const retirerButtons = document.querySelectorAll('.retirer-button');
  retirerButtons.forEach(button => {
    button.addEventListener('click', function() {
      const articleId = this.getAttribute('data-article-id');

      fetch('retirer.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'Article_ID=' + encodeURIComponent(articleId),
      })
        .then(response => response.text())
        .then(data => {
          console.log(data);
          location.reload();
        })
        .catch((error) => {
          console.error('Error:', error);
        });
    });
  });
</script>
</body>
</html>
</body>
</html>
