<?php

@include 'lien.php';

session_start();

if(!isset($_SESSION['vendeur_nom'])){
   header('location:login_form.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Vendeur</title>

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<div class="container">

   <div class="content">
      <h3>Bonjour Vendeur</h3>
      <h1>Bienvenue <span><?php echo $_SESSION['vendeur_nom'] ?></span></h1>
      <a href="logout.php" class="btn">logout</a>
   </div>

</div>

</body>
</html>