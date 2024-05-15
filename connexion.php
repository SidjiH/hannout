<?php
session_start();
require 'lien.php';
$conn = mysqli_connect('localhost','root','','test');
function handleLogin($conn) {
  if(!isset($_POST['submit'])) return;

  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $mdp = $_POST['mdp']; 
  $select = "SELECT * FROM utilisateur WHERE email = '$email'";
  $result = mysqli_query($conn, $select);

  if(mysqli_num_rows($result) <= 0){
    $GLOBALS['error'][] = 'Email ou mot de passe Incorrect';
    return;
  }

  $row = mysqli_fetch_array($result);
  $_SESSION['email'] = $row['email'];
  $_SESSION['row'] = $row;
  if(!password_verify($mdp, $row['mdp'])){ 
    $GLOBALS['error'][] = 'Email ou mot de passe Incorrect';
    return;
  }

  if($row['type_uti'] === 'vendeur'){
    $_SESSION['id'] = $row['id'];
    $_SESSION['vendeur_nom'] = $row['nom'];
    header('location:index.php');
  } else if($row['type_uti'] === 'acheteur') {
    $_SESSION['acheteur_nom'] = $row['nom'];
    $_SESSION['id'] = $row['id'];
    header('location:index.php');
  } else if($row['type_uti'] === 'admin') {
    $_SESSION['admin_nom'] = $row['nom'];
    $_SESSION['id'] = $row['id'];
    header('location:index.php'); // Rediriger vers la page admin
  }
}

handleLogin($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion</title>

  <link rel="stylesheet" href="connexion.css">

</head>
<style>

 
</style>
<body>
<ul class="menu">
  <li><a href="index.php#home">Accueil</a></li>
  <li><a href="validation.php">Panier</a></li>
  <li class="dropdown">
    <a href="#" class="dropbtn">Profil</a>
    <div class="dropdown-content">
      <?php
      if (isset($_SESSION['acheteur_nom']) || isset($_SESSION['vendeur_nom'])) {
        echo '<a href="profil.php">Profil</a>';
        echo '<a href="deconnexion.php">Déconnexion</a>';
      } else {
        echo '<a href="connexion.php">Connexion</a>';
        echo '<a href="inscription.php">Inscription</a>';
      }
      ?>
    </div>
  </li>
</ul>

<div class="form-container">
  <form action="" method="post">
    <h3>Bonjour</h3>
    <?php
    if(isset($error)){
      foreach($error as $err){
        echo '<span class="error-msg">'.$err.'</span>';
      };
    };
    ?>
    <input type="email" name="email" required placeholder="Email">
    <input type="password" name="mdp" required placeholder="Mot de passe">
    <input type="submit" name="submit" value="Connexion" class="form-btn">
    <p>Pas de compte ? <a href="inscription.php">inscription</a></p>
  </form>
</div>

</body>
</html>
