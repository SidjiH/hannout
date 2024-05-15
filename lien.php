<?php

$conn = mysqli_connect('localhost','root','','test');
if (mysqli_connect_error()) {
  echo "Pas reussi a se connecter" . mysqli_connect_error();
  exit();
}
