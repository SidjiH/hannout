<?php

$conn = mysqli_connect('localhost','root','','utilisateurs');

if (mysqli_connect_errno()) {
  echo "Pas reussi a se connecter" . mysqli_connect_error();
  exit();
}
