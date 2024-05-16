<?php
session_start();
include 'lien.php';
$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');
function deleteArticle($articleId, $userId, $userType,$vendeur_id) {
  global $db;
  try {
        if ($userType == 'admin') {
            $query = $db->prepare("UPDATE articles SET Stock = 0 AND supprimé = 1 WHERE id = :articleId");
            $query->execute([':articleId' => $articleId]);
            echo "L'article a été supprimé avec succès par l'administrateur.";
        } else {
            $query = $db->prepare("SELECT 1 FROM articles WHERE id = :articleId AND vendeur_id = :userId");
            $query->execute([':articleId' => $articleId, ':userId' => $userId]);

            if ($query->rowCount() > 0) {
                $query = $db->prepare("UPDATE articles SET Stock = 0 AND supprimé = 1 WHERE id = :articleId");
                $query->execute([':articleId' => $articleId]);
                echo "L'article a été supprimé avec succès par le vendeur.";
            } else {
                echo "Échec de la suppression de l'article. Vous n'êtes pas le vendeur de cet article.";
            }
        }
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression de l'article: ". $e->getMessage();
    }
}
var_dump($_SESSION['id']);
var_dump($_SESSION['type_uti']);
var_dump($_POST['article_id']);
var_dump($_POST['vendeur_id']);

if (isset($_SESSION['id'], $_SESSION['type_uti'], $_POST['article_id']) && isset($_POST['vendeur_id'])) {
  $userId = $_SESSION['id'];
  $userType = $_SESSION['type_uti'];
  $articleId = $_POST['article_id'];
  $vendeurId = $_POST['vendeur_id'];

  deleteArticle($articleId, $userId, $userType, $vendeurId);
} else {
  // Gérer le cas où "vendeur_id" n'est pas défini
}
?>
