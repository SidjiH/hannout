<?php
session_start();
include 'lien.php';
$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');
function deleteArticle($articleId, $userId, $userType) {
  global $db;
  try {
        if ($userType == 'admin') {
            $query = $db->prepare("DELETE FROM articles WHERE id = :articleId");
            $query->execute([':articleId' => $articleId]);
            echo "L'article a été supprimé avec succès par l'administrateur.";
        } else {
            $query = $db->prepare("SELECT 1 FROM articles WHERE id = :articleId AND vendeur_id = :userId");
            $query->execute([':articleId' => $articleId, ':userId' => $userId]);

            if ($query->rowCount() > 0) {
                $query = $db->prepare("DELETE FROM articles WHERE id = :articleId");
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


if (isset($_SESSION['id'], $_SESSION['type_uti'], $_POST['article_id'])) {
    $userId = $_SESSION['id'];
    $userType = $_SESSION['type_uti'];
    $articleId = $_POST['article_id'];

    deleteArticle($articleId, $userId, $userType);
} else {
    echo "Échec de la suppression de l'article. Vous devez être connecté en tant que vendeur de l'article ou administrateur.";
}
?>
