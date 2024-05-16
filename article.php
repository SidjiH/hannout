<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <link rel="stylesheet" href="css/article.css">
  <link rel="stylesheet" href="css/themes.css">
</head>

<body>


<ul class="menu">
  <a href="index.php">
    <img class="logo" src="img/Logo.png" alt="Logo Hannout">
  </a>
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

<div class="main">

  <div class="article-details">
    <?php
    $db = new PDO('mysql:host=localhost;dbname=test', 'root', '');

    $nom = isset($_GET['nom']) ? urldecode($_GET['nom']) : '';

    $stmt = $db->prepare("SELECT * FROM articles WHERE Nom = :nom");

    $stmt->execute([
      'nom' => $nom
    ]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    $db = new PDO('mysql:host=localhost;dbname=test', 'root', '');

    $articleId = $article['id'];

    $userId = $_SESSION['id'];
    $stmt = $db->prepare("SELECT id FROM panier WHERE Utilisateur_ID = :userId");
    $stmt->execute(['userId' => $userId]);
    $panier = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($panier) {
      $panierId = $panier['id'];
    } else {
      $panierId = null;
    }

    $stmt = $db->prepare("SELECT Quantité FROM panier_article WHERE Panier_ID = :panierId AND Article_ID = :articleId");
    $stmt->execute(['panierId' => $panierId, 'articleId' => $articleId]);
    $articleInCart = $stmt->fetch(PDO::FETCH_ASSOC);
    $quantityInCart = $articleInCart ? $articleInCart['Quantité'] : 0;

    $maxQuantity = $article['Stock'] - $quantityInCart;


    if ($article) {
      echo "<h1>" . htmlspecialchars($article['Nom']) . "</h1>";
      echo "<img src='" . htmlspecialchars($article['Photo']) . "' alt='Image de l\'article'>";
      echo "<p>" . htmlspecialchars($article['Déscription']) . "</p>";
      echo "<p>Prix : " . htmlspecialchars($article['Prix']) . "€</p>";
      echo "<p>Stock : " . htmlspecialchars($article['Stock']) . "</p>";
      echo "<p>Vendeur : " . htmlspecialchars($article['Vendeur']) . "</p>";
      echo "<p>Date d'ajout : " . htmlspecialchars($article['Date_ajout']) . "</p>";
      echo "<p>Réduction : " . htmlspecialchars($article['Reduction']) . "%</p>";
      echo "<p>Note : " . htmlspecialchars($article['Note']) . "/5</p>";
    } else {
      echo "<p>Cet article n'existe pas.</p>";
    }


  ?>
  </div>


  <div class="sidebar">
    <div class="sidebar-item">
      <h2>Prix : <?php echo htmlspecialchars($article['Prix']); ?>€</h2>
    </div>
    <div class="sidebar-item">
      <p>Stock : <?php echo htmlspecialchars($article['Stock']); ?></p>
    </div>
    <div class="sidebar-item">
      <p>Réduction : <?php echo htmlspecialchars($article['Reduction']); ?>%</p>
    </div>
    <div class="sidebar-item">

      <input type="hidden" id="Article_ID" value="<?php echo $article['id'];?>">
      <input type="hidden" id="Prix" value="<?php echo $article['Prix'];?>">
      <input type="number" id="Quantité" min="0" max="<?php echo $maxQuantity;?>" value="0" > <button id="add-to-cart">Ajouter au panier</button>
      <span id="augmenter-stock" data-article-id="<?php echo $article['id']; ?>">Augmenter le stock</span>
      <span id="supprimer-article" data-article-id="<?php echo $article['id'];?>" data-vendeur-id="<?php echo $article['Vendeur_id']; ?>">Supprimer cet article.</span>
    </div>

    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const deleteButton = document.getElementById("supprimer-article");
        deleteButton.addEventListener("click", function() {
          const articleId = this.dataset.articleId;
          const vendeur_id = this.dataset.vendeurId;
          $.post("supprimer_article.php", { article_id: articleId, vendeur_id: vendeur_id }, function(data) {
            alert(data);
          });
        });
      });


      $(document).ready(function(){
        $("#augmenter-stock").click(function(){
          var articleId = $(this).data('article-id');
          $.ajax({
            url: 'augmenter_stock.php',
            type: 'post',
            data: {article_id: articleId},
            success: function(response){
              alert(response);
            }
          });
        });
      });

      document.getElementById('add-to-cart').addEventListener('click', function() {
        var articleId = document.getElementById('Article_ID').value;
        var quantity = document.getElementById('Quantité').value;


        fetch('panier.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'Article_ID=' + encodeURIComponent(articleId) + '&Quantité=' + encodeURIComponent(quantity),
        })
          .then(response => response.text())
          .then(data => console.log(data))
          .catch((error) => {
            console.error('Error:', error);
          });
      });
      $(document).ready(function(){
        $("#augmenter-stock").click(function(){
          var articleId = $(this).data('article-id');
          $.ajax({
            url: 'augmenter_stock.php',
            type: 'post',
            data: {article_id: articleId},
            success: function(response){
              alert(response);
            }
          });
        });
      });
    </script>
  </div>
  <div class="comment-section">
  <h2>Commentaires</h2>
<div class="ajouter-com">
  <?php
  if (isset($_SESSION['acheteur_nom']) || isset($_SESSION['vendeur_nom']) || isset($_SESSION['admin_nom'])) {
    echo "<h3>Ajouter un commentaire</h3>";
  } else {
    echo "<h3>Voir les commentaires</h3>";
  }
  ?>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
    <?php
      if (isset($_SESSION['acheteur_nom']) || isset($_SESSION['vendeur_nom']) || isset($_SESSION['admin_nom'])) {
        $auteur = isset($_SESSION['acheteur_nom']) ? $_SESSION['acheteur_nom'] : (isset($_SESSION['vendeur_nom']) ? $_SESSION['vendeur_nom'] : $_SESSION['admin_nom']);
        echo "<input type='hidden' name='auteur' value='" . $auteur . "'>";
      }
    ?>
    <textarea name="contenu" placeholder="Votre commentaire" required></textarea>
    <input type="file" name="photo">
    <input type="submit" value="Ajouter">
  </form>
</div>

<?php
$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');
$stmt = $db->prepare("SELECT * FROM commentaires WHERE Article_ID = :id");
$stmt->execute([
  'id' => $article['id']
]);

while ($commentaire = $stmt->fetch(PDO::FETCH_ASSOC)) {
  echo "<div class='comment'>";
  echo "<h3>" . htmlspecialchars($commentaire['Auteur']) . "</h3>";
  echo "<p>" . htmlspecialchars($commentaire['Contenu']) . "</p>";
  echo "<p class='comment-date'>" . htmlspecialchars($commentaire['Date_Commentaire']) . "</p>";
  if (!empty($commentaire['Photo'])) {
    echo "<img src='uploads/" . htmlspecialchars($commentaire['Photo']) . "' alt='Photo de " . htmlspecialchars($commentaire['Auteur']) . "'>";
  }
  echo "</div>";
}

if (isset($_SESSION['acheteur_nom']) || isset($_SESSION['vendeur_nom']) || isset($_SESSION['admin_nom'])) {
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO commentaires (Auteur, Contenu, Date_Commentaire, Article_ID, Photo) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $date = date('Y-m-d H:i:s');
    $photo = $_FILES['photo']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
      echo "Désolé, seuls les fichiers JPG, JPEG, PNG & GIF sont autorisés.";
    } else {
      move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file);
      $stmt->execute([
        $_POST['auteur'],
        $_POST['contenu'],
        $date,
        $_POST['article_id'],
        $photo
      ]);
    }
  }
}
?>
</div>
</div>

<script src="js/them-switch.js"></script>


</body>
</html>

