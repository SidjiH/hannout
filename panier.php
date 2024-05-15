<?php
session_start();
echo '<pre>';
var_dump($_POST);
echo '</pre>';


require 'lien.php'; // Assurez-vous que ce fichier établit une connexion à votre base de données

if (!isset($_SESSION['id'])) {
  // Redirigez l'utilisateur vers la page de connexion s'il n'est pas connecté
  header('Location: connexion.php');
  exit();
}

$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');


$userId = $_SESSION['id'];
$articleId = isset($_POST['Article_ID']) ? $_POST['Article_ID'] : null;
$quantity = isset($_POST['Quantité']) ? $_POST['Quantité'] : null;

$stmt = $db->prepare("SELECT Prix FROM articles WHERE id = :articleId");
$stmt->execute(['articleId' => $articleId]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
$price = $article ? $article['Prix'] : null;

if ($articleId === null || $quantity === null) {
  // Redirigez l'utilisateur vers la page précédente ou affichez un message d'erreur
  //header('Location: index.php');
  exit();
}



// Vérifiez si un panier existe déjà pour cet utilisateur
$stmt = $db->prepare("SELECT id FROM panier WHERE Utilisateur_ID = :userId");
$stmt->execute(['userId' => $userId]);
$panier = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$panier) {
  // Créez un nouveau panier si aucun n'existe
  $stmt = $db->prepare("INSERT INTO panier (Utilisateur_ID) VALUES (:userId)");
  $stmt->execute(['userId' => $userId]);
  $panierId = $db->lastInsertId();
} else {
  $panierId = $panier['id'];
}

// Vérifiez si l'article est déjà dans le panier
$stmt = $db->prepare("SELECT Quantité FROM panier_article WHERE Panier_ID = :panierId AND Article_ID = :articleId");
$stmt->execute(['panierId' => $panierId, 'articleId' => $articleId]);
$panierArticle = $stmt->fetch(PDO::FETCH_ASSOC);
$oldQuantity = $panierArticle['Quantité'];

$newQuantity = $oldQuantity + $quantity;
$newPrice = $newQuantity * $price;

$stmt = $db->prepare("UPDATE panier_article SET Quantité = :newQuantity, Prix = :newPrice WHERE Panier_ID = :panierId AND Article_ID = :articleId");
$stmt->execute(['newQuantity' => $newQuantity, 'newPrice' => $newPrice, 'panierId' => $panierId, 'articleId' => $articleId]);

if($panierArticle) {
  // Si l'article est déjà dans le panier, augmentez simplement la quantité
  $stmt = $db->prepare("UPDATE panier_article SET Quantité = Quantité + :quantity WHERE Panier_ID = :panierId AND Article_ID = :articleId");
  $stmt->execute(['quantity' => $quantity, 'panierId' => $panierId, 'articleId' => $articleId]);
} else {
  // Sinon, ajoutez un nouvel enregistrement dans la table panier_article
  $stmt = $db->prepare("INSERT INTO panier_article (Panier_ID, Article_ID, Quantité, Prix) VALUES (:panierId, :articleId, :quantity, :price)");
  $stmt->execute(['panierId' => $panierId, 'articleId' => $articleId, 'quantity' => $quantity, 'price' => $price]);
}

header('Location: panier.php');
exit();
?>
