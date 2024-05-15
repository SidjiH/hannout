<?php
session_start();
// Connexion à la base de données
$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');

// on verifie si l'utilisateur est connecté et est un vendeur
if(isset($_SESSION['id']) && $_SESSION['row']['type_uti'] === 'vendeur'){
  // Récupérez l'ID de l'article à partir de la requête AJAX
  $articleId = $_POST['article_id'];

  // Préparation de la requête SQL pour augmenter le stock
  $stmt = $db->prepare("UPDATE articles SET stock = stock + 1 WHERE id = :articleId AND vendeur_id = :vendeurId");

  // Exécution de la requête SQL
  $stmt->execute([
    'articleId' => $articleId,
    'vendeurId' => $_SESSION['id']
  ]);

  // Vérification de si la requête a réussi
  if($stmt->rowCount() > 0){
    echo "Le stock a été augmenté avec succès.";
  } else {
    echo "Erreur : vous n'êtes pas le vendeur de cet article ou l'article n'existe pas.";
  }
} else {
  echo "Erreur : vous devez être connecté en tant que vendeur pour augmenter le stock.";
}
?>
