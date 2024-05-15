<?php

@include 'lien.php';

session_start();

if(!isset($_SESSION['acheteur_nom'])){
   header('location:login_form.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Acheteur</title>

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<div class="container">

   <div class="content">
      <h3>Bonjour</h3>
      <h1>Bienvenue <span><?php echo $_SESSION['acheteur_nom'] ?></span></h1>
      <a href="deconnexion.php" class="btn">Se déconnecter</a>
   </div>

</div>

</body>
</html>