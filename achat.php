<?php
session_start();

// Connexion à la base de données
$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');

// Récupération de l'ID de l'utilisateur
$userId = $_SESSION['id'];

// Récupération de l'ID du panier de l'utilisateur
$stmt = $db->prepare("SELECT id FROM panier WHERE Utilisateur_ID = :userId");
$stmt->execute(['userId' => $userId]);
$panier = $stmt->fetch(PDO::FETCH_ASSOC);
$panierId = $panier['id'];

// Récupération des articles dans le panier
$stmt = $db->prepare("SELECT * FROM panier_article WHERE Panier_ID = :panierId");
$stmt->execute(['panierId' => $panierId]);

$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($articles as &$article) {
  $stmt = $db->prepare("SELECT * FROM articles WHERE id = :articleId");
  $stmt->execute(['articleId' => $article['Article_ID']]);
  $articleDetails = $stmt->fetch(PDO::FETCH_ASSOC);

  // Fusionner les détails de l'article avec les informations de panier_article
  $article = array_merge($article, $articleDetails);
}

// Calcul du prix total
$totalPrice = 0;
foreach ($articles as $article) {
  $totalPrice += $article['Prix'] * $article['Quantité'];
}

// Création de la commande
$stmt = $db->prepare("INSERT INTO commande (Utilisateur_ID, date_commande, Prix_Total) VALUES (:userId, NOW(), :totalPrice)");
$stmt->execute(['userId' => $userId, 'totalPrice' => $totalPrice]);

// Récupération de l'ID de la commande
$commandeId = $db->lastInsertId();

// Association de chaque article à la commande
foreach ($articles as $article) {
  $stmt = $db->prepare("INSERT INTO achats (id_article, Commande_ID, Vendeur_id, Nom, Déscription, Prix, Quantité, Vendeur, Photo) VALUES (:articleId, :commandeId, :vendeurId, :nom, :description, :prix, :quantite, :vendeur, :photo)");
  $stmt->execute([
    'articleId' => $article['id'],
    'commandeId' => $commandeId,
    'vendeurId' => $article['Vendeur_id'],
    'nom' => $article['Nom'],
    'description' => $article['Déscription'],
    'prix' => $article['Prix'],
    'quantite' => $article['Quantité'],
    'vendeur' => $article['Vendeur'],
    'photo' => $article['Photo']
  ]);
  $stmt = $db->prepare("UPDATE articles SET Stock = Stock - :quantite WHERE id = :articleId");
  $stmt->execute([
    'quantite' => $article['Quantité'],
    'articleId' => $article['id']
  ]);
}

// Suppression des articles du panier
$stmt = $db->prepare("DELETE FROM panier_article WHERE Panier_ID = :panierId");
$stmt->execute(['panierId' => $panierId]);

// Redirection vers la page de confirmation
header('Location: validation.php');
exit();
?>
