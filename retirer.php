<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Article_ID'])) {
  $db = new PDO('mysql:host=localhost;dbname=test', 'root', '');

  $userId = $_SESSION['id'];
  $stmt = $db->prepare("SELECT id FROM panier WHERE Utilisateur_ID = :userId");
  $stmt->execute(['userId' => $userId]);
  $panier = $stmt->fetch(PDO::FETCH_ASSOC);
  $panierId = $panier['id'];

  $articleId = $_POST['Article_ID'];

  $delete = $db->prepare("DELETE FROM panier_article WHERE Panier_ID = :panierId AND Article_ID = :articleId");
  $delete->execute(['panierId' => $panierId, 'articleId' => $articleId]);
}
?>
