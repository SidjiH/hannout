<?php
session_start();
// Vérifier si le formulaire est soumis
if(isset($_POST["submit"])) {
  // Obtenez les données du formulaire

  $name = $_POST['name'];
  $description = $_POST['description'];
  $price = $_POST['price'];
  $stock = $_POST['stock'];
  $date = date('Y-m-d H:i:s');
  $vendor = $_SESSION['vendeur_nom'];
  $reduction = $_POST['reduction'];
  $note = $_POST['note'];

  // Obtener les informations du fichier
  $fileName = $_FILES['file']['name'];
  $fileTmpName = $_FILES['file']['tmp_name'];
  $fileSize = $_FILES['file']['size'];
  $fileError = $_FILES['file']['error'];
  $fileType = $_FILES['file']['type'];

  // Vérifier si le fichier est une image
  $fileExt = explode('.', $fileName);
  $fileActualExt = strtolower(end($fileExt));
  $allowed = array('jpg', 'jpeg', 'png', 'svg');

  if(in_array($fileActualExt, $allowed)) {
    if($fileError === 0) {
      if($fileSize < 500000) { // Taille du fichier inférieure à 500KB
        // Créer un nom de fichier unique
        $fileNameNew = $vendor."_".$name.".".$fileActualExt;
        $fileDestination = 'img/'.$fileNameNew;
        move_uploaded_file($fileTmpName, $fileDestination);

        // Insértion des données dans la base de données
        // Connection à la base de données
        $db = new PDO('mysql:host=localhost;dbname=test', 'root', '');
        $sql = "INSERT INTO articles (Vendeur_id, Nom, Déscription, Prix, Stock, Vendeur, Date_ajout, Reduction, Note, Photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $date = date('Y-m-d H:i:s'); // Obtenez la date et l'heure actuelles
        $stmt->execute([$_SESSION['id'],$name, $description, $price, $stock, $vendor, $date, $reduction, $note, $fileDestination]);

        header("Location: ajouter_article.php?uploadsuccess");
      } else {
        echo "Votre fichier est trop grand !";
      }
    } else {
      echo "Il y a eu une erreur lors du téléchargement de votre fichier !";
    }
  } else {
    echo "Vous ne pouvez pas télécharger des fichiers de ce type !";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Ajouter un article</title>
  <style>
    /* Styles existants */
    .menu {
      display: flex;
      justify-content: space-between;
      list-style-type: none;
      margin: 0;
      padding: 0;
      background-color: #333;
    }
    .logo {
      width: 100px;
      height: auto;
    }

    .menu li {
      display: inline;
    }

    .menu li a {
      display: block;
      color: #938a8a;
      text-align: center;
      padding: 14px 16px;
      text-decoration: none;
    }

    .menu li a:hover {
      background-color: #111;
    }

    .dropdown {
      position: relative;
      display: inline-block;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      right: 0;
      background-color: #f9f9f9;
      min-width: 160px;
      box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
      z-index: 1;
    }

    .dropdown-content a {
      color: black;
      padding: 12px 16px;
      text-decoration: none;
      display: block;
      text-align: left;
    }

    .dropdown-content a:hover {background-color: #f1f1f1}

    .dropdown:hover .dropdown-content {
      display: block;
    }
    /* Styles pour la barre de recherche */
    form {
      margin-bottom: 20px;
    }

    input[type="text"] {
      padding: 10px;
      border: none;
      border-radius: 5px;
      width: 80%;
    }

    input[type="submit"] {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      background-color: #333;
      color: white;
      cursor: pointer;
    }
  </style>
  <link rel="stylesheet" href="css/index.css">
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

<form action="" method="post" enctype="multipart/form-data">
  <label for="name">Nom:</label><br>
  <input type="text" id="name" name="name"><br>
  <label for="description">Description:</label><br>
  <input type="text" id="description" name="description"><br>
  <label for="price">Prix:</label><br>
  <input type="text" id="price" name="price"><br>
  <label for="stock">Stock:</label><br>
  <input type="text" id="stock" name="stock"><br>
  <label for="reduction">Réduction:</label><br>
  <input type="text" id="reduction" name="reduction"><br>
  <label for="note">Note:</label><br>
  <input type="text" id="note" name="note"><br>
  <label for="file">Sélectionner une image:</label><br>
  <input type="file" id="file" name="file"><br>
  <input type="submit" value="Ajouter un article" name="submit">
</form>
</body>
</html>
