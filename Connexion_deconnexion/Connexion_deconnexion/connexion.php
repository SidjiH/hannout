<?php
require 'lien.php';

session_start();

function handleLogin($conn) {
    if(!isset($_POST['submit'])) return;

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $mdp = md5($_POST['mdp']);

    $select = " SELECT * FROM utilisateur WHERE email = '$email' && mdp = '$mdp' ";
    $result = mysqli_query($conn, $select);

    if(mysqli_num_rows($result) <= 0){
        $GLOBALS['error'][] = 'Email ou mot de passe Incorrect';
        return;
    }

    $row = mysqli_fetch_array($result);

    if($row['type_uti'] === 'vendeur'){
        $_SESSION['vendeur_nom'] = $row['nom'];
        header('location:page_vendeur.php');
    } else if($row['type_uti'] === 'acheteur') {
        $_SESSION['acheteur_nom'] = $row['nom'];
        header('location:page_acheteur.php');
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

   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   
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
