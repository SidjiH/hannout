<?php
session_start();
require_once 'lien.php';
$conn = mysqli_connect('localhost','root','','test');
$db = new PDO('mysql:host=localhost;dbname=test', 'root', '');
function registerUser($conn) {
    if(!isset($_POST['submit'])) return;

    $type_uti = $_POST['type_uti'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
    $cmdp = md5($_POST['cmdp']);
    $nom = mysqli_real_escape_string($conn, $_POST['nom']);

    $select = " SELECT * FROM utilisateur WHERE email = '$email' && mdp = '$mdp' ";
    $result = mysqli_query($conn, $select);

    if(mysqli_num_rows($result) > 0){
        $GLOBALS['error'][] = 'utilisateur deja existant';
        return;
    }

    if($_POST['mdp'] != $_POST['cmdp']){
      $GLOBALS['error'][] = 'Mot de passe pas identiques';
      return;
    }

    $insert = "INSERT INTO utilisateur (nom, email, mdp, type_uti)
    VALUES('$nom','$email','$mdp','$type_uti')";
    if(!mysqli_query($conn, $insert)){
        echo "Erreur: " . mysqli_error($conn);
        return;
    }

    header('location:connexion.php');
}

registerUser($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Inscription</title>

   <link rel="stylesheet" href="css/inscription.css">
    <link rel="stylesheet" href="css/themes.css">
  <link rel="stylesheet" href="css/index.css">
  <link rel="stylesheet"href="css/style.css" >


</head>
<body>



<ul class="menu">
  <li><a href="index.php#home">Accueil</a></li>
  <li><a href="contact.php">Contact</a></li>
  <li class="dropdown">
    <a href="#" class="dropbtn">Profil</a>
    <div class="dropdown-content">
      <a href="connexion.php">Connexion</a>
      <a href="inscription.php">Inscription</a>
      <a href="profil.php">Profil</a>
    </div>
  </li>
</ul>



<div class="form-container">
   <form action="" method="post">
      <h3>S'inscrire</h3>
      <?php
      if(isset($error)){
         foreach($error as $err){
            echo '<span class="error-msg">'.$err.'</span>';
         };
      };
      ?>
      <input type="text" name="nom" required placeholder="Enter votre nom">
      <input type="email" name="email" required placeholder="Entrez votre mail">
      <input type="password" name="mdp" required placeholder="Entrez votre mdp">
      <input type="password" name="cmdp" required placeholder="Confirmez votre mdp ">
      <select name="type_uti">
         <option value="acheteur">acheteur</option>
         <option value="vendeur">vendeur</option>
      </select>
      <input type="submit" name="submit" value="Inscription" class="form-btn">
      <p>Vous Avez déja un compte ? <a href="connexion.php">Se connecter</a></p>
   </form>
</div>

</body>
</html>
